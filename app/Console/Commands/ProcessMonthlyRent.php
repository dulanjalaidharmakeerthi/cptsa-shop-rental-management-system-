<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use Carbon\Carbon;

class ProcessMonthlyRent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * We choose a simple name so it can be scheduled easily.
     *
     * @var string
     */
    protected $signature = 'rent:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Charge monthly rent on the 1st of the month and apply interest to overdue amounts (28+ days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();

        // Only perform the "new rent" logic on the 1st of the month.
        // The interest portion can run every day but scheduling is usually once a month.
        if ($today->day !== 1) {
            $this->info('rent:process should normally run on the 1st. Running interest check only.');
        }

        $monthColumn = 'rent_' . strtolower($today->format('F')); // e.g. rent_january

        Shop::chunk(100, function ($shops) use ($today, $monthColumn) {
            foreach ($shops as $shop) {
                $rent = $shop->monthly_rent ?? 0;

                // if we're on the 1st, add the month's rent.  A guard ensures we only
                // charge once per calendar month, in case the scheduler is triggered
                // twice inadvertently.
                if ($today->day === 1) {
                    $alreadyCharged = $shop->last_rent_charged
                        && Carbon::parse($shop->last_rent_charged)->month === $today->month
                        && Carbon::parse($shop->last_rent_charged)->year === $today->year;
                    if (! $alreadyCharged) {
                        // store the rent in the corresponding monthly column so reports can show it
                        $shop->{$monthColumn} = $rent;

                        // update due amounts
                        $shop->due_payment += $rent;
                        $shop->total_due += $rent;
                        $shop->year_charge += $rent;
                        $shop->last_rent_charged = $today;
                    }
                }

                // interest calculation: when the find has unpaid balance by 28th of the month.
                // For current running month, if rent was added on 1st and remains unpaid by 28th,
                // we apply rate-based penalty once.
                if ($shop->due_payment > 0) {
                    if ($today->day === 28) {
                        $alreadyChargedForThisMonth = $shop->last_interest_charged
                            && Carbon::parse($shop->last_interest_charged)->month === $today->month
                            && Carbon::parse($shop->last_interest_charged)->year === $today->year;

                        if (! $alreadyChargedForThisMonth) {
                            $interest = ($shop->monthly_rent * ($shop->rate / 100));
                            $shop->due_interest += $interest;
                            $shop->total_due += $interest;
                            $shop->year_interest += $interest;
                            $shop->last_interest_charged = $today;
                        }
                    } else {
                        // existing fallback consent: 28+ days since last rent/interest charge
                        $baseDate = $shop->last_interest_charged ?: $shop->last_rent_charged ?: $shop->created_at;
                        if ($baseDate && Carbon::parse($baseDate)->diffInDays($today) > 28) {
                            $interest = ($shop->due_payment * ($shop->rate / 100));
                            $shop->due_interest += $interest;
                            $shop->total_due += $interest;
                            $shop->year_interest += $interest;
                            $shop->last_interest_charged = $today;
                        }
                    }
                }

                // save only if something changed (avoid touching updated_at unnecessarily)
                if ($shop->isDirty()) {
                    $shop->save();
                }
            }
        });

        $this->info('Monthly rent processing completed for ' . $today->format('F Y'));
    }
}
