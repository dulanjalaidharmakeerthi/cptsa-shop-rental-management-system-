@extends('layouts.app')

@section('title', 'Due Payments')

@section('content')
<div class="due-payments-page">
    <div class="page-header">
        <div>
            <h1>Due Payments</h1>
            <p>All shops without payment recorded for {{ $displayRange }}.</p>
        </div>
        <a href="{{ url('/managedashboard') }}" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <!-- Date Range Filter Form -->
    <div class="card elevation" style="margin-bottom: 24px;">
        <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 600;">Filter by Date Range & Bus Stand</h3>
            <form method="GET" action="{{ route('dashboard.due-payments') }}" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 220px;">
                    <label for="date_from" style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">From Date</label>
                    <input type="date" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ $dateFrom ?? $displayDateFrom }}"
                           style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.95rem;">
                </div>
                <div style="flex: 1; min-width: 220px;">
                    <label for="date_to" style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">To Date</label>
                    <input type="date" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ $dateTo ?? $displayDateTo }}"
                           style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.95rem;">
                </div>
                <div style="flex: 1; min-width: 220px;">
                    <label for="bus_stand_id" style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem;">Bus Stand</label>
                    <select id="bus_stand_id" 
                            name="bus_stand_id" 
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.95rem;">
                        <option value="">-- All Bus Stands --</option>
                        @foreach($busStands as $stand)
                            <option value="{{ $stand->id }}" @if($busStandId == $stand->id) selected @endif>
                                {{ $stand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 8px 18px; height: auto;">Filter</button>
                <a href="{{ route('dashboard.due-payments') }}" class="btn btn-secondary" style="padding: 8px 18px; height: auto; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; border: 1px solid #6b7280;">Reset</a>
            </form>
        </div>
    </div>

    <!-- Due Balance & Interest Summary -->
    <div class="card elevation" style="margin-bottom: 24px;">
        <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 600;">Due Balance Summary</h3>
        </div>
        <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div class="summary-card">
                <div style="font-size: 0.85rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.02em; font-weight: 500; margin-bottom: 8px;">Total Due Members</div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $dueCount }}</div>
                <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 4px;">Shops without payment</div>
            </div>
            <div class="summary-card">
                <div style="font-size: 0.85rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.02em; font-weight: 500; margin-bottom: 8px;">Total Due Balance</div>
                <div style="font-size: 2rem; font-weight: 700; color: #b91c1c;">Rs {{ number_format($totalDueBalance, 2) }}</div>
                <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 4px;">Outstanding rent payment</div>
            </div>
            <div class="summary-card">
                <div style="font-size: 0.85rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.02em; font-weight: 500; margin-bottom: 8px;">Total Interest Due</div>
                <div style="font-size: 2rem; font-weight: 700; color: #d97706;">Rs {{ number_format($totalDueInterest, 2) }}</div>
                <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 4px;">Interest to be paid by tenants</div>
            </div>
            <div class="summary-card">
                <div style="font-size: 0.85rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.02em; font-weight: 500; margin-bottom: 8px;">Total Due with Interest</div>
                <div style="font-size: 2rem; font-weight: 700; color: #7c3aed;">Rs {{ number_format($totalDueWithInterest, 2) }}</div>
                <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 4px;">Total amount to be collected</div>
            </div>
        </div>
    </div>

    <div class="card elevation">
        <div class="card-header">
            <div>
                <div class="summary-title">Due members: {{ $dueCount }}</div>
                <div class="summary-subtitle">Click any row to open the shop detail page.</div>
            </div>
            <div class="header-actions">
                <span class="filter-label">Filter: {{ $displayRange }}</span>
                <a href="{{ route('dashboard.due-payments', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-primary btn-download">Download Excel</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover due-payments-table">
                <colgroup>
                    <col style="width: 5%;">
                    <col style="width: 12%;">
                    <col style="width: 10%;">
                    <col style="width: 18%;">
                    <col style="width: 15%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bus Stand</th>
                        <th>Shop No.</th>
                        <th>Shop Name</th>
                        <th>Owner</th>
                        <th class="text-right">Monthly Rent</th>
                        <th class="text-right">Due Amount</th>
                        <th class="text-right">Due Interest</th>
                        <th>Last Payment</th>
                        <th>Agreement End</th>
                    </tr>
                </thead>
                <tbody>
                    @if($dueShops->count() > 0)
                        @foreach($dueShops as $index => $shop)
                            <tr class="clickable-row" onclick="window.location='{{ url('/payments?shop_id=' . $shop->id) }}';" aria-label="Open payment page for {{ $shop->shop_name }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $shop->busStand ? $shop->busStand->name : 'N/A' }}</td>
                                <td>{{ $shop->shop_number }}</td>
                                <td>{{ $shop->shop_name }}</td>
                                <td>{{ $shop->shop_owner_name }}</td>
                                <td class="text-right">Rs {{ number_format($shop->monthly_rent ?? 0, 2) }}</td>
                                <td class="text-right text-danger">Rs {{ number_format($shop->due_payment ?? 0, 2) }}</td>
                                <td class="text-right text-warning">Rs {{ number_format($shop->due_interest ?? 0, 2) }}</td>
                                <td>{{ $shop->last_payment_date }}</td>
                                <td>{{ $shop->agreement_end ? \Carbon\Carbon::parse($shop->agreement_end)->format('Y-m-d') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center">No due members found for this month.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.due-payments-page {
    padding: 28px;
    max-width: 1140px;
    margin: auto;
    font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #1f2937;
}
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
    gap: 1rem;
}
.page-header h1 {
    margin: 0;
    font-size: 1.85rem;
    font-weight: 700;
}
.page-header p {
    margin: 0.35rem 0 0;
    color: #4b5563;
}
.card.elevation {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08);
}
.card > .card-header {
    padding: 14px 16px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.summary-card {
    padding: 16px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.summary-card:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
}
.table-responsive {
    width: 100%;
    overflow-x: auto;
}
.card-header {
    padding: 18px 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}
.summary-title {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 4px;
}
.summary-subtitle {
    font-size: 0.95rem;
    color: #4b5563;
}
.header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.filter-label {
    font-size: 0.9rem;
    color: #4f46e5;
    font-weight: 600;
}
.btn-download {
    padding: 10px 18px;
}
.table.due-payments-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 920px;
    font-size: 0.94rem;
}
.table.due-payments-table th,
.table.due-payments-table td {
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    vertical-align: middle;
}
.table.due-payments-table th {
    background: #f8f9ff;
    color: #1f2937;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 0.8rem;
}
.table.due-payments-table td {
    color: #334155;
}
.table.due-payments-table tbody tr:nth-child(odd) {
    background: #fbfbff;
}
.table.due-payments-table tbody tr:hover {
    background: #eef2ff;
}
.table.due-payments-table td.text-right,
.table.due-payments-table th.text-right {
    text-align: right;
}
.clickable-row {
    cursor: pointer;
}
.clickable-row:hover {
    box-shadow: inset 0 0 0 1px rgba(79, 70, 229, 0.15);
}
.btn.btn-primary {
    background: #4f46e5;
    color: #ffffff;
    border: 1px solid #4f46e5;
    border-radius: 8px;
    padding: 9px 14px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 3px 10px rgba(79, 70, 229, 0.16);
}
.btn.btn-primary:hover { background: #4338ca; border-color: #4338ca; }
.btn.btn-secondary {
    background: #6b7280;
    color: #ffffff;
    border: 1px solid #6b7280;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
}
.btn.btn-secondary:hover { background: #4b5563; border-color: #4b5563; }
.text-danger { color: #b91c1c; font-weight: 700; }
.text-warning { color: #d97706; font-weight: 700; }
.text-center { text-align: center; color: #6b7280; font-weight: 500; }
</style>

@endsection
