<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DueReportExport;
use App\Models\Shop;
use App\Models\BusStand;

class ReportController extends Controller
{
    /**
     * Display a listing of tenant reports with filters.
     */
    public function index(Request $request)
    {
        $query = Shop::with('busStand');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($s) use ($q) {
                $s->where('shop_owner_name', 'like', "%{$q}%")
                  ->orWhere('shop_owner_nic', 'like', "%{$q}%")
                  ->orWhere('shop_name', 'like', "%{$q}%")
                  ->orWhere('shop_number', 'like', "%{$q}%");
            });
        }

        if ($request->filled('shop_owner_name')) {
            $v = $request->input('shop_owner_name');
            $query->where(function($s) use ($v) {
                $s->where('shop_owner_name', 'like', '%'.$v.'%')
                  ->orWhere('shop_owner_nic', 'like', '%'.$v.'%');
            });
        }

        if ($request->filled('bus_stand_id')) {
            $query->where('bus_stand_id', $request->input('bus_stand_id'));
        }

        $tenantSummary = (clone $query)
            ->selectRaw('COUNT(*) as shop_count, COALESCE(SUM(due_payment), 0) as due_payment_total, COALESCE(SUM(due_interest), 0) as due_interest_total')
            ->first();
        $shopsWithOutstandingBalance = (clone $query)
            ->where(function ($shopsQuery) {
                $shopsQuery->where('due_payment', '>', 0)
                    ->orWhere('due_interest', '>', 0);
            })
            ->count();

        $tenantSummary->total_outstanding = (float) $tenantSummary->due_payment_total + (float) $tenantSummary->due_interest_total;
        $tenantSummary->shops_with_outstanding_balance = $shopsWithOutstandingBalance;
        $tenantSummary->due_payment_share = $tenantSummary->total_outstanding > 0
            ? ((float) $tenantSummary->due_payment_total / $tenantSummary->total_outstanding) * 100
            : 0;
        $tenantSummary->due_interest_share = $tenantSummary->total_outstanding > 0
            ? ((float) $tenantSummary->due_interest_total / $tenantSummary->total_outstanding) * 100
            : 0;

        $shops = $query->orderBy('shop_owner_name')->paginate(20)->withQueryString();

        $busStands = BusStand::orderBy('name')->get();

        return view('reports.tenants.index', compact('shops', 'busStands', 'tenantSummary'));
    }

    /**
     * Display the detailed information for a single tenant/shop.
     */
    public function show($id)
    {
        $shop = Shop::with('busStand', 'payments')->findOrFail($id);

        // Use stored fields where available, else compute simple fallbacks
        $computed = [];
        $computed['year_total_taken'] = $shop->year_total_taken ?? (
            ($shop->rent_january ?? 0) + ($shop->rent_february ?? 0) + ($shop->rent_march ?? 0) + ($shop->rent_april ?? 0) + ($shop->rent_may ?? 0) + ($shop->rent_june ?? 0) + ($shop->rent_july ?? 0) + ($shop->rent_august ?? 0) + ($shop->rent_september ?? 0) + ($shop->rent_october ?? 0) + ($shop->rent_november ?? 0) + ($shop->rent_december ?? 0)
        );

        $computed['total_paid'] = $shop->total_paid ?? $shop->payments->sum('amount');
        $computed['total_due'] = $shop->total_due ?? ($shop->year_charge ?? 0) - $computed['total_paid'];
        $computed['balance_amount'] = $shop->balance_amount ?? ($computed['total_due'] ?? 0);

        return view('reports.tenants.show', compact('shop', 'computed'));
    }

    /**
     * Display a listing of all shops with details.
     */
    public function shopsIndex(Request $request)
    {
        $query = Shop::with('busStand');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($s) use ($q) {
                $s->where('shop_owner_name', 'like', "%{$q}%")
                  ->orWhere('shop_number', 'like', "%{$q}%")
                  ->orWhere('shop_name', 'like', "%{$q}%");
            });
        }

        if ($request->filled('bus_stand_id')) {
            $query->where('bus_stand_id', $request->input('bus_stand_id'));
        }

        if ($request->filled('shop_id')) {
            $query->where('id', $request->input('shop_id'));
        }

        $shops = $query->orderBy('shop_number')->paginate(15)->withQueryString();

        $busStands = BusStand::orderBy('name')->get();
        $shopOptions = Shop::query();

        if ($request->filled('bus_stand_id')) {
            $shopOptions->where('bus_stand_id', $request->input('bus_stand_id'));
        }

        $shopOptions = $shopOptions->orderBy('shop_name')->get(['id', 'shop_name', 'shop_number']);

        return view('reports.shops.index', compact('shops', 'busStands', 'shopOptions'));
    }

    /**
     * Show edit form for a shop.
     */
    public function shopsShow($id)
    {
        $shop = Shop::with('busStand', 'payments')->findOrFail($id);

        $currentYear = now()->year;
        $paymentsThisYear = $shop->payments->filter(function ($payment) use ($currentYear) {
            return ($payment->year === $currentYear) || ($payment->paid_at && $payment->paid_at->year === $currentYear);
        });

        $computed = [];
        $computed['current_year_total_taken'] =
            (!empty($shop->year_total_taken) ? $shop->year_total_taken : $paymentsThisYear->sum('amount'))
            ?: (
                ($shop->rent_january ?? 0) + ($shop->rent_february ?? 0) + ($shop->rent_march ?? 0) + ($shop->rent_april ?? 0) + ($shop->rent_may ?? 0) + ($shop->rent_june ?? 0) + ($shop->rent_july ?? 0) + ($shop->rent_august ?? 0) + ($shop->rent_september ?? 0) + ($shop->rent_october ?? 0) + ($shop->rent_november ?? 0) + ($shop->rent_december ?? 0)
            );

        $computed['total_paid'] = $shop->total_paid ?? $shop->payments->sum('amount');
        $computed['total_due'] = $shop->total_due ?? (($shop->year_charge ?? 0) - $computed['total_paid']);
        $computed['balance_amount'] = $shop->balance_amount ?? $computed['total_due'];

        return view('reports.shops.show', compact('shop', 'computed'));
    }

    public function shopsEdit($id)
    {
        $shop = Shop::findOrFail($id);
        $busStands = BusStand::orderBy('name')->get();

        return view('reports.shops.edit', compact('shop', 'busStands'));
    }

    /**
     * Update shop details.
     */
    public function shopsUpdate(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_owner_name' => 'required|string|max:255',
            'shop_owner_nic' => 'required|string|max:20',
            'shop_owner_phone' => 'required|string|max:20',
            'shop_owner_address' => 'required|string|max:255',
            'monthly_rent' => 'required|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric',
            'agreement_duration' => 'nullable|integer|min:0',
            'bus_stand_id' => 'nullable|exists:bus_stands,id',
            'location' => 'nullable|string|max:255',
            'shop_address' => 'nullable|string|max:255',
            'agreement_start' => 'nullable|date',
            'agreement_end' => 'nullable|date',
            'owner_change_reason' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'image1_path' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'image2_path' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'image3_path' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'document_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:4096',
        ]);

        $oldOwnerData = [
            'shop_owner_name' => $shop->shop_owner_name,
            'shop_owner_nic' => $shop->shop_owner_nic,
            'shop_owner_phone' => $shop->shop_owner_phone,
            'shop_owner_address' => $shop->shop_owner_address,
            'shop_address' => $shop->shop_address,
            'agreement_start' => $shop->agreement_start,
            'agreement_end' => $shop->agreement_end,
        ];

        // handle file uploads and optional removal for stored files
        foreach (['image_path','image1_path','image2_path','image3_path','document_path'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($shop->$field) {
                    $this->deleteFile($shop->$field);
                }
                
                // Determine upload folder based on field type
                if ($field === 'document_path') {
                    // Store documents in public/PDF
                    $uploadFolder = 'images/PDF';
                    $fileName = 'doc_' . time() . '_' . $request->file($field)->getClientOriginalName();
                } else {
                    // Store images in public/images
                    $uploadFolder = 'images';
                    $fileName = 'shop_' . time() . '_' . $request->file($field)->getClientOriginalName();
                }
                
                // Store file directly in public folder
                $filePath = $request->file($field)->storeAs($uploadFolder, $fileName, 'public');
                $validated[$field] = $filePath;
            } elseif ($request->boolean("remove_{$field}")) {
                // Delete file if exists
                if ($shop->$field) {
                    $this->deleteFile($shop->$field);
                }
                $validated[$field] = null;
            }
        }

        $ownerChanged = (
            $validated['shop_owner_name'] !== $shop->shop_owner_name ||
            ($validated['shop_owner_nic'] ?? null) !== $shop->shop_owner_nic ||
            ($validated['shop_owner_phone'] ?? null) !== $shop->shop_owner_phone ||
            ($validated['shop_owner_address'] ?? null) !== $shop->shop_owner_address ||
            ($validated['shop_address'] ?? null) !== $shop->shop_address ||
            ($validated['agreement_start'] ?? null) != optional($shop->agreement_start)->format('Y-m-d') ||
            ($validated['agreement_end'] ?? null) != optional($shop->agreement_end)->format('Y-m-d')
        );

        if ($ownerChanged) {
            $shop->recordOwnerHistory($oldOwnerData, $validated['owner_change_reason'] ?? 'Owner reassigned');
        }

        $shop->update($validated);

        return redirect()->route('reports.shops.index')->with('success', 'Shop details updated successfully!');
    }

    /**
     * Delete a shop.
     */
    public function shopsDestroy($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();

        return redirect()->route('reports.shops.index')->with('success', 'Shop deleted successfully!');
    }

    /**
     * Display month-wise due balances for all shops from 2023 to current running month.
     */
    public function dueIndex(Request $request)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $allYears = range(2023, $currentYear);
        $selectedYear = $request->input('year');
        $selectedBusStandId = $request->input('bus_stand_id');
        $years = $selectedYear && in_array((int)$selectedYear, $allYears) ? [(int)$selectedYear] : $allYears;

        $monthsByYear = [];

        foreach ($years as $year) {
            if ($year === $currentYear) {
                $monthsByYear[$year] = range(1, $currentMonth);
            } else {
                $monthsByYear[$year] = range(1, 12);
            }
        }

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $query = Shop::with('busStand', 'payments');

        if ($selectedBusStandId) {
            $query->where('bus_stand_id', $selectedBusStandId);
        }

        if ($request->filled('shop_id')) {
            $query->where('id', $request->input('shop_id'));
        }

        // Apply search filter
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($s) use ($q) {
                $s->where('shop_owner_name', 'like', "%{$q}%")
                  ->orWhere('shop_owner_nic', 'like', "%{$q}%")
                  ->orWhere('shop_name', 'like', "%{$q}%")
                  ->orWhere('shop_number', 'like', "%{$q}%");
            });
        }

        $shops = $query->orderBy('shop_number')->get();
        $busStands = BusStand::orderBy('name')->get();
        $shopOptions = Shop::query();

        if ($selectedBusStandId) {
            $shopOptions->where('bus_stand_id', $selectedBusStandId);
        }

        $shopOptions = $shopOptions->orderBy('shop_name')->get(['id', 'shop_name', 'shop_number']);

        $dueData = [];

        foreach ($shops as $shop) {
            $shopData = [
                'shop' => $shop,
                'years' => []
            ];

            foreach ($years as $year) {
                $yearData = [
                    'year' => $year,
                    'months' => []
                ];

                $activeMonths = $monthsByYear[$year] ?? range(1, 12);

                foreach ($activeMonths as $monthNum) {
                    $monthName = $months[$monthNum] ?? $monthNum;

                    // Get the rent for this month (use monthly_rent as fallback when per-month rent is zero/null)
                    $monthNames = [
                        1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'june',
                        7 => 'july', 8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december'
                    ];
                    $rentField = 'rent_' . ($monthNames[$monthNum] ?? '');
                    $monthRentValue = $shop->$rentField;
                    $monthlyRent = ($monthRentValue > 0 ? $monthRentValue : ($shop->monthly_rent ?? 0));
                    $interestRate = $shop->rate ?? 0;

                    $payments = $shop->payments->where('year', $year)->where('month', $monthNum);
                    $totalPaid = $payments->sum('amount');
                    $totalInterest = $payments->sum('interest') ?? 0;
                    $totalPaidWithInterest = $totalPaid + $totalInterest;
                    $monthlyAmount = $monthlyRent + $totalInterest;
                    $monthlyBalance = $monthlyRent - $totalPaid;
                    $paidDate = $payments->sortByDesc('paid_at')->first()?->paid_at?->format('d-M-Y');
                    $due = max(0, $monthlyAmount - $totalPaid);
                    $status = $due > 0 ? 'Due' : ($totalPaid > 0 ? 'Paid' : 'Due');

                    $yearData['months'][$monthNum] = [
                        'month' => $monthName,
                        'rent' => $monthlyRent,
                        'rate' => $interestRate,
                        'balance' => max(0, $monthlyBalance),
                        'interest' => $totalInterest,
                        'monthly_amount' => $monthlyAmount,
                        'paid' => $totalPaid,
                        'paid_with_interest' => $totalPaidWithInterest,
                        'paid_date' => $paidDate,
                        'due' => $due,
                        'status' => $status,
                    ];
                }

                $shopData['years'][] = $yearData;
            }

            $shopData['summary'] = [
                'shop_number' => $shop->shop_number,
                'shop_name' => $shop->shop_name,
                'bus_stand' => $shop->busStand?->name ?? 'N/A',
                'owner_name' => $shop->shop_owner_name,
                'owner_nic' => $shop->shop_owner_nic,
                'monthly_rent' => $shop->monthly_rent ?? 0,
                'balance_amount' => $shop->balance_amount ?? $shop->due_payment ?? 0,
                'due_balance' => $shop->due_payment ?? 0,
                'due_interest' => $shop->due_interest ?? 0,
            ];

            $dueData[] = $shopData;
        }

        if ($request->query('export') === 'excel') {
            return Excel::download(
                new DueReportExport($dueData, [
                    'shop_id' => $request->input('shop_id'),
                    'year' => $selectedYear,
                    'query' => $request->input('q'),
                ]),
                'due_report_' . ($selectedYear ?: 'all') . '_' . now()->format('Ymd_His') . '.xlsx'
            );
        }

        return view('reports.due.index', compact('dueData', 'years', 'months', 'allYears', 'selectedYear', 'busStands', 'shopOptions', 'selectedBusStandId'));
    }

    /**
     * Delete a file from the public folder
     */
    private function deleteFile(?string $filePath)
    {
        if (empty($filePath)) {
            return;
        }

        // Remove any leading slashes
        $filePath = ltrim($filePath, '/');
        
        // Try to delete from storage (handles both public disk and direct paths)
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
