@extends('layouts.app')

@section('content')
<div class="tenants-report-container">
    <div class="tenants-header">
        <div class="header-content">
            <h1><i class="fas fa-home"></i> Tenant Reports</h1>
            <p>Manage and monitor all tenant information</p>
        </div>
    </div>

    <section class="tenant-analytics" aria-label="Outstanding balance analytics">
        <div class="tenant-analytics-heading">
            <div>
                <p class="tenant-analytics-eyebrow">OUTSTANDING BALANCE ANALYTICS</p>
                <h2>Tenant due overview</h2>
                <p>Totals reflect all shops matching the current filters.</p>
            </div>
            <div class="tenant-shop-count">
                <span>Shops with outstanding balance</span>
                <strong>{{ number_format($tenantSummary->shops_with_outstanding_balance) }}</strong>
            </div>
        </div>

        <div class="tenant-analytics-grid">
            <div class="tenant-due-metrics">
                <article class="tenant-due-metric principal-metric">
                    <span>Due payment</span>
                    <strong>Rs {{ number_format($tenantSummary->due_payment_total, 2) }}</strong>
                    <small>Outstanding principal</small>
                </article>
                <article class="tenant-due-metric interest-metric">
                    <span>Due interest</span>
                    <strong>Rs {{ number_format($tenantSummary->due_interest_total, 2) }}</strong>
                    <small>Outstanding interest</small>
                </article>
                <article class="tenant-due-metric total-metric">
                    <span>Total outstanding</span>
                    <strong>Rs {{ number_format($tenantSummary->total_outstanding, 2) }}</strong>
                    <small>{{ number_format($tenantSummary->shop_count) }} shops in this report</small>
                </article>
            </div>

            <div class="tenant-due-chart">
                <div class="tenant-due-donut" role="img"
                    aria-label="Outstanding balance composition: {{ number_format($tenantSummary->due_payment_share, 1) }} percent due payment and {{ number_format($tenantSummary->due_interest_share, 1) }} percent due interest"
                    style="--principal-share: {{ $tenantSummary->due_payment_share }}%; background: {{ $tenantSummary->total_outstanding > 0 ? 'conic-gradient(#2563eb 0 ' . $tenantSummary->due_payment_share . '%, #f59e0b ' . $tenantSummary->due_payment_share . '% 100%)' : '#e2e8f0' }}">
                    <div class="tenant-due-donut-center">
                        @if($tenantSummary->total_outstanding > 0)
                            <strong>{{ number_format($tenantSummary->due_payment_share, 0) }}%</strong>
                            <span>principal</span>
                        @else
                            <strong>Rs 0</strong>
                            <span>no outstanding</span>
                        @endif
                    </div>
                </div>
                <div class="tenant-due-legend">
                    <div><span class="legend-dot principal-dot"></span><span>Due payment</span><strong>{{ number_format($tenantSummary->due_payment_share, 1) }}%</strong></div>
                    <div><span class="legend-dot interest-dot"></span><span>Due interest</span><strong>{{ number_format($tenantSummary->due_interest_share, 1) }}%</strong></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Section -->
    <div class="filter-section">
        <form action="{{ route('reports.tenants.index') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="Search by name, phone, address..." 
                           value="{{ request('q') }}" class="search-input">
                </div>

                <input type="text" name="shop_owner_name" placeholder="Filter by owner name" 
                       value="{{ request('shop_owner_name') }}" class="search-input" style="flex: 1; min-width: 200px;">

                <select name="bus_stand_id" class="filter-select">
                    <option value="">All Bus Stands</option>
                    @foreach($busStands as $bs)
                        <option value="{{ $bs->id }}" {{ request('bus_stand_id') == $bs->id ? 'selected' : '' }}>
                            {{ $bs->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-filter">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>

            @if(request('q') || request('shop_owner_name') || request('bus_stand_id'))
                <div class="active-filters">
                    <span><i class="fas fa-check-circle"></i> Active Filters:</span>
                    @if(request('q'))
                        <span class="filter-badge">Search: "{{ request('q') }}"</span>
                    @endif
                    @if(request('shop_owner_name'))
                        <span class="filter-badge">Owner: "{{ request('shop_owner_name') }}"</span>
                    @endif
                    @if(request('bus_stand_id'))
                        <span class="filter-badge">Bus Stand: "{{ optional($busStands->firstWhere('id', request('bus_stand_id')))->name }}"</span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    <!-- Tenants Table -->
    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="tenants-table">
                <thead>
                    <tr>
                        <th class="col-owner">Owner Name</th>
                        <th class="col-phone">Phone</th>
                        <th class="col-address">Shop Address</th>
                        <th class="col-rent">Monthly Rent</th>
                        <th class="col-financial">Financial Summary</th>
                        <th class="col-agreement">Agreement</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shops as $shop)
                        <tr class="table-row">
                            <td class="col-owner">
                                <div class="owner-info">
                                    <div class="owner-name">
                                        <i class="fas fa-user-circle"></i> {{ $shop->shop_owner_name }}
                                    </div>
                                    <div class="owner-nic">{{ $shop->shop_owner_nic }}</div>
                                </div>
                            </td>

                            <td class="col-phone">
                                <div class="phone-info">
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:{{ $shop->shop_owner_phone }}" class="phone-link">
                                        {{ $shop->shop_owner_phone }}
                                    </a>
                                </div>
                            </td>

                            <td class="col-address">
                                <div class="address-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $shop->shop_address ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <td class="col-rent">
                                <span class="rent-badge">Rs {{ number_format($shop->monthly_rent, 2) }}</span>
                            </td>

                            <td class="col-financial">
                                <div class="financial-summary">
                                    <div class="fin-item">
                                        <span class="label">Due Payment:</span>
                                        <span class="value">Rs {{ number_format($shop->due_payment ?? 0, 2) }}</span>
                                    </div>
                                    <div class="fin-item">
                                        <span class="label">Due Interest:</span>
                                        <span class="value">Rs {{ number_format($shop->due_interest ?? 0, 2) }}</span>
                                    </div>
                                    <div class="fin-item">
                                        <span class="label">Year Total:</span>
                                        <span class="value">Rs {{ number_format($shop->year_total_taken ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="col-agreement">
                                <div class="agreement-info">
                                    @if($shop->agreement_duration)
                                        <div class="financial-item">
                                            <span class="label">Duration:</span>
                                            <span class="value">{{ $shop->agreement_duration }}</span>
                                        </div>
                                    @endif
                                    @if($shop->agreement_start)
                                        <div class="financial-item">
                                            <span class="label">Start:</span>
                                            <span class="value">{{ optional($shop->agreement_start)->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                    @if($shop->agreement_end)
                                        <div class="financial-item">
                                            <span class="label">End:</span>
                                            <span class="value">{{ optional($shop->agreement_end)->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="col-actions">
                                <a href="{{ route('reports.tenants.show', $shop->id) }}" class="btn-action btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No tenants found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($shops->hasPages())
        <div class="pagination-wrapper">
            {{ $shops->links() }}
        </div>
    @endif
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .tenants-report-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #fff 100%);
        min-height: 100vh;
        padding: 30px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .tenant-analytics {
        margin: 0 0 22px;
        padding: 22px;
        border: 1px solid #e5eaf1;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
    }

    .tenant-analytics-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
    }

    .tenant-analytics-eyebrow {
        margin: 0 0 5px;
        color: #64748b;
        font-size: 10px;
        font-weight: 750;
        letter-spacing: 1px;
    }

    .tenant-analytics-heading h2 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 19px;
        font-weight: 750;
    }

    .tenant-analytics-heading > div:first-child > p:last-child {
        margin: 0;
        color: #64748b;
        font-size: 12px;
    }

    .tenant-shop-count {
        display: grid;
        gap: 4px;
        padding: 10px 14px;
        border-radius: 11px;
        background: #f8fafc;
        color: #64748b;
        font-size: 10px;
    }

    .tenant-shop-count strong {
        color: #172033;
        font-size: 18px;
    }

    .tenant-analytics-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(230px, 0.55fr);
        align-items: center;
        gap: 24px;
    }

    .tenant-due-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .tenant-due-metric {
        display: grid;
        align-content: start;
        gap: 8px;
        min-width: 0;
        padding: 14px;
        border: 1px solid #edf1f5;
        border-radius: 12px;
        background: #fbfcfe;
    }

    .tenant-due-metric > span {
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
    }

    .tenant-due-metric strong {
        overflow-wrap: anywhere;
        color: #172033;
        font-size: clamp(15px, 1.5vw, 20px);
        font-weight: 750;
    }

    .tenant-due-metric small {
        color: #94a3b8;
        font-size: 10px;
    }

    .principal-metric {
        border-top: 3px solid #2563eb;
    }

    .interest-metric {
        border-top: 3px solid #f59e0b;
    }

    .total-metric {
        border-top: 3px solid #7c3aed;
    }

    .tenant-due-chart {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
    }

    .tenant-due-donut {
        display: grid;
        width: 142px;
        height: 142px;
        flex: 0 0 142px;
        place-items: center;
        border-radius: 50%;
        background: conic-gradient(#2563eb 0 var(--principal-share), #f59e0b var(--principal-share) 100%);
    }

    .tenant-due-donut-center {
        display: grid;
        width: 98px;
        height: 98px;
        align-content: center;
        justify-items: center;
        border-radius: 50%;
        background: #fff;
    }

    .tenant-due-donut-center strong {
        color: #172033;
        font-size: 22px;
    }

    .tenant-due-donut-center span {
        color: #64748b;
        font-size: 10px;
    }

    .tenant-due-legend {
        display: grid;
        gap: 12px;
        min-width: 126px;
    }

    .tenant-due-legend > div {
        display: grid;
        grid-template-columns: 9px 1fr auto;
        align-items: center;
        gap: 7px;
        color: #64748b;
        font-size: 10px;
    }

    .tenant-due-legend strong {
        color: #172033;
        font-size: 11px;
    }

    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .principal-dot {
        background: #2563eb;
    }

    .interest-dot {
        background: #f59e0b;
    }

    /* Header */
    .tenants-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        border-radius: 15px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .header-content h1 {
        font-size: 28px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-content p {
        font-size: 14px;
        opacity: 0.9;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .filter-group {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        color: #667eea;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        padding: 10px 12px 10px 38px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-select {
        padding: 10px 12px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 150px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-filter {
        padding: 10px 20px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .active-filters {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 12px;
        background: #f0f2f5;
        border-radius: 8px;
        font-size: 13px;
        color: #2c3e50;
    }

    .active-filters span:first-child {
        font-weight: 600;
        color: #667eea;
    }

    .filter-badge {
        background: white;
        padding: 4px 10px;
        border-radius: 6px;
        border-left: 3px solid #667eea;
    }

    /* Table Wrapper */
    .table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .tenants-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tenants-table thead {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .tenants-table thead th {
        padding: 18px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .tenants-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .tenants-table tbody tr:hover {
        background: linear-gradient(135deg, #f5f7fa, #f0f2f5);
        box-shadow: inset 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    .table-row td {
        padding: 16px 12px;
        vertical-align: middle;
    }

    .col-owner {
        width: 180px;
    }

    .col-phone {
        width: 140px;
    }

    .col-address {
        width: 160px;
    }

    .col-rent {
        width: 130px;
    }

    .col-financial {
        width: 200px;
    }

    .col-agreement {
        width: 150px;
    }

    .col-actions {
        width: 100px;
    }

    /* Owner Info */
    .owner-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .owner-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .owner-name i {
        color: #667eea;
    }

    .owner-nic {
        font-size: 11px;
        color: #999;
    }

    /* Phone Info */
    .phone-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }

    .phone-info i {
        color: #667eea;
    }

    .phone-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .phone-link:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Address Info */
    .address-info {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 12px;
        color: #555;
    }

    .address-info i {
        color: #667eea;
        margin-top: 2px;
    }

    /* Rent Badge */
    .rent-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }

    /* Financial Summary */
    .financial-summary {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 11px;
    }

    .fin-item {
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }

    .fin-item .label {
        color: #999;
        font-weight: 500;
    }

    .fin-item .value {
        color: #2c3e50;
        font-weight: 600;
        color: #667eea;
    }

    /* Agreement Info (reuses financial-item styling) */
    .agreement-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 11px;
    }

    .agreement-info .financial-item {
        padding-left: 10px; /* show left border clearly */
    }

    /* old agr-item rules removed */

    /* Action Buttons */
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 7px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-view {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 15px;
        display: block;
    }

    .empty-state p {
        font-size: 16px;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        padding: 20px 0;
    }

    .pagination {
        display: flex;
        gap: 5px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        text-decoration: none;
        color: #667eea;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-color: transparent;
    }

    .pagination .active span {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-color: transparent;
    }

    .pagination .disabled span {
        color: #ccc;
        cursor: not-allowed;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .col-address,
        .col-financial {
            width: auto;
        }

        .address-info span {
            display: none;
        }

        .tenant-analytics-grid {
            grid-template-columns: 1fr;
        }

        .tenant-due-chart {
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .tenants-report-container {
            padding: 15px;
        }

        .tenant-analytics {
            padding: 16px;
        }

        .tenant-analytics-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .tenant-shop-count {
            width: 100%;
        }

        .tenant-due-metrics {
            grid-template-columns: 1fr;
        }

        .filter-group {
            flex-direction: column;
        }

        .search-input,
        .filter-select,
        .btn-filter {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .tenants-table thead,
        .tenants-table tbody,
        .tenants-table tr {
            display: block;
            width: 100%;
        }

        .tenants-table tbody tr {
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .tenants-table td {
            display: block;
            padding: 10px;
            text-align: right;
            padding-left: 50%;
            position: relative;
        }

        .tenants-table td:before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            color: #667eea;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection