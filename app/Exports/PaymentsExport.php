<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentsExport implements FromView
{
    protected $shops;
    protected $filters;

    public function __construct($shops, $filters = [])
    {
        $this->shops = $shops;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('exports.payments', [
            'shops' => $this->shops,
            'filters' => $this->filters,
        ]);
    }
}
