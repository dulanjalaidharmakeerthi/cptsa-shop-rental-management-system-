<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BusStand;
use App\Models\Shop;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;

class HistoryController extends Controller
{
    /**
     * Show payment history with optional shop, bus stand, owner name, and ID filters.
     */
    public function index(Request $request)
    {
        $filters = $this->historyFilters($request);

        $yearFrom = $request->query('year_from');
        $yearTo = $request->query('year_to');
        $status = $request->query('status');
        $shopId = $request->query('shop_id');

        // build payment constraint based on year range if provided
        $paymentConstraint = function ($query) use ($yearFrom, $yearTo) {
            if ($yearFrom && $yearTo) {
                try {
                    $start = Carbon::create((int)$yearFrom, 1, 1, 0, 0, 0);
                    $end = Carbon::create((int)$yearTo, 12, 31, 23, 59, 59);
                    $query->whereBetween('paid_at', [$start, $end]);
                } catch (\Exception $ex) {
                    // ignore malformed years
                }
            } elseif ($yearFrom) {
                try {
                    $start = Carbon::create((int)$yearFrom, 1, 1, 0, 0, 0);
                    $end = Carbon::create((int)$yearFrom, 12, 31, 23, 59, 59);
                    $query->whereBetween('paid_at', [$start, $end]);
                } catch (\Exception $ex) {}
            }
            $query->orderBy('paid_at', 'desc');
        };

        // Load shops matching query and eager-load payments (within range) and slips
        $shops = Shop::with(['busStand', 'payments' => $paymentConstraint, 'payments.slips'])
            ->when(!$shopId, fn ($builder) => $this->applyShopFilters($builder, $filters))
            ->when($shopId, function($builder) use ($shopId) {
                $builder->where('id', $shopId);
            })
            ->limit(200)
            ->get();

        // Export to Excel if requested
        if ($request->has('export')) {
            $exportFilters = array_merge($filters, [
                'year_from' => $yearFrom,
                'year_to' => $yearTo,
                'status' => $status,
            ]);
            return Excel::download(new PaymentsExport($shops, $exportFilters), 'payment_history.xlsx');
        }

        return view('history', [
            'shops' => $shops,
            'filters' => $filters,
            'busStands' => BusStand::orderBy('name')->get(),
        ]);
    }

    public function export(Request $request)
    {
        $filters = $this->historyFilters($request);
        $from = $request->query('year_from');
        $to = $request->query('year_to');
        $status = $request->query('status');
        $shopId = $request->query('shop_id');

        $fromDate = $from ? \Carbon\Carbon::parse($from)->startOfDay() : null;
        $toDate = $to ? \Carbon\Carbon::parse($to)->endOfDay() : null;

        $shops = \App\Models\Shop::with(['busStand', 'payments' => function($query) use ($fromDate, $toDate, $status) {
            if ($fromDate) {
                $query->where(function($q) use ($fromDate) {
                    $q->whereDate('paid_at', '>=', $fromDate)
                      ->orWhere(function($q2) use ($fromDate) {
                          $q2->whereNull('paid_at')->whereDate('created_at', '>=', $fromDate);
                      });
                });
            }
            if ($toDate) {
                $query->where(function($q) use ($toDate) {
                    $q->whereDate('paid_at', '<=', $toDate)
                      ->orWhere(function($q2) use ($toDate) {
                          $q2->whereNull('paid_at')->whereDate('created_at', '<=', $toDate);
                      });
                });
            }
            if ($status) {
                if ($status === 'paid') {
                    $query->whereNotNull('paid_at');
                } elseif ($status === 'due') {
                    $query->whereNull('paid_at');
                } elseif ($status === 'late') {
                    $query->whereNotNull('paid_at')->whereRaw('DAY(paid_at) > 28');
                }
            }
            $query->orderBy('paid_at', 'desc');
        }, 'payments.slips'])
        ->when(!$shopId, fn ($builder) => $this->applyShopFilters($builder, $filters))
        ->when($shopId, function($builder) use ($shopId) {
            $builder->where('id', $shopId);
        })
        ->limit(200)
        ->get();

        $exportFilters = array_merge($filters, [
            'year_from' => $from,
            'year_to' => $to,
            'status' => $status,
        ]);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PaymentsExport($shops, $exportFilters), 'payment_history.xlsx');
    }

    private function historyFilters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'shop_number' => trim((string) $request->query('shop_number', '')),
            'bus_stand_id' => $request->query('bus_stand_id'),
            'owner_name' => trim((string) $request->query('owner_name', '')),
            'owner_nic' => trim((string) $request->query('owner_nic', '')),
        ];
    }

    private function applyShopFilters(Builder $builder, array $filters): Builder
    {
        return $builder
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($ownerQuery) use ($filters) {
                    $ownerQuery->where('shop_owner_name', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('shop_owner_nic', 'like', '%' . $filters['q'] . '%');
                });
            })
            ->when($filters['shop_number'] !== '', function ($query) use ($filters) {
                $query->where(function ($shopQuery) use ($filters) {
                    $shopQuery->where('shop_number', 'like', '%' . $filters['shop_number'] . '%')
                        ->orWhere('shop_name', 'like', '%' . $filters['shop_number'] . '%');
                });
            })
            ->when($filters['bus_stand_id'], function ($query) use ($filters) {
                $query->where('bus_stand_id', $filters['bus_stand_id']);
            })
            ->when($filters['owner_name'] !== '', function ($query) use ($filters) {
                $query->where('shop_owner_name', 'like', '%' . $filters['owner_name'] . '%');
            })
            ->when($filters['owner_nic'] !== '', function ($query) use ($filters) {
                $query->where('shop_owner_nic', 'like', '%' . $filters['owner_nic'] . '%');
            });
    }
}
