<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    public function index()
    {
        return view('payments', [
            'paymentEditModeEnabled' => $this->getPaymentEditModeSetting(),
        ]);
    }

    protected function getPaymentEditModeSetting()
    {
        if (!Storage::disk('local')->exists('payment-settings.json')) {
            return false;
        }

        $settings = json_decode(Storage::disk('local')->get('payment-settings.json'), true);
        return isset($settings['payment_edit_mode_enabled']) ? (bool) $settings['payment_edit_mode_enabled'] : false;
    }

    private function duePeriodStart(Shop $shop)
    {
        $agreementStart = Carbon::parse($shop->agreement_start)->startOfMonth();
        $minimumStart = Carbon::create(2023, 1, 1)->startOfMonth();

        return $agreementStart->gt($minimumStart) ? $agreementStart : $minimumStart;
    }

    private function duePeriodEnd(Shop $shop, bool $includeFuture = false)
    {
        $currentDate = now();
        $now = $currentDate->copy()->startOfMonth();
        $rentDueAt = $currentDate->copy()->day(28)->setTime(12, 0);

        if (!$includeFuture && !$currentDate->gt($rentDueAt)) {
            $now->subMonth();
        }

        if ($shop->agreement_end) {
            $agreementEnd = Carbon::parse($shop->agreement_end)->startOfMonth();
            if ($includeFuture) {
                return $agreementEnd->gt($now) ? $agreementEnd : $now;
            }
            return $agreementEnd->lt($now) ? $agreementEnd : $now;
        }

        return $now;
    }

    private function getUnpaidMonths(Shop $shop)
    {
        if (!$shop->monthly_rent || !$shop->agreement_start) {
            return [];
        }

        try {
            $monthlyRent = floatval($shop->monthly_rent);
            $rate = floatval($shop->rate ?: 0);
            $now = now();
            $startDate = $this->duePeriodStart($shop);
            $endDate = $this->duePeriodEnd($shop, false);

            // Get all paid months in one query
            $paidPayments = Payment::where('shop_id', $shop->id)
                ->where('monthly_payment', 1)
                ->where('year', '>=', $startDate->year)
                ->where('year', '<=', $endDate->year)
                ->get(['year', 'month']);

            $paidMonths = [];
            foreach ($paidPayments as $payment) {
                $paidMonths[$payment->year][] = $payment->month;
            }

            $unpaidMonths = [];
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $year = $current->year;
                $month = $current->month;

                if (!isset($paidMonths[$year]) || !in_array($month, $paidMonths[$year])) {
                    $monthName = $current->format('F Y');
                    $dueRent = $monthlyRent;
                    $monthEnd = $current->copy()->endOfMonth();
                    $dueInterest = 0;

                    $currentMonthRentIsDue = $now->gt($now->copy()->day(28)->setTime(12, 0));
                    if ($monthEnd->lt($now) || ($current->isCurrentMonth() && $currentMonthRentIsDue)) {
                        $dueInterest = $monthlyRent * ($rate / 100);
                    }

                    $unpaidMonths[] = [
                        'month' => $month,
                        'year' => $year,
                        'month_name' => $monthName,
                        'due_rent' => round($dueRent, 2),
                        'due_interest' => round($dueInterest, 2),
                        'total_due' => round($dueRent + $dueInterest, 2),
                    ];
                }

                $current->addMonth();
            }

            return $unpaidMonths;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function computeDueFromLastPayment(Shop $shop)
    {
        if (!$shop->monthly_rent || !$shop->agreement_start) {
            return [
                'due_payment' => floatval($shop->due_payment ?: 0),
                'due_interest' => floatval($shop->due_interest ?: 0),
                'due_months' => 0,
                'interest_months' => 0,
            ];
        }

        try {
            $monthlyRent = floatval($shop->monthly_rent);
            $rate = floatval($shop->rate ?: 0);
            $now = now();
            $startDate = $this->duePeriodStart($shop);
            $endDate = $this->duePeriodEnd($shop, false);

            $paidPayments = Payment::where('shop_id', $shop->id)
                ->where('monthly_payment', 1)
                ->where('year', '>=', $startDate->year)
                ->where('year', '<=', $endDate->year)
                ->get(['year', 'month']);

            $paidMonths = [];
            foreach ($paidPayments as $payment) {
                $paidMonths[$payment->year][] = $payment->month;
            }

            $duePayment = 0;
            $dueInterest = 0;
            $dueMonths = 0;
            $interestMonths = 0;

            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $year = $current->year;
                $month = $current->month;

                if (!isset($paidMonths[$year]) || !in_array($month, $paidMonths[$year])) {
                    $dueMonths++;
                    $duePayment += $monthlyRent;
                    $monthEnd = $current->copy()->endOfMonth();

                    $currentMonthRentIsDue = $now->gt($now->copy()->day(28)->setTime(12, 0));
                    if ($monthEnd->lt($now) || ($current->isCurrentMonth() && $currentMonthRentIsDue)) {
                        $interestMonths++;
                        $dueInterest += $monthlyRent * ($rate / 100);
                    }
                }

                $current->addMonth();
            }

            return [
                'due_payment' => round($duePayment, 2),
                'due_interest' => round($dueInterest, 2),
                'due_months' => $dueMonths,
                'interest_months' => $interestMonths,
            ];
        } catch (\Exception $e) {
            return [
                'due_payment' => floatval($shop->due_payment ?: 0),
                'due_interest' => floatval($shop->due_interest ?: 0),
                'due_months' => 0,
                'interest_months' => 0,
            ];
        }
    }

    public function search(Request $request)
    {
        $q = $request->query('q');

        if (!$q) {
            return response()->json([], 404);
        }

        $shops = Shop::with('busStand')
            ->where(function($query) use ($q) {
                $query->where('shop_owner_name', 'LIKE', "%{$q}%")
                      ->orWhere('shop_number', 'LIKE', "%{$q}%")
                      ->orWhere('shop_owner_nic', 'LIKE', "%{$q}%");
            })
            ->get();

        if ($shops->isEmpty()) {
            return response()->json([], 404);
        }

        $shops->transform(function ($shop) {
            $today = now();

            // Commented out auto-charge, now handled in record method
            /*
            // auto‑charge monthly rent once per calendar month on first lookup
            $firstOfMonth = $today->copy()->startOfMonth();
            if (!$shop->last_rent_charged || $shop->last_rent_charged->lt($firstOfMonth)) {
                // calculate how many months we should charge (normally 1)
                $months = 1;
                if ($shop->last_rent_charged) {
                    $months += $shop->last_rent_charged->diffInMonths($firstOfMonth);
                }
                $shop->due_payment = ($shop->due_payment ?? 0) + ($shop->monthly_rent * $months);
                $shop->last_rent_charged = $firstOfMonth;
                // do not fail search if save fails but persist when possible
                try {
                    $shop->save();
                } catch (\Exception $e) {
                    // ignore
                }
            }
            */

            // 1. Calculate Accumulated Total Paid from payment_details table
            $totals = DB::table('payment_details')
                ->where('shop_id', $shop->id)
                ->selectRaw('SUM(amount) as total_amount, SUM(interest) as total_interest')
                ->first();

            $totalPaid = ($totals->total_amount ?? 0) + ($totals->total_interest ?? 0);
            $shop->total_accumulated_pay = $totalPaid;

            // Commented out interest calculation, now handled in record method
            /*
            // Apply late payment penalty if applicable (once per month)
            $today = now();
            $firstOfMonth = $today->copy()->startOfMonth();
            if ($today->day > 28) {
                // ensure we haven't already charged interest for this month
                if (!$shop->last_interest_charged || $shop->last_interest_charged->lt($firstOfMonth)) {
                    $paymentThisMonth = DB::table('payment_details')
                        ->where('shop_id', $shop->id)
                        ->whereYear('paid_at', $today->year)
                        ->whereMonth('paid_at', $today->month)
                        ->sum('amount');

                    if ($paymentThisMonth < $shop->monthly_rent) {
                        $penalty = ($shop->monthly_rent * $shop->rate) / 100;
                        $shop->due_interest += $penalty;
                    }

                    $shop->last_interest_charged = $firstOfMonth;
                    try { $shop->save(); } catch (\Exception $e) { // ignore }
                }
            }
            */
            
            // 2. Calculate current due payment only. Keep shop's stored due_interest value from shops.due_interest.
            $dueInfo = $this->computeDueFromLastPayment($shop);
            $shop->due_payment = $dueInfo['due_payment'];
            $shop->due_interest = $dueInfo['due_interest'];
            $shop->total_need_to_pay = max(0, $shop->due_payment + $shop->due_interest);

            // Add unpaid months breakdown
            $shop->unpaid_months = $this->getUnpaidMonths($shop);

            return $shop;
        });

        return response()->json($shops);
    }

    public function record(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'receipt_number' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'interest' => 'nullable|numeric|min:0',
            // allow manual adjustment of the savings account (positive deposit, negative withdrawal)
            'saving_adjustment' => 'nullable|numeric',
            'paid_at' => 'required|date',
            'selected_months' => 'required|json',
            'selected_dues' => 'nullable|json',
            'year' => 'required|integer',
            'slip' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $selectedMonths = json_decode($request->selected_months, true);
        $selectedDues = json_decode($request->selected_dues, true) ?: [];
        if (!is_array($selectedMonths) || empty($selectedMonths)) {
            return response()->json(['message' => 'Selected months are required'], 422);
        }
        if (!is_array($selectedDues)) {
            return response()->json(['message' => 'Selected interest months are invalid'], 422);
        }

        $selectedMonths = array_map('intval', $selectedMonths);
        $selectedDues = array_values(array_unique(array_map('intval', $selectedDues)));
        foreach ($selectedMonths as $month) {
            if ($month < 1 || $month > 12) {
                return response()->json(['message' => 'Selected month is invalid'], 422);
            }
        }
        foreach ($selectedDues as $month) {
            if ($month < 1 || $month > 12 || !in_array($month, $selectedMonths, true)) {
                return response()->json(['message' => 'Selected interest month is invalid'], 422);
            }
        }

        $year = (int) $request->input('year', now()->year);
        $now = now();
        $interestDueAt = $now->copy()->day(28)->setTime(12, 0);
        foreach ($selectedDues as $month) {
            $selectedMonthDate = Carbon::create($year, $month, 1);
            $interestIsDue = $selectedMonthDate->lt($now->copy()->startOfMonth())
                || ($selectedMonthDate->isSameMonth($now) && $now->gt($interestDueAt));
            if (!$interestIsDue) {
                return response()->json(['message' => 'Interest is not due for the selected month yet'], 422);
            }
        }

        $amount = (float) $request->input('amount', 0);
        $interest = (float) $request->input('interest', 0);
        $savingAdjustment = (float) $request->input('saving_adjustment', 0);

        $payableMonths = collect($selectedMonths)
            ->map(fn ($month) => (int) $month)
            ->sort()
            ->values()
            ->all();
        $payableMonthLabel = collect($payableMonths)->map(function ($month) use ($year) {
            $date = Carbon::create($year, $month, 1);
            return $date->format('M Y');
        })->implode(', ');

        // at least one of amount, interest or saving adjustment must be non-zero
        if ($amount <= 0 && $interest <= 0 && $savingAdjustment == 0) {
            return response()->json(['message' => 'Amount, interest or saving adjustment is required'], 422);
        }

        try {
            DB::beginTransaction();

            $shop = Shop::findOrFail($request->shop_id);

            // Calculate per month amounts
            $monthlyRent = $shop->monthly_rent ?? 0;
            $rate = $shop->rate ?? 0;
            $totalRent = count($selectedMonths) * $monthlyRent;
            $totalInterest = count($selectedDues) * ($monthlyRent * $rate / 100);

            // Check for duplicate payments (one record per month/year per shop)
            foreach ($selectedMonths as $month) {
                $existingPayment = Payment::where('shop_id', $shop->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                if ($existingPayment) {
                    return response()->json(['message' => "Payment for month {$month}/{$year} already exists"], 422);
                }
            }

            // Create Payment records for each selected month
            $firstPayment = null;
            foreach ($selectedMonths as $index => $month) {
                $monthAmount = $monthlyRent;
                $isDueInterestCollected = in_array($month, $selectedDues);
                $monthInterest = $isDueInterestCollected ? $monthlyRent * $rate / 100 : 0;

                $savingForThis = 0;
                if ($index == 0 && $savingAdjustment != 0) {
                    $savingForThis = $savingAdjustment;
                }

                $payment = Payment::create([
                    'shop_id' => $shop->id,
                    'amount' => $monthAmount,
                    'interest' => $monthInterest,
                    'saving_adjustment' => $savingForThis,
                    'receipt_number' => $request->receipt_number,
                    'paid_at' => $request->paid_at,
                    'month' => $month,
                    'year' => $year,
                    'monthly_payment' => 1,
                    'due_interest' => $isDueInterestCollected ? 1 : 0,
                    'payable_months' => $payableMonthLabel,
                ]);

                if (!$firstPayment) $firstPayment = $payment;

                // Create Payment Detail
                PaymentDetail::create([
                    'payment_id' => $payment->id,
                    'shop_id' => $shop->id,
                    'shop_number' => $shop->shop_number,
                    'owner_name' => $shop->shop_owner_name,
                    'amount' => $monthAmount,
                    'interest' => $monthInterest,
                    'saving_adjustment' => $savingForThis,
                    'receipt_number' => $request->receipt_number,
                    'paid_at' => $request->paid_at,
                    'payable_months' => $payableMonthLabel,
                ]);
            }

            // Attach slip to the first payment if exists
            if ($request->hasFile('slip') && $firstPayment) {
                $file = $request->file('slip');
                $receiptName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '_', $request->receipt_number), '_');
                $receiptName = $receiptName !== '' ? $receiptName : 'receipt';
                $extension = $file->guessExtension();
                $fileName = 'receipt_' . $receiptName . '_' . $firstPayment->id . '.' . $extension;
                $file->move(public_path('images'), $fileName);
                $path = 'public/images/' . $fileName;
                $firstPayment->slips()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            // Update Shop Balances
            // reduce due amounts from actual payment and interest applied
            $paymentToDue = min($totalRent, $shop->due_payment ?? 0);
            $shop->due_payment = max(0, ($shop->due_payment ?? 0) - $paymentToDue);

            $interestToDue = min($totalInterest, $shop->due_interest ?? 0);
            $shop->due_interest = max(0, ($shop->due_interest ?? 0) - $interestToDue);

            // Record total paid and sync total due/balance_amount for dashboard reports
            $shop->total_paid += $paymentToDue + $interestToDue;
            $shop->total_due = max(0, ($shop->total_due ?? 0) - ($paymentToDue + $interestToDue));
            $shop->balance_amount = max(0, ($shop->balance_amount ?? 0) - ($paymentToDue + $interestToDue));

            // If the user paid over current arrears, apply overflow to savings.
            $overflowAmount = max(0, $totalRent - $paymentToDue);
            $overflowInterest = max(0, $totalInterest - $interestToDue);

            if ((($shop->due_payment ?? 0) <= 0) && $shop->monthly_rent > 0 && ($overflowAmount + $overflowInterest) > 0) {
                $shop->saving = ($shop->saving ?? 0) + $overflowAmount + $overflowInterest;
            }

            // manual adjustment from form (positive deposit or negative withdrawal)
            if ($savingAdjustment != 0) {
                $shop->saving = ($shop->saving ?? 0) + $savingAdjustment;
                // prevent negative saving value
                if ($shop->saving < 0) {
                    $shop->saving = 0;
                }
                // track aggregate deductions if desired
                if ($savingAdjustment < 0) {
                    $shop->deduct_payment = ($shop->deduct_payment ?? 0) + abs($savingAdjustment);
                }
            }

            $shop->save();

            DB::commit();

            // Recalculate due amounts based on last locked payment month
            $dueInfo = $this->computeDueFromLastPayment($shop);
            $shop->due_payment = $dueInfo['due_payment'];
            $shop->due_interest = $dueInfo['due_interest'];
            $shop->save();

            return response()->json(['message' => 'Payment recorded successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    public function shop($id)
    {
        $shop = Shop::with('busStand')
            ->withMax('payments', 'paid_at')
            ->findOrFail($id);

        $shop->total_accumulated_pay = PaymentDetail::where('shop_id', $shop->id)
            ->selectRaw('COALESCE(SUM(amount), 0) + COALESCE(SUM(interest), 0) as total')
            ->value('total') ?? 0;

        $dueInfo = $this->computeDueFromLastPayment($shop);
        $shop->due_payment = $dueInfo['due_payment'];
        $shop->due_interest = $dueInfo['due_interest'];
        $shop->total_need_to_pay = max(0, $shop->due_payment + $shop->due_interest);
        $shop->last_payment_date = $shop->payments_max_paid_at ? $shop->payments_max_paid_at : null;

        // Add unpaid months breakdown
        $shop->unpaid_months = $this->getUnpaidMonths($shop);

        return response()->json($shop);
    }

    public function history(Request $request)
    {
        $shopId = $request->query('shop_id');
        if (!$shopId) return response()->json([]);

        $payments = Payment::with('slips')
            ->where('shop_id', $shopId)
            ->orderBy('paid_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $slipsByReceipt = $payments
            ->filter(fn ($payment) => $payment->receipt_number && $payment->slips->isNotEmpty())
            ->groupBy('receipt_number')
            ->map(fn ($receiptPayments) => $receiptPayments->first()->slips);

        $payments->transform(function ($payment) use ($slipsByReceipt) {
            if ($payment->slips->isEmpty() && $payment->receipt_number && $slipsByReceipt->has($payment->receipt_number)) {
                $payment->setRelation('slips', clone $slipsByReceipt->get($payment->receipt_number));
            }

            $payment->slips->transform(function ($slip) {
                $slip->url = str_starts_with($slip->file_path, 'public/images/')
                    ? asset(substr($slip->file_path, strlen('public/')))
                    : Storage::url($slip->file_path);
                return $slip;
            });
            return $payment;
        });

        return response()->json($payments);
    }
}