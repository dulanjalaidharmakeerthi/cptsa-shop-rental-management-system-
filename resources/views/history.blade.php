@php
    $layout = 'layouts.app';
    if (auth()->check() && auth()->user()->position === 'Cashier') {
        $layout = 'layouts.chap';
    }
@endphp
@extends($layout)
@section('title', 'Payment History')

@section('content')
<style>
    .payment-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(30, 64, 175, 0.15);
    }

    .payment-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #fff;
    }

    .search-container {
        background: white;
        padding: 24px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e8eef5;
    }

    .search-container h3 {
        margin-top: 0;
        margin-bottom: 16px;
        color: #1e40af;
        font-size: 16px;
        font-weight: 600;
    }

    .search-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }

    .search-form input {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .search-form input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .search-form input[type="text"] {
        flex: 1;
        min-width: 200px;
    }

    .search-form input[type="number"] {
        width: 120px;
    }

    .search-form label {
        color: #475569;
        font-weight: 500;
        font-size: 14px;
    }

    .search-form button {
        padding: 10px 24px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .search-form button:hover {
        background: #2563eb;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .shop-card {
        background: white;
        border: 1px solid #e8eef5;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .shop-card-header {
        border-bottom: 2px solid #eff6ff;
        padding-bottom: 16px;
        margin-bottom: 16px;
    }

    .shop-card-header .owner-name {
        font-size: 18px;
        font-weight: 700;
        color: #1e40af;
        margin: 0 0 8px 0;
    }

    .shop-card-header .bus-stand {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    .shop-card-header .bus-stand strong {
        color: #334155;
    }

    .payments-table-wrapper {
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid #e8eef5;
    }

    .payments-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .payments-table thead {
        background: linear-gradient(90deg, #eff6ff 0%, #f0f9ff 100%);
        border-bottom: 2px solid #3b82f6;
    }

    .payments-table thead tr {
        border: none;
    }

    .payments-table th {
        padding: 16px 14px;
        text-align: left;
        font-weight: 700;
        color: #1e40af;
        white-space: nowrap;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .payments-table tbody tr {
        border-bottom: 1px solid #e8eef5;
        transition: background-color 0.2s ease;
    }

    .payments-table tbody tr:hover {
        background-color: #f8fbff;
    }

    .payments-table tbody tr:last-child {
        border-bottom: none;
    }

    .payments-table td {
        padding: 14px;
        color: #334155;
    }

    .amount-column {
        font-weight: 600;
        color: #1e40af;
    }

    .slip-images {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .slip-images a img {
        width: 56px;
        height: 48px;
        object-fit: cover;
        border-radius: 6px;
        border: 2px solid #e8eef5;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .slip-images a img:hover {
        border-color: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        transform: scale(1.05);
    }

    .slip-link {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        padding: 6px 10px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .slip-link:hover {
        background-color: #eff6ff;
        color: #1e40af;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-due {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-paid {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-late {
        background-color: #fef3c7;
        color: #92400e;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 10px;
        border: 1px dashed #cbd5e1;
        color: #64748b;
    }

    .empty-state p {
        font-size: 16px;
        margin: 0;
    }

    .no-payments-message {
        text-align: center;
        padding: 24px;
        color: #64748b;
        font-style: italic;
    }

    .history-page {
        --history-ink: #172554;
        --history-muted: #64748b;
        --history-border: #e2e8f0;
        --history-blue: #2563eb;
        max-width: 1280px;
        margin: 0 auto;
        padding-bottom: 36px;
    }

    .history-page,
    .history-page * {
        box-sizing: border-box;
    }

    .history-page .payment-header {
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        min-height: 150px;
        padding: 30px 36px;
        margin-bottom: 22px;
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 18px;
        background: linear-gradient(120deg, #172554 0%, #1d4ed8 58%, #38bdf8 130%);
        box-shadow: 0 14px 34px rgba(30, 64, 175, .18);
    }

    .history-page .payment-header::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        right: 4%;
        top: -115px;
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 50%;
        box-shadow: 0 0 0 24px rgba(255,255,255,.06), 0 0 0 50px rgba(255,255,255,.04);
        pointer-events: none;
    }

    .history-page .history-heading {
        position: relative;
        z-index: 1;
    }

    .history-page .history-eyebrow {
        display: block;
        margin-bottom: 8px;
        color: #bfdbfe;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .history-page .payment-header h1 {
        font-size: clamp(24px, 3vw, 34px);
        letter-spacing: -.035em;
    }

    .history-page .history-subtitle {
        margin: 9px 0 0;
        color: rgba(255,255,255,.82);
        font-size: 14px;
    }

    .history-page .history-result-count {
        position: relative;
        z-index: 1;
        flex: 0 0 auto;
        min-width: 132px;
        padding: 14px 18px;
        border: 1px solid rgba(255,255,255,.24);
        border-radius: 14px;
        background: rgba(255,255,255,.12);
        color: white;
        text-align: center;
        backdrop-filter: blur(8px);
    }

    .history-page .history-result-count strong {
        display: block;
        font-size: 27px;
        line-height: 1.1;
    }

    .history-page .history-result-count span {
        display: block;
        margin-top: 4px;
        color: rgba(255,255,255,.78);
        font-size: 12px;
    }

    .history-page .search-container {
        padding: 24px;
        margin-bottom: 22px;
        border: 1px solid var(--history-border);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15,23,42,.045);
    }

    .history-page .history-filter-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
    }

    .history-page .search-container h3 {
        margin: 0 0 5px;
        color: var(--history-ink);
        font-size: 18px;
        letter-spacing: -.02em;
    }

    .history-page .search-form {
        display: block;
    }

    .history-page .history-filter-help {
        margin: 0;
        color: var(--history-muted);
        font-size: 13px;
    }

    .history-page .history-filter-grid {
        display: grid;
        width: 100%;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .history-page .history-field {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 7px;
    }

    .history-page .history-field label {
        min-height: 18px;
        line-height: 18px;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
    }

    .history-page .history-field input,
    .history-page .history-field select {
        width: 100%;
        height: 44px;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #d6deea !important;
        border-radius: 9px !important;
        background: #fff;
        color: #1e293b;
        font: inherit;
        font-size: 13px !important;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .history-page .history-field input {
        flex: 0 1 auto;
    }

    .history-page .history-field input::placeholder {
        color: #94a3b8;
    }

    .history-page .history-field input:focus,
    .history-page .history-field select:focus {
        outline: 0;
        border-color: var(--history-blue) !important;
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }

    .history-page .history-filter-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        padding-top: 17px;
        border-top: 1px solid #edf1f6;
    }

    .history-page .history-action-button {
        display: inline-flex;
        min-height: 41px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 16px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease, transform .18s ease;
    }

    .history-page .history-action-button:hover {
        transform: translateY(-1px);
    }

    .history-page .history-action-primary {
        background: var(--history-blue);
        color: #fff;
    }

    .history-page .history-action-primary:hover {
        background: #1d4ed8;
    }

    .history-page .history-action-secondary {
        border-color: #dbe3ef;
        background: #fff;
        color: #334155;
    }

    .history-page .history-action-secondary:hover {
        border-color: #b8c7dc;
        background: #f8fafc;
    }

    .history-page .history-export-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 14px;
    }

    .history-page .history-export-button {
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .history-page .history-export-button:hover {
        border-color: #93c5fd;
        background: #eff6ff;
    }

    .history-page .history-filter-chip {
        padding: 5px 9px;
        border: 1px solid #dbeafe;
        border-radius: 999px;
        background: #eff6ff;
        color: #1e40af;
        font-size: 11px;
        font-weight: 600;
    }

    .history-page .shop-card {
        padding: 0;
        margin-bottom: 18px;
        border: 1px solid var(--history-border);
        border-radius: 16px;
        box-shadow: 0 6px 22px rgba(15,23,42,.045);
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .history-page .shop-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15,23,42,.08);
    }

    .history-page .shop-card-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px 18px;
        padding: 19px 22px;
        margin: 0;
        border-bottom: 1px solid #edf1f6;
        background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
    }

    .history-page .shop-card-header .owner-name {
        flex: 1 1 100%;
        margin: 0;
        color: var(--history-ink);
        font-size: 17px;
    }

    .history-page .shop-card-header .shop-info,
    .history-page .shop-card-header .bus-stand {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin: 0 !important;
        color: var(--history-muted);
        font-size: 12px;
        font-weight: 500 !important;
    }

    .history-page .shop-card-header strong {
        color: #334155;
    }

    .history-page .payments-table-wrapper {
        margin: 16px 18px 0;
        border-color: #e8edf4;
    }

    .history-page .payments-table {
        min-width: 760px;
        font-size: 13px;
    }

    .history-page .payments-table thead {
        background: #f5f8fd;
        border-bottom: 1px solid #e4eaf3;
    }

    .history-page .payments-table th {
        padding: 12px 13px;
        color: #52627a;
        font-size: 10px;
        letter-spacing: .07em;
    }

    .history-page .payments-table td {
        padding: 12px 13px;
        vertical-align: middle;
        font-size: 12px;
    }

    .history-page .payments-table tbody tr:last-child {
        border-bottom: 0;
    }

    .history-page .amount-column {
        color: #1e40af;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .history-page .status-badge {
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        letter-spacing: .04em;
    }

    .history-page .history-payment-summary {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 15px 18px 18px;
    }

    .history-page .history-summary-item {
        min-width: 150px;
        padding: 10px 13px;
        border: 1px solid #e7edf5;
        border-radius: 10px;
        background: #f8fafc;
    }

    .history-page .history-summary-label {
        display: block;
        margin-bottom: 4px;
        color: var(--history-muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .history-page .history-summary-value {
        color: var(--history-ink);
        font-size: 14px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }

    .history-page .history-summary-value small {
        color: var(--history-muted);
        font-size: 10px;
        font-weight: 600;
    }

    .history-page .history-empty {
        padding: 48px 22px;
        border-style: solid;
        border-color: var(--history-border);
        box-shadow: 0 8px 24px rgba(15,23,42,.04);
    }

    .history-page .history-empty-icon {
        display: grid;
        width: 54px;
        height: 54px;
        place-items: center;
        margin: 0 auto 14px;
        border-radius: 16px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 24px;
    }

    .history-page .history-empty-icon svg {
        width: 23px;
        height: 23px;
        fill: none;
        stroke: currentColor;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 1.8;
    }

    .history-page .history-empty h2 {
        margin: 0 0 7px;
        color: var(--history-ink);
        font-size: 17px;
    }

    .history-page .history-empty p {
        color: var(--history-muted);
        font-size: 13px;
    }

    @media (max-width: 1100px) {
        .history-page .history-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .history-page .payment-header {
            min-height: 0;
            padding: 23px 20px;
            border-radius: 14px;
        }

        .history-page .history-result-count {
            min-width: auto;
            padding: 10px 12px;
        }

        .history-page .history-result-count strong {
            font-size: 21px;
        }

        .history-page .history-result-count span {
            font-size: 10px;
        }

        .history-page .search-container {
            padding: 18px;
            border-radius: 13px;
        }

        .history-page .history-filter-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .history-page .history-filter-actions {
            flex-wrap: wrap;
        }

        .history-page .history-action-button {
            flex: 1 1 auto;
        }

        .history-page .shop-card-header {
            padding: 16px;
        }

        .history-page .payments-table-wrapper {
            margin: 12px 12px 0;
        }

        .history-page .history-payment-summary {
            padding: 12px;
        }

        .history-page .history-summary-item {
            flex: 1 1 135px;
            min-width: 0;
        }
    }
</style>

<div class="history-page">
<!-- Header Section -->
<div class="payment-header">
    <div class="history-heading">
        <span class="history-eyebrow">Rent &amp; receipts</span>
        <h1>Payment history</h1>
        <p class="history-subtitle">Find payments by shop, bus stand, owner, or ID number.</p>
    </div>
    <div class="history-result-count">
        <strong>{{ $shops->count() }}</strong>
        <span>{{ \Illuminate\Support\Str::plural('shop', $shops->count()) }} found</span>
    </div>
</div>

<!-- Search Form -->
<div class="search-container">
    @php
        $activeHistoryFilterLabels = [
            'Shop' => $filters['shop_number'] ?? '',
            'Bus stand' => $busStands->firstWhere('id', $filters['bus_stand_id'] ?? null)?->name,
            'Owner' => $filters['owner_name'] ?? '',
            'ID / NIC' => $filters['owner_nic'] ?? '',
            'From' => request('year_from'),
            'To' => request('year_to'),
            'Status' => request('status') ? ucfirst(request('status')) : '',
        ];
        $activeHistoryFilterCount = collect($activeHistoryFilterLabels)->filter(fn ($value) => filled($value))->count();
    @endphp
    <div class="history-filter-heading">
        <div>
            <h3>Filter payment records</h3>
            <p class="history-filter-help">Combine filters to narrow the results.</p>
        </div>
        @if($activeHistoryFilterCount)
            <span class="history-filter-chip">{{ $activeHistoryFilterCount }} {{ \Illuminate\Support\Str::plural('filter', $activeHistoryFilterCount) }} active</span>
        @endif
    </div>
    <form method="get" action="{{ route('history') }}" class="search-form">
        <div class="history-filter-grid">
        <div class="history-field">
        <label for="history-shop-number">Shop:</label>
        <input type="text" id="history-shop-number" name="shop_number"
               placeholder="Shop number or name"
               value="{{ $filters['shop_number'] ?? '' }}">
        </div>

        <div class="history-field">
        <label for="history-bus-stand">Bus stand:</label>
        <select id="history-bus-stand" name="bus_stand_id">
            <option value="">All bus stands</option>
            @foreach($busStands as $busStand)
                <option value="{{ $busStand->id }}" {{ (string) ($filters['bus_stand_id'] ?? '') === (string) $busStand->id ? 'selected' : '' }}>
                    {{ $busStand->name }}
                </option>
            @endforeach
        </select>
        </div>

        <div class="history-field">
        <label for="history-owner-name">User / owner name:</label>
        <input type="text" id="history-owner-name" name="owner_name"
               placeholder="Enter name"
               value="{{ $filters['owner_name'] ?? '' }}">
        </div>

        <div class="history-field">
        <label for="history-owner-nic">ID / NIC number:</label>
        <input type="text" id="history-owner-nic" name="owner_nic"
               placeholder="Enter ID number"
               value="{{ $filters['owner_nic'] ?? '' }}">
        </div>

        <div class="history-field">
         <label for="year-from">From:</label>
         <input type="date" id="year-from" name="year_from"
             value="{{ request('year_from') }}"
             placeholder="From">
        </div>

        <div class="history-field">
         <label for="year-to">To:</label>
         <input type="date" id="year-to" name="year_to"
             value="{{ request('year_to') }}"
             placeholder="To">
        </div>

         <!-- Amount Min/Max filters removed -->

         <!-- Interest filters removed as requested -->

        <div class="history-field">
        <label for="status">Payment status:</label>
        <select name="status" id="status">
            <option value="" {{ request('status')=='' ? 'selected' : '' }}>All</option>
            <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>Paid</option>
            <option value="due" {{ request('status')=='due' ? 'selected' : '' }}>Due</option>
            <option value="late" {{ request('status')=='late' ? 'selected' : '' }}>Late</option>
        </select>
        </div>
        </div>

        <div class="history-filter-actions">
            <button type="submit" class="history-action-button history-action-primary">Apply filters</button>
            @if($activeHistoryFilterCount)
                <a href="{{ route('history') }}" class="history-action-button history-action-secondary">Clear all</a>
            @endif
        </div>
    </form>

    @unless (strtolower(auth()->user()->position ?? '') === 'accountant')
    <div class="history-export-row">
        <form method="get" action="{{ route('history.export') }}">
            <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">
            <input type="hidden" name="shop_number" value="{{ $filters['shop_number'] ?? '' }}">
            <input type="hidden" name="bus_stand_id" value="{{ $filters['bus_stand_id'] ?? '' }}">
            <input type="hidden" name="owner_name" value="{{ $filters['owner_name'] ?? '' }}">
            <input type="hidden" name="owner_nic" value="{{ $filters['owner_nic'] ?? '' }}">
            <input type="hidden" name="year_from" value="{{ request('year_from') }}">
            <input type="hidden" name="year_to" value="{{ request('year_to') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="shop_id" value="">
            <button type="submit" class="history-action-button history-action-secondary history-export-button">
                Export results to Excel
            </button>
        </form>
    </div>
    @endunless
</div>

@if($shops->isEmpty())
    <div class="empty-state history-empty">
        <div class="history-empty-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.3"></circle><path d="m15.5 15.5 4 4"></path></svg>
        </div>
        <h2>No matching shops</h2>
        <p>Try changing or clearing one or more filters to see payment records.</p>
    </div>
@else

    @foreach($shops as $shop)

        <!-- Shop Card -->
        <div class="shop-card">
            <!-- Shop Header -->
            <div class="shop-card-header">
                <p class="owner-name">{{ $shop->shop_owner_name }}</p>
                <p class="shop-info">
                    <strong>Shop:</strong>
                    @if($shop->shop_number || $shop->shop_name)
                        {{ $shop->shop_number ?? '-' }}
                        @if($shop->shop_name) - {{ $shop->shop_name }} @endif
                    @else
                        -
                    @endif
                </p>
                <p class="bus-stand">
                    <strong>Bus Stand:</strong>
                    {{ $shop->busStand ? ($shop->busStand->name ?? $shop->bus_stand_id) : $shop->bus_stand_id }}
                </p>
            </div>

            <!-- Payments Table -->

                @php
                    // Prepare filters from request
                    $fromDate = request('year_from') ? \Carbon\Carbon::parse(request('year_from'))->startOfDay() : null;
                    $toDate = request('year_to') ? \Carbon\Carbon::parse(request('year_to'))->endOfDay() : null;
                    $statusFilter = request('status');

                    $filteredPayments = $shop->payments->filter(function($p) use ($fromDate, $toDate, $statusFilter) {
                        // choose date for filtering: paid_at if exists, otherwise created_at
                        $date = $p->paid_at ? \Carbon\Carbon::parse($p->paid_at) : (\Carbon\Carbon::parse($p->created_at) ?? null);

                        if($fromDate && $date && $date->lt($fromDate)) return false;
                        if($toDate && $date && $date->gt($toDate)) return false;

                        // amount filtering removed

                        // interest filtering removed

                        if($statusFilter) {
                            if($statusFilter === 'paid' && !$p->paid_at) return false;
                            if($statusFilter === 'due' && $p->paid_at) return false;
                            if($statusFilter === 'late') {
                                if(!$p->paid_at) return false;
                                $day = \Carbon\Carbon::parse($p->paid_at)->day;
                                if($day <= 28) return false;
                            }
                        }

                        return true;
                    });

                    $paidPayments = $filteredPayments->filter(fn($x) => (bool) $x->paid_at);
                    $duePayments = $filteredPayments->filter(fn($x) => !$x->paid_at);

                    $paidTotal = $paidPayments->reduce(function($carry, $p) { return $carry + (float) ($p->amount ?? 0); }, 0);
                    $paidCount = $paidPayments->count();

                    // Total interest for *all* filtered payments (paid or due)
                    $interestTotal = $filteredPayments->reduce(function($carry, $p) { return $carry + (float) ($p->interest ?? 0); }, 0);

                    // Sum interest for unpaid (due) payments, handle nulls and strings
                    $dueInterestTotal = $duePayments->reduce(function($carry, $p) { return $carry + (float) ($p->interest ?? 0); }, 0);
                    $interestBalance = $dueInterestTotal;
                @endphp

                <div class="payments-table-wrapper">
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Paid Date</th>
                            <th>Receipt</th>
                            <th>Amount</th>
                            <th>Interest</th>
                            <th>Payable Month</th>
                            <th>Payment Slip</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    @if($filteredPayments->isNotEmpty())
                        @foreach($filteredPayments as $payment)
                            <tr>
                                <td>
                                    @if($payment->paid_at)
                                        {{ $payment->paid_at->format('d M, Y') }}<br>
                                        <small style="color: #94a3b8;">{{ $payment->paid_at->format('H:i A') }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $payment->receipt_number ?? '-' }}</td>
                                <td class="amount-column">Rs. {{ number_format($payment->amount ?? 0, 2) }}</td>
                                <td>{{ $payment->interest ? 'Rs. ' . number_format($payment->interest, 2) : '-' }}</td>
                                <td>{{ $payment->payable_months_display ?? '-' }}</td>
                                <td>
                                    @if($payment->slips->isNotEmpty())
                                        <div class="slip-images">
                                            @foreach($payment->slips as $slip)
                                                @php $url = Storage::url($slip->file_path); @endphp
                                                @if(str_starts_with($slip->mime_type ?? '', 'image'))
                                                    <a href="{{ $url }}" target="_blank" title="View slip">
                                                        <img src="{{ $url }}" alt="Payment slip">
                                                    </a>
                                                @else
                                                    <a href="{{ $url }}" target="_blank" class="slip-link">
                                                        {{ $slip->original_name ?? 'View File' }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color: #94a3b8;">-</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $status = 'Due';
                                        $statusClass = 'status-due';
                                        
                                        if($payment->amount > 0 && $payment->paid_at){
                                            $day = \Carbon\Carbon::parse($payment->paid_at)->day;
                                            if($day <= 28){
                                                $status = 'Paid'; 
                                                $statusClass = 'status-paid';
                                            } else {
                                                $status = 'Late'; 
                                                $statusClass = 'status-late';
                                            }
                                        }
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="no-payments-message">
                                No payment records available for this owner.
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>

            <div class="history-payment-summary">
                <div class="history-summary-item">
                    <span class="history-summary-label">Total paid</span>
                    <span class="history-summary-value">Rs. {{ number_format($paidTotal ?? 0, 2) }} <small>({{ $paidCount }} {{ \Illuminate\Support\Str::plural('payment', $paidCount) }})</small></span>
                </div>
                <div class="history-summary-item">
                    <span class="history-summary-label">Interest total</span>
                    <span class="history-summary-value">Rs. {{ number_format($interestTotal ?? 0, 2) }}</span>
                </div>
                @unless (strtolower(auth()->user()->position ?? '') === 'accountant')
                <div style="margin-left:auto;">
                    <form method="get" action="{{ route('history.export') }}">
                        <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                        <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">
                        <input type="hidden" name="shop_number" value="{{ $filters['shop_number'] ?? '' }}">
                        <input type="hidden" name="bus_stand_id" value="{{ $filters['bus_stand_id'] ?? '' }}">
                        <input type="hidden" name="owner_name" value="{{ $filters['owner_name'] ?? '' }}">
                        <input type="hidden" name="owner_nic" value="{{ $filters['owner_nic'] ?? '' }}">
                        <input type="hidden" name="year_from" value="{{ request('year_from') }}">
                        <input type="hidden" name="year_to" value="{{ request('year_to') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <button type="submit" class="history-action-button history-action-secondary history-export-button">Export shop</button>
                    </form>
                </div>
                @endunless

            </div>
        </div>

    @endforeach
@endif
</div>

@endsection
