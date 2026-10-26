<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\DuePaymentsExport;
use App\Models\Payment;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboards.general');
    }

    public function manager()
    {
        $now = Carbon::now();
        $threeMonthsFromNow = $now->copy()->addMonths(3);
        
        // Shops with agreements that have already expired or will end within the
        // next 3 months.  Previously the query only returned future dates, so
        // the card title "Expired Agreements" was misleading and the day
        // calculation always treated entries as "remaining".  Including past
        // records allows us to show how many days have passed since expiration.
        $expired_shops = Shop::where('agreement_end', '<=', $threeMonthsFromNow)
            ->orderBy('agreement_end', 'asc')
            ->get();

        $currentYear = $now->year;
        $currentMonth = $now->month;
        $today = $now->day;

        // Calculate the tenant that has gone the longest without making a payment.
        // We fetch a max(`paid_at`) for each shop and then pick the one whose last payment
        // date is the oldest (or null for never paid). This is better aligned with
        // "long time not do the payment" requirement.
        $shops = Shop::withMax('payments', 'paid_at')->get();

        $revenueTrend = collect(range(5, 0))->map(function ($monthsAgo) use ($now) {
            $month = $now->copy()->startOfMonth()->subMonths($monthsAgo);
            $monthEnd = $month->copy()->endOfMonth()->min($now);

            return [
                'label' => $month->format('M'),
                'amount' => (float) Payment::whereBetween('paid_at', [$month, $monthEnd])->sum('amount'),
            ];
        })->values();

        $agreementStatusData = [
            [
                'label' => 'Active',
                'value' => Shop::whereNotNull('agreement_start')
                    ->whereNotNull('agreement_end')
                    ->whereDate('agreement_end', '>', $now->copy()->addMonths(3)->toDateString())
                    ->count(),
                'color' => '#2563eb',
            ],
            [
                'label' => 'Expiring soon',
                'value' => Shop::whereNotNull('agreement_start')
                    ->whereNotNull('agreement_end')
                    ->whereDate('agreement_end', '>=', $now->toDateString())
                    ->whereDate('agreement_end', '<=', $now->copy()->addMonths(3)->toDateString())
                    ->count(),
                'color' => '#f59e0b',
            ],
            [
                'label' => 'Expired',
                'value' => Shop::whereNotNull('agreement_start')
                    ->whereNotNull('agreement_end')
                    ->whereDate('agreement_end', '<', $now->toDateString())
                    ->count(),
                'color' => '#ef4444',
            ],
            [
                'label' => 'Agreement pending',
                'value' => Shop::whereNull('agreement_start')
                    ->orWhereNull('agreement_end')
                    ->count(),
                'color' => '#94a3b8',
            ],
        ];

        // shop with the earliest last payment timestamp (null will sort first)
        $topDefaulter = $shops->sortBy(function($shop) {
            // when payments_max_paid_at is null we want it to come first
            return $shop->payments_max_paid_at ? Carbon::parse($shop->payments_max_paid_at)->timestamp : 0;
        })->first();

        $topDefaulterData = null;
        if ($topDefaulter) {
            // compute months since last payment; if there was no payment, use created_at
            $lastPaid = $topDefaulter->payments_max_paid_at ? Carbon::parse($topDefaulter->payments_max_paid_at) : $topDefaulter->created_at;
            $monthsAgo = Carbon::now()->diffInMonths($lastPaid);

            $topDefaulterData = [
                'shop_name' => $topDefaulter->shop_name,
                'shop_number' => $topDefaulter->shop_number,
                'owner' => $topDefaulter->shop_owner_name,
                'last_paid_at' => $lastPaid ? $lastPaid->toDateString() : null,
                'months_since_last' => $monthsAgo,
                'total_due_payment' => $topDefaulter->due_payment ?? 0,
                'due_interest' => $topDefaulter->due_interest ?? 0,
            ];
        }

        // Shops that have not recorded a payment in the current month
        $dueShopsThisMonth = Shop::whereDoesntHave('payments', function($q) use ($currentYear, $currentMonth) {
            $q->whereYear('paid_at', $currentYear)->whereMonth('paid_at', $currentMonth);
        })->get();

        $dueCount = $dueShopsThisMonth->count();

        // If today is on or before 28th, prepare a short list of owner names and shop numbers
        $dueOwnersBefore28 = [];
        if ($today <= 28) {
            $dueOwnersBefore28 = $dueShopsThisMonth->map(function($s) {
                return [
                    'shop_name' => $s->shop_name,
                    'shop_number' => $s->shop_number,
                    'owner' => $s->shop_owner_name,
                ];
            })->take(10)->values();
        }

        // Income actually collected this month (based on payments.paid_at)
        $totalPaidThisMonth = Payment::whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->sum('amount');

        // For reference we still calculate expected income and outstanding amount,
        // but these are not included in the "current month income" value.
        $totalExpectedIncome = Shop::sum('monthly_rent');
        $totalDueThisMonth = max(0, $totalExpectedIncome - $totalPaidThisMonth);
        $collectionSurplusThisMonth = max(0, $totalPaidThisMonth - $totalExpectedIncome);
        $collectionProgressPercent = $totalExpectedIncome > 0
            ? min(100, ($totalPaidThisMonth / $totalExpectedIncome) * 100)
            : ($totalPaidThisMonth > 0 ? 100 : 0);

        // The dashboard card should show only what has been collected.
        $currentMonthEarnings = $totalPaidThisMonth;

        // Total registered shops
        $totalShops = Shop::count();
        // Count shops where agreement dates are not set (agreement not issued)
        $agreementNotIssuedCount = Shop::whereNull('agreement_start')
            ->orWhereNull('agreement_end')
            ->count();

        // Calculate total due payment and due interest from all shops
        $totalDuePaymentAllShops = Shop::sum('due_payment');
        $totalDueInterestAllShops = Shop::sum('due_interest');
        $totalDueBalanceAllShops = max(0, $totalDuePaymentAllShops + $totalDueInterestAllShops);

        return response()
            ->view('dashboards.manager', compact(
                'expired_shops',
                'topDefaulterData',
                'dueOwnersBefore28',
                'dueCount',
                'currentMonthEarnings',
                'totalPaidThisMonth',
                'totalDueThisMonth',
                'totalExpectedIncome',
                'collectionSurplusThisMonth',
                'collectionProgressPercent',
                'totalShops',
                'agreementNotIssuedCount',
                'revenueTrend',
                'agreementStatusData',
                'totalDuePaymentAllShops',
                'totalDueInterestAllShops',
                'totalDueBalanceAllShops'
            ), 200)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function duePayments(Request $request)
    {
        $now = Carbon::now();
        
        // Get date range filters from request
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $busStandId = $request->input('bus_stand_id');
        
        // Set default dates (current month)
        $fromDate = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : $now->copy()->startOfMonth();
        $toDate = $dateTo ? Carbon::parse($dateTo)->endOfDay() : $now->copy()->endOfMonth();

        $query = Shop::withMax('payments', 'paid_at')
            ->whereDoesntHave('payments', function($q) use ($fromDate, $toDate) {
                $q->whereBetween('paid_at', [$fromDate, $toDate]);
            });

        // Apply bus stand filter if provided
        if ($busStandId) {
            $query->where('bus_stand_id', $busStandId);
        }

        $dueShops = $query->orderBy('shop_number', 'asc')->get();

        $dueShops->transform(function ($shop) {
            $shop->last_payment_date = $shop->payments_max_paid_at ? Carbon::parse($shop->payments_max_paid_at)->format('Y-m-d') : 'No payments';
            $shop->due_payment = $shop->due_payment ?? 0;
            $shop->due_interest = $shop->due_interest ?? 0;
            return $shop;
        });

        $dueCount = $dueShops->count();

        // Calculate totals for due balance and interest
        $totalDueBalance = $dueShops->sum('due_payment');
        $totalDueInterest = $dueShops->sum('due_interest');
        $totalDueWithInterest = $totalDueBalance + $totalDueInterest;

        // Format display dates
        $displayDateFrom = $fromDate->format('Y-m-d');
        $displayDateTo = $toDate->format('Y-m-d');
        $displayRange = $fromDate->format('M d, Y') . ' - ' . $toDate->format('M d, Y');

        // Export requested? Create Excel from the current due list and preserve filters.
        if ($request->query('export') === 'excel') {
            return Excel::download(
                new DuePaymentsExport($dueShops, [
                    'date_from' => $displayDateFrom,
                    'date_to' => $displayDateTo,
                    'bus_stand_id' => $busStandId,
                ]),
                'due_payments_' . now()->format('Ymd_His') . '.xlsx'
            );
        }

        // Get all bus stands for the filter dropdown
        $busStands = \App\Models\BusStand::orderBy('name', 'asc')->get();

        return view('dashboards.due_payments', compact(
            'dueShops', 
            'dueCount', 
            'dateFrom',
            'dateTo',
            'busStandId',
            'busStands',
            'displayDateFrom',
            'displayDateTo',
            'displayRange',
            'totalDueBalance',
            'totalDueInterest',
            'totalDueWithInterest'
        ));
    }

    public function expiredAgreements()
    {
        $now = Carbon::now();
        $threeMonthsFromNow = $now->copy()->addMonths(3);

        $expired_shops = Shop::where('agreement_end', '<=', $threeMonthsFromNow)
            ->orderBy('agreement_end', 'asc')
            ->get();

        return view('dashboards.expired_agreements', compact('expired_shops'));
    }

    public function profile()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('dashboards.profile', compact('user'))->with('readonly', true);
    }

    public function editProfile()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('dashboards.profile', compact('user'))->with('readonly', false);
    }

    public function editPassword()
    {
        return view('dashboards.change-password');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Please log in to update your profile.']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $request->filled('phone') ? $data['phone'] : null,
        ]);

        $user->save();

        return redirect()->route('dashboard.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('dashboard.profile.show')->with('success', 'Password changed successfully.');
    }

    public function staff()
    {
        return view('dashboards.staff');
    }

    public function cashier()
    {
        return view('dashboards.cashier');
    }
}
