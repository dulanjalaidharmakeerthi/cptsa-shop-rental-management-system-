<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DueReportExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $dueData;
    protected $filters;

    public function __construct(array $dueData, array $filters = [])
    {
        $this->dueData = $dueData;
        $this->filters = $filters;
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->dueData as $shopData) {
            $shop = $shopData['shop'];

            foreach ($shopData['years'] as $yearData) {
                foreach ($yearData['months'] as $monthData) {
                    $rows[] = [
                        'Shop Name' => $shop->shop_name,
                        'Shop Number' => $shop->shop_number,
                        'Owner Name' => $shop->shop_owner_name,
                        'Bus Stand' => optional($shop->busStand)->name,
                        'Year' => $yearData['year'],
                        'Month' => $monthData['month'],
                        'Monthly Rent' => number_format($monthData['rent'], 2, '.', ''),
                        'Rate (%)' => number_format($monthData['rate'] ?? 0, 2, '.', ''),
                        'Monthly Balance' => number_format($monthData['balance'], 2, '.', ''),
                        'Interest' => number_format($monthData['interest'], 2, '.', ''),
                        'Monthly Amount' => number_format($monthData['monthly_amount'], 2, '.', ''),
                        'Amount Paid' => number_format($monthData['paid'], 2, '.', ''),
                        'Total Paid' => number_format($monthData['paid_with_interest'] ?? 0, 2, '.', ''),
                        'Paid Date' => $monthData['paid_date'] ?? '',
                        'Status' => $monthData['status'],
                    ];
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Shop Name',
            'Shop Number',
            'Owner Name',
            'Bus Stand',
            'Year',
            'Month',
            'Monthly Rent',
            'Rate (%)',
            'Monthly Balance',
            'Interest',
            'Monthly Amount',
            'Amount Paid',
            'Total Paid',
            'Paid Date',
            'Status',
        ];
    }
}
