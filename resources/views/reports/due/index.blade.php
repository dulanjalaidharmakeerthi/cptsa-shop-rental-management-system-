@extends('layouts.app')

@section('content')
<div class="due-report-container">
    <div class="due-header">
        <div class="header-content">
            <h1><i class="fas fa-money-bill-wave"></i> Due Reports</h1>
            <p>Month-wise due balances for all tenants (2023 to current month)</p>
        </div>
    </div>

<!-- Search Section -->
    <div class="search-section">
        <form action="{{ route('reports.due.index') }}" method="GET" class="search-form">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="q" placeholder="Search by tenant name, NIC, shop name, or shop number..."
                       value="{{ request('q') }}" class="search-input">

                <select name="bus_stand_id" class="year-select">
                    <option value="">All Bus Stands</option>
                    @foreach($busStands as $busStand)
                        <option value="{{ $busStand->id }}" {{ request('bus_stand_id') == $busStand->id ? 'selected' : '' }}>
                            {{ $busStand->name }}
                        </option>
                    @endforeach
                </select>

                <select name="shop_id" class="year-select">
                    <option value="">All Shops</option>
                    @foreach($shopOptions as $shopOption)
                        <option value="{{ $shopOption->id }}" {{ request('shop_id') == $shopOption->id ? 'selected' : '' }}>
                            {{ $shopOption->shop_number }} - {{ $shopOption->shop_name }}
                        </option>
                    @endforeach
                </select>

                <select name="year" class="year-select">
                    <option value="">All years</option>
                    @foreach($allYears as $year)
                        <option value="{{ $year }}" {{ (int)$selectedYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>

            @if(request('q'))
                <div class="active-search">
                    <span><i class="fas fa-check-circle"></i> Searching for: "{{ request('q') }}"</span>
                    <a href="{{ route('reports.due.index') }}" class="clear-search">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Due Data Table -->
    <div class="table-wrapper">
        <div class="table-responsive">
            @forelse($dueData as $shopData)
                <div class="shop-due-section">
                    <h3 class="shop-title">
                        <i class="fas fa-store"></i>
                        {{ $shopData['shop']->shop_name }} ({{ $shopData['shop']->shop_number }})
                    </h3>

                    <div class="shop-summary-box">
                        <div class="summary-grid">
                            <div class="summary-item">
                                <span class="summary-label">Shop Number</span>
                                <span class="summary-value">{{ $shopData['summary']['shop_number'] ?? $shopData['shop']->shop_number }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Shop Name</span>
                                <span class="summary-value">{{ $shopData['summary']['shop_name'] ?? $shopData['shop']->shop_name }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Bus Stand</span>
                                <span class="summary-value">{{ $shopData['summary']['bus_stand'] ?? ($shopData['shop']->busStand?->name ?? 'N/A') }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Owner Name</span>
                                <span class="summary-value">{{ $shopData['summary']['owner_name'] ?? $shopData['shop']->shop_owner_name }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">ID Number</span>
                                <span class="summary-value">{{ $shopData['summary']['owner_nic'] ?? $shopData['shop']->shop_owner_nic }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Monthly Rent</span>
                                <span class="summary-value">Rs {{ number_format($shopData['summary']['monthly_rent'] ?? ($shopData['shop']->monthly_rent ?? 0), 2) }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Balance</span>
                                <span class="summary-value">Rs {{ number_format($shopData['summary']['balance_amount'] ?? ($shopData['shop']->balance_amount ?? 0), 2) }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Due Balance</span>
                                <span class="summary-value">Rs {{ number_format($shopData['summary']['due_balance'] ?? ($shopData['shop']->due_payment ?? 0), 2) }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Due Interest</span>
                                <span class="summary-value">Rs {{ number_format($shopData['summary']['due_interest'] ?? ($shopData['shop']->due_interest ?? 0), 2) }}</span>
                            </div>
                        </div>
                    </div>

                    @foreach($shopData['years'] as $yearData)
                        <div class="year-section">
                            <div class="year-section-header">
                                <h4 class="year-title">{{ $yearData['year'] }}</h4>
                                <form action="{{ route('reports.due.index') }}" method="GET" class="export-form">
                                    <input type="hidden" name="q" value="{{ request('q') }}">
                                    <input type="hidden" name="year" value="{{ $yearData['year'] }}">
                                    <input type="hidden" name="shop_id" value="{{ $shopData['shop']->id }}">
                                    <input type="hidden" name="export" value="excel">
                                    <button type="submit" class="btn-export">
                                        <i class="fas fa-file-excel"></i> Download Excel
                                    </button>
                                </form>
                            </div>
                            <table class="due-table">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Monthly Rent</th>
                                        <th>Rate (%)</th>
                                        <th>Monthly Balance</th>
                                        <th>Paid Interest</th>
                                        <th>Total Paid</th>
                                        <th>Paid Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($yearData['months'] as $monthData)
                                        <tr class="{{ $monthData['due'] > 0 ? 'due-row' : 'paid-row' }}">
                                            <td class="month-cell">{{ $monthData['month'] }}</td>
                                            <td class="rent-cell">Rs {{ number_format($monthData['rent'], 2) }}</td>
                                            <td class="rate-cell">{{ number_format($monthData['rate'] ?? 0, 2) }}%</td>
                                            <td class="balance-cell">Rs {{ number_format($monthData['balance'], 2) }}</td>
                                            <td class="interest-cell">Rs {{ number_format($monthData['interest'], 2) }}</td>
                                            <td class="paid-total-cell">Rs {{ number_format($monthData['paid_with_interest'] ?? 0, 2) }}</td>
                                            <td class="date-cell">
                                                @if($monthData['paid_date'])
                                                    <span class="date-badge">{{ $monthData['paid_date'] }}</span>
                                                @else
                                                    <span class="no-date">—</span>
                                                @endif
                                            </td>
                                            <td class="status-cell {{ strtolower($monthData['status']) }}-status">
                                                {{ $monthData['status'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No shops found</h3>
                    <p>Try adjusting your search filters or <a href="{{ route('reports.due.index') }}">clear all filters</a> to see all results.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.due-report-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.due-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.header-content h1 {
    margin: 0;
    font-size: 2.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-content p {
    margin: 10px 0 0 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

/* Search Section */
.search-section {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.search-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 760px;
}

.year-select {
    padding: 10px 12px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    background: white;
    font-size: 14px;
    color: #495057;
}

.search-box i {
    color: #667eea;
    font-size: 16px;
}

.search-input {
    flex: 1;
    padding: 12px 16px;
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

.btn-search {
    padding: 12px 20px;
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
    white-space: nowrap;
}

.btn-search:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.active-search {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #f0f2f5;
    border-radius: 8px;
    font-size: 14px;
    color: #2c3e50;
}

.active-search span {
    font-weight: 600;
    color: #667eea;
    display: flex;
    align-items: center;
    gap: 8px;
}

.clear-search {
    color: #dc3545;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
}

.clear-search:hover {
    text-decoration: underline;
}

.year-section {
    margin-bottom: 30px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(55, 63, 104, 0.08);
    padding: 20px;
}

.year-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    gap: 12px;
}

.year-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
}

.export-form {
    margin: 0;
}

.btn-export {
    padding: 10px 16px;
    background: #1d7a1d;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-export:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(29, 122, 29, 0.18);
}

}

.no-results i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-results h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #495057;
}

.no-results p {
    font-size: 1rem;
    margin-bottom: 15px;
}

.no-results a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.no-results a:hover {
    text-decoration: underline;
}

.shop-due-section {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    overflow: hidden;
}

.shop-title {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    margin: 0;
    padding: 20px;
    font-size: 1.3rem;
    font-weight: 600;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.shop-summary-box {
    background: linear-gradient(135deg, #f5f7ff 0%, #f4f6fb 100%);
    border-bottom: 1px solid #e7ebf3;
    padding: 18px 20px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
}

.summary-item {
    background: #ffffff;
    border: 1px solid #edf0f5;
    border-radius: 10px;
    padding: 12px 14px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.summary-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 6px;
}

.summary-value {
    color: #1f2937;
    font-weight: 700;
    font-size: 14px;
}

.year-section {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.year-section:last-child {
    border-bottom: none;
}

.year-title {
    color: #333;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.due-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.due-table th {
    background: #f8f9fa;
    color: #333;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.due-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #dee2e6;
}

.due-row {
    background: #fff5f5;
}

.paid-row {
    background: #f8fff8;
}

.month-cell {
    font-weight: 600;
    color: #495057;
}

.rent-cell, .balance-cell, .interest-cell, .paid-cell, .due-cell {
    font-family: 'Courier New', monospace;
    text-align: right;
}

.date-cell {
    text-align: center;
}

.date-badge {
    display: inline-block;
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.no-date {
    color: #999;
    font-size: 14px;
}

.interest-cell {
    color: #ff9800;
    font-weight: 500;
}

.balance-cell {
    color: #f44336;
    font-weight: 600;
}

.due-amount {
    color: #dc3545;
    font-weight: 600;
}

.no-due {
    color: #28a745;
    font-weight: 600;
}

.status-cell {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    text-align: center;
}

.status-cell.due-status {
    color: #dc3545;
}

.status-cell.paid-status {
    color: #28a745;
}

@media (max-width: 768px) {
    .due-report-container {
        padding: 10px;
    }

    .due-header {
        padding: 20px;
    }

    .header-content h1 {
        font-size: 2rem;
    }

    .shop-title {
        font-size: 1.1rem;
        padding: 15px;
    }

    .year-section {
        padding: 15px;
    }

    .due-table {
        font-size: 0.9rem;
    }

    .due-table th,
    .due-table td {
        padding: 8px;
    }
}
</style>
@endsection