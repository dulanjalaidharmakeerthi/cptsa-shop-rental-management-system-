<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use App\Models\Payment;
use Carbon\Carbon;

class FixDueBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'due:fix {shopNumbers?} {--all : Fix due balance for all shops}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix due_payment and due_interest for specific shops by recalculating from payment history. Example: php artisan due:fix "RK-001,RK-002"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shopNumbersInput = $this->argument('shopNumbers');
        $allShops = $this->option('all');

        if (!$shopNumbersInput && !$allShops) {
            $this->error('Please provide shop numbers or use --all flag. Example: php artisan due:fix "RK-001,RK-002" or php artisan due:fix --all');
            return 1;
        }

        if ($allShops) {
            // Get all shops
            $shops = Shop::all();
            $shopNumbers = $shops->pluck('shop_number')->toArray();
            $this->info('Processing ' . count($shopNumbers) . ' shops...');
        } else {
            $shopNumbers = array_map('trim', explode(',', $shopNumbersInput));
        }

        foreach ($shopNumbers as $shopNumber) {
            $shop = Shop::where('shop_number', strtoupper($shopNumber))->first();

            if (!$shop) {
                $this->error("Shop {$shopNumber} not found");
                continue;
            }

            $oldDuePayment = $shop->due_payment;
            $oldDueInterest = $shop->due_interest;

            // Recalculate due amounts
            $dueInfo = $this->calculateDueBalance($shop);

            $shop->due_payment = $dueInfo['due_payment'];
            $shop->due_interest = $dueInfo['due_interest'];
            $shop->save();

            $this->info("Shop: {$shop->shop_number} ({$shop->shop_name})");
            $this->info("  Old due_payment: {$oldDuePayment} → New: {$dueInfo['due_payment']}");
            $this->info("  Old due_interest: {$oldDueInterest} → New: {$dueInfo['due_interest']}");
            $this->info("  Unpaid months: {$dueInfo['due_months']} | Interest months: {$dueInfo['interest_months']}");
            $this->line('');
        }

        $this->info('Due balance recalculation completed!');
        return 0;
    }

    /**
     * Calculate due payment and interest based on unpaid months
     */
    private function calculateDueBalance(Shop $shop)
    {
        if (!$shop->monthly_rent || !$shop->agreement_start) {
            return [
                'due_payment' => 0,
                'due_interest' => 0,
                'due_months' => 0,
                'interest_months' => 0,
            ];
        }

        try {
            $monthlyRent = floatval($shop->monthly_rent);
            $rate = floatval($shop->rate ?: 0);
            $now = now();

            // Get agreement period
            $agreementStart = Carbon::parse($shop->agreement_start)->startOfMonth();
            $minimumStart = Carbon::create(2023, 1, 1)->startOfMonth();
            $startDate = $agreementStart->gt($minimumStart) ? $agreementStart : $minimumStart;

            // Get end date
            $endDate = $now->copy()->startOfMonth();
            if ($shop->agreement_end) {
                $agreementEnd = Carbon::parse($shop->agreement_end)->startOfMonth();
                if ($agreementEnd->lt($now)) {
                    $endDate = $agreementEnd;
                }
            }

            // Get all paid months (monthly_payment = 1 only)
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

                    // Apply interest if month is in the past or current month after 28th
                    if ($monthEnd->lt($now) || ($current->isCurrentMonth() && $now->day > 28)) {
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
            $this->error("Error calculating due balance for shop {$shop->shop_number}: {$e->getMessage()}");
            return [
                'due_payment' => floatval($shop->due_payment ?: 0),
                'due_interest' => floatval($shop->due_interest ?: 0),
                'due_months' => 0,
                'interest_months' => 0,
            ];
        }
    }
}
