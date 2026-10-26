@extends('layouts.app')

@section('content')
<style>
    /* Global constraint for oversized elements */
    svg { max-width: 100%; height: auto; }
    img { max-width: 100%; height: auto; }
    
    /* Keep pagination arrows at reasonable size */
    .pagination svg,
    .pagination a svg,
    .pagination span svg {
        max-width: 16px !important;
        max-height: 16px !important;
        width: 16px !important;
        height: 16px !important;
        font-size: 14px !important;
        line-height: 1 !important;
    }

    /* Keep pagination text at readable size */
    .pagination,
    .pagination a,
    .pagination span {
        font-size: 14px !important;
        line-height: 1.4 !important;
    }

    .pagination a::before,
    .pagination a::after,
    .pagination span::before,
    .pagination span::after {
        font-size: 14px !important;
    }
</style>
<div class="shops-report-container">
    <div class="shops-header">
        <div class="header-content">
            <h1><i class="fas fa-store"></i> Shop Details Report</h1>
            <p>Comprehensive shop information and management</p>
        </div>
        
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="filter-section">
        <form action="{{ route('reports.shops.index') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="Search by name, shop number..." 
                           value="{{ request('q') }}" class="search-input">
                </div>

                <select name="bus_stand_id" class="filter-select">
                    <option value="">All Bus Stands</option>
                    @foreach($busStands as $busStand)
                        <option value="{{ $busStand->id }}" 
                                {{ request('bus_stand_id') == $busStand->id ? 'selected' : '' }}>
                            {{ $busStand->name }}
                        </option>
                    @endforeach
                </select>

                <select name="shop_id" class="filter-select">
                    <option value="">All Shops</option>
                    @foreach($shopOptions ?? [] as $shopOption)
                        <option value="{{ $shopOption->id }}" {{ request('shop_id') == $shopOption->id ? 'selected' : '' }}>
                            {{ $shopOption->shop_number }} - {{ $shopOption->shop_name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-filter">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Shops Table -->
    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="shops-table">
                <thead>
                    <tr>
                        <th class="col-id">#</th>
                        <th class="col-shop">Shop Info</th>
                        <th class="col-owner">Owner Details</th>
                        <th class="col-location">Location</th>
                        <th class="col-financial">Financial</th>
                        <th class="col-agreement">Agreement</th>
                        <th class="col-remarks">Remarks</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shops as $shop)
                        <tr class="table-row">
                            <td class="col-id">
                                <span class="badge-id">{{ $shop->shop_number }}</span>
                            </td>
                            
                            <td class="col-shop">
                                <div class="shop-info">
                                    <div class="shop-name">{{ $shop->shop_name ?? 'N/A' }}</div>
                                    <div class="shop-meta">
                                        @if($shop->busStand)
                                            <span class="badge badge-bus">
                                                <i class="fas fa-bus"></i> {{ $shop->busStand->name }}
                                            </span>
                                        @endif
                                        @if($shop->image_path)
                                            <span class="badge badge-image">
                                                <i class="fas fa-image"></i> Image
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="col-owner">
                                <div class="owner-info">
                                    <div class="owner-name">
                                        <i class="fas fa-user-circle"></i> {{ $shop->shop_owner_name }}
                                    </div>
                                    <div class="owner-contact">
                                        <div class="contact-item">
                                            <i class="fas fa-id-card"></i> {{ $shop->shop_owner_nic }}
                                        </div>
                                        <div class="contact-item">
                                            <i class="fas fa-phone"></i> {{ $shop->shop_owner_phone }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="col-location">
                                <div class="location-info">
                                    @if($shop->location || $shop->shop_address)
                                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                            @if(str_starts_with(trim($shop->location ?? ''), 'http://') || str_starts_with(trim($shop->location ?? ''), 'https://'))
                                                <button type="button" class="location-btn" 
                                                        onclick="openGoogleMapsLinkDirect('{{ $shop->location }}')">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <span>{{ $shop->location ?? $shop->shop_address }}</span>
                                                </button>
                                                <span class="badge-map-link" title="Google Maps Link - Opens in Google Maps">
                                                    <i class="fas fa-external-link-alt"></i> MAP LINK
                                                </span>
                                            @else
                                                <button type="button" class="location-btn" 
                                                        onclick="openMapModal('{{ $shop->shop_address }} {{ $shop->location }}', '{{ $shop->shop_name }}', '{{ $shop->shop_owner_name }}')">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <span>{{ $shop->location ?? $shop->shop_address }}</span>
                                                </button>
                                            @endif
                                            <span class="badge-ro">RO</span>
                                        </div>
                                    @else
                                        <span style="color: #999;">No location</span>
                                    @endif
                                </div>
                            </td>

                            <td class="col-financial">
                                <div class="financial-info">
                                    <div class="financial-item">
                                        <span class="label">Rent:</span>
                                        <span class="value">Rs {{ number_format($shop->monthly_rent, 2) }}</span>
                                    </div>
                                    <div class="financial-item">
                                        <span class="label">Rate:</span>
                                        <span class="value">{{ $shop->rate ?? '0' }}%</span>
                                    </div>
                                    <div class="financial-item">
                                        <span class="label">Advance:</span>
                                        <span class="value">Rs {{ number_format($shop->advance_payment ?? 0, 2) }}</span>
                                    </div>
                                    @if($shop->opening_balance)
                                        <div class="financial-item">
                                            <span class="label">Opening Balance:</span>
                                            <span class="value">Rs {{ number_format($shop->opening_balance, 2) }}</span>
                                        </div>
                                    @endif
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
                                            <span class="value">{{ $shop->agreement_start->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                    @if($shop->agreement_end)
                                        <div class="financial-item">
                                            <span class="label">End:</span>
                                            <span class="value">{{ $shop->agreement_end->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="col-remarks">
                                <div class="remarks-info">
                                    @if($shop->remarks)
                                        <span class="remarks-text" title="{{ $shop->remarks }}">{{ Str::limit($shop->remarks, 100) }}</span>
                                    @else
                                        <span style="color: #999;">No remarks</span>
                                    @endif
                                </div>
                            </td>

                            <td class="col-actions">
                                <div class="action-buttons">
                                    <a href="{{ route('reports.shops.show', $shop->id) }}" class="btn-action btn-view" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('reports.shops.edit', $shop->id) }}" class="btn-action btn-edit" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn-action btn-delete" onclick="confirmDelete({{ $shop->id }})" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>

                                    <form id="delete-form-{{ $shop->id }}" 
                                          action="{{ route('reports.shops.destroy', $shop->id) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No shops found. <a href="{{ route('admin.shops.create') }}">Create a new shop</a></p>
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

    .shops-report-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #fff 100%);
        min-height: 100vh;
        padding: 30px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Header */
    .shops-header {
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

    .btn-create {
        background: linear-gradient(135deg, #f093fb, #f5576c);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
    }

    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Alerts */
    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border: none;
        animation: slideDown 0.3s ease;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        gap: 15px;
        flex: 1;
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

    .shops-table {
        width: 100%;
        border-collapse: collapse;
    }

    .shops-table thead {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .shops-table thead th {
        padding: 18px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .shops-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .shops-table tbody tr:hover {
        background: linear-gradient(135deg, #f5f7fa, #f0f2f5);
        box-shadow: inset 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    .table-row td {
        padding: 16px 12px;
        vertical-align: middle;
    }

    .col-id {
        width: 60px;
    }

    .col-shop {
        width: 180px;
    }

    .col-owner {
        width: 180px;
    }

    .col-location {
        width: 150px;
    }

    .col-financial {
        width: 180px;
    }

    .col-agreement {
        width: 150px;
    }

    .col-remarks {
        width: 350px;
    }

    .col-actions {
        width: 140px;
    }

    /* Images column */
    .col-images {
        width: 120px;
    }
    .col-images .shop-image { max-width: 60px; cursor: pointer; margin-right:4px; }
    .col-images .no-image { color:#999; }

    /* Badge ID */
    .badge-id {
        display: inline-block;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }

    /* Shop Info */
    .shop-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .shop-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 14px;
    }

    .shop-meta {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
    }

    .badge-bus {
        background: #e3f2fd;
        color: #1e40af;
    }

    .badge-image {
        background: #f3e5f5;
        color: #6a1b9a;
    }

    /* Owner Info */
    .owner-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
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

    .owner-contact {
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 12px;
        color: #666;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .contact-item i {
        color: #667eea;
        width: 14px;
    }

    /* Location Info */
    .location-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 12px;
    }

    .location-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #667eea;
        font-weight: 500;
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 12px;
    }

    .location-btn:hover {
        color: #764ba2;
        transform: translateX(2px);
        text-decoration: underline;
    }

    .location-btn i {
        color: #667eea;
        transition: all 0.3s ease;
    }

    .location-btn:hover i {
        transform: scale(1.2);
    }

    /* Read-Only Badge */
    .badge-ro {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f093fb, #f5576c);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(245, 87, 108, 0.25);
        white-space: nowrap;
    }

    /* Map Link Badge */
    .badge-map-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
        white-space: nowrap;
        cursor: help;
    }

    .badge-map-link i {
        font-size: 9px;
    }

    /* Google Maps Modal */
    .map-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        animation: fadeIn 0.3s ease;
    }

    .map-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .map-modal-content {
        background: white;
        border-radius: 15px;
        width: 95%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            transform: translateY(40px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .map-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 25px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 15px 15px 0 0;
    }

    .map-modal-title h2 {
        font-size: 22px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .map-modal-title h2 i {
        font-size: 24px;
    }

    .map-modal-title p {
        font-size: 14px;
        opacity: 0.9;
        margin: 0;
    }

    .map-close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .map-close-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }

    .map-container {
        padding: 20px;
        background: white;
        display: flex;
        justify-content: center;
    }

    .map-modal-body {
        padding: 25px;
        background: white;
    }

    .address-display {
        background: linear-gradient(135deg, #f5f7fa, #f0f2f5);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 5px solid #667eea;
    }

    .address-display h3 {
        font-size: 16px;
        color: #2c3e50;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .address-display h3 i {
        color: #667eea;
        font-size: 18px;
    }

    .address-display p {
        color: #555;
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
    }

    .map-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-map-link {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white !important;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-map-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        color: white !important;
        text-decoration: none;
    }

    .btn-map-close {
        background: #f0f2f5;
        color: #666;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-map-close:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }

    /* Responsive Map Modal */
    @media (max-width: 768px) {
        .map-modal-content {
            width: 98%;
            max-height: 95vh;
        }

        .map-modal-header {
            flex-direction: column;
            gap: 15px;
        }

        .map-actions {
            flex-direction: column;
        }

        .btn-map-link,
        .btn-map-close {
            width: 100%;
            justify-content: center;
        }
    }

    /* Financial Info */
    .financial-info {
        display: inline-flex;
        flex-direction: row;
        gap: 18px;
        font-size: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .financial-item {
        display: inline-flex;
        align-items: baseline;
        gap: 6px;
        padding: 4px 10px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
        border-radius: 6px;
        border-left: 3px solid #667eea;
        white-space: nowrap;
    }

    .financial-item .label {
        color: #666;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .financial-item .value {
        color: #667eea;
        font-weight: 700;
        font-size: 13px;
    }

    /* Agreement Info (reuses financial-item styling) */
    .agreement-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 12px;
    }

    /* keep a small vertical gap between financial-style pills */
    .agreement-info .financial-item {
        padding-left: 10px; /* ensure left border shows distinctly */
    }

    /* previous agreement-item rules are no longer needed */

    /* Remarks Info */
    .remarks-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 12px;
    }

    .remarks-text {
        color: #666;
        line-height: 1.4;
        padding: 8px 12px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
        border-radius: 6px;
        border-left: 3px solid #667eea;
        word-break: break-word;
        white-space: normal;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

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

    .btn-edit {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-delete {
        background: linear-gradient(135deg, #f093fb, #f5576c);
        color: white;
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 87, 108, 0.3);
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

    .empty-state a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .empty-state a:hover {
        text-decoration: underline;
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
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .pagination .page-item {
        display: inline-block;
    }

    .pagination .page-link {
        padding: 8px 12px;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        text-decoration: none;
        color: #667eea;
        transition: all 0.3s ease;
        font-size: 14px;
        line-height: 1.4;
        display: inline-block;
        background: white;
    }

    .pagination .page-link:hover {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-color: transparent;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-color: transparent;
    }

    .pagination .page-item.disabled .page-link {
        color: #ccc;
        cursor: not-allowed;
        background: #f8f9fa;
    }

    .pagination .page-item.disabled .page-link:hover {
        background: #f8f9fa;
        color: #ccc;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .table-responsive {
            overflow-x: auto;
        }

        .col-owner,
        .col-location {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .shops-header {
            flex-direction: column;
            text-align: center;
        }

        .filter-form {
            flex-direction: column;
        }

        .search-box,
        .filter-select {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .shops-table thead,
        .shops-table tbody,
        .shops-table tr {
            display: block;
            width: 100%;
        }

        .shops-table tbody tr {
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .shops-table td {
            display: block;
            padding: 10px;
            text-align: right;
            padding-left: 50%;
            position: relative;
        }

        .shops-table td:before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            color: #667eea;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Google Maps Modal -->
<div id="mapModal" class="map-modal">
    <div class="map-modal-content">
        <div class="map-modal-header">
            <div class="map-modal-title">
                <h2><i class="fas fa-map-location-dot"></i> <span id="modalShopName">Location</span></h2>
                <p id="modalOwnerName"></p>
            </div>
            <button type="button" class="map-close-btn" onclick="closeMapModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="mapContainer" class="map-container">
            <div id="mapCanvas" style="width: 100%; height: 500px; border-radius: 8px;"></div>
        </div>
        <div class="map-modal-body">
            <div class="address-display">
                <h3><i class="fas fa-location-dot"></i> Address</h3>
                <p id="modalAddress" style="word-break: break-word;"></p>
            </div>
            <div class="map-actions">
                <a id="googlemapsLink" href="#" target="_blank" class="btn btn-map-link">
                    <i class="fas fa-external-link-alt"></i> Open in Google Maps
                </a>
                <button type="button" class="btn btn-map-close" onclick="closeMapModal()">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', '') }}&libraries=places"></script>

<script>
    let mapInstance = null;
    let markerInstance = null;
    let nearbyMarkers = [];
    const googleMapsApiKey = '{{ env("GOOGLE_MAPS_API_KEY", "") }}';

    function openMapModal(address, shopName, ownerName) {
        // Check if API key is set
        if (!googleMapsApiKey || googleMapsApiKey.trim() === '') {
            showApiKeyError();
            return;
        }

        // Check if address is a URL/link (KML, Google Maps, etc.)
        if (address.trim().startsWith('http://') || address.trim().startsWith('https://')) {
            // Detect if it's a Google Maps link
            if (address.includes('maps.app.goo.gl') || address.includes('google.com/maps') || 
                address.includes('mymaps.google.com') || address.includes('maps.google.com')) {
                // Open Google Maps link - force opening in app/external
                openGoogleMapsLink(address);
                return;
            }
            // For other URLs (KML, etc.), also open in new tab
            window.open(address, '_blank');
            return;
        }

        const modal = document.getElementById('mapModal');
        const modalShopName = document.getElementById('modalShopName');
        const modalOwnerName = document.getElementById('modalOwnerName');
        const modalAddress = document.getElementById('modalAddress');
        const googlemapsLink = document.getElementById('googlemapsLink');

        // Set content
        modalShopName.textContent = shopName || 'Location';
        modalOwnerName.textContent = ownerName || '';
        modalAddress.textContent = address || 'No address available';

        const encodedAddress = encodeURIComponent(address);
        const mapsLink = `https://www.google.com/maps/search/${encodedAddress}`;
        googlemapsLink.href = mapsLink;

        // Show modal
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Initialize map
        setTimeout(() => {
            initializeMap(address, shopName);
        }, 100);

        // Add close button functionality with Escape key
        document.addEventListener('keydown', handleEscapeKey);
    }

    function openGoogleMapsLink(url) {
        // Check if mobile device
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            // For mobile, use a method that will trigger app opening
            // Create an iframe and use it to navigate (more reliable)
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = url;
            document.body.appendChild(iframe);
            
            // Also try window.location as fallback
            setTimeout(() => {
                window.location.href = url;
            }, 1000);
            
            // Clean up iframe
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 2000);
        } else {
            // Desktop: open in new tab
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    }

    function openGoogleMapsLinkDirect(url) {
        // Clean the URL by trimming whitespace
        const cleanUrl = url.trim();
        
        if (!cleanUrl || (!cleanUrl.startsWith('http://') && !cleanUrl.startsWith('https://'))) {
            alert('⚠️ Invalid link. Please check the location URL.');
            return;
        }

        console.log('Opening Google Maps:', cleanUrl);
        
        // Check if mobile device
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        try {
            if (isMobile) {
                // Try to open in Google Maps app first (mobile-specific)
                if (cleanUrl.includes('maps.app.goo.gl')) {
                    // Already a short link, just navigate
                    window.location.href = cleanUrl;
                } else if (cleanUrl.includes('google.com/maps') || cleanUrl.includes('mymaps.google.com')) {
                    // Convert to app link if possible, otherwise just navigate
                    window.location.href = cleanUrl;
                } else {
                    window.location.href = cleanUrl;
                }
            } else {
                // Desktop: open in new tab
                const newWindow = window.open(cleanUrl, '_blank', 'noopener,noreferrer');
                if (!newWindow) {
                    // Fallback if popup was blocked
                    window.location.href = cleanUrl;
                }
            }
        } catch (error) {
            console.error('Error opening map:', error);
            alert('⚠️ Could not open the map. Please try opening the link directly.');
        }
    }

    function initializeMap(address, shopName) {
        const mapContainer = document.getElementById('mapContainer');
        
        // Remove iframe if exists
        const existingIframe = mapContainer.querySelector('iframe');
        if (existingIframe) {
            existingIframe.remove();
        }

        // Create canvas for map
        if (!document.getElementById('mapCanvas')) {
            const canvas = document.createElement('div');
            canvas.id = 'mapCanvas';
            canvas.style.width = '100%';
            canvas.style.height = '500px';
            canvas.style.borderRadius = '8px';
            mapContainer.innerHTML = '';
            mapContainer.appendChild(canvas);
        }

        const geocoder = new google.maps.Geocoder();
        
        geocoder.geocode({ address: address }, (results, status) => {
            if (status === 'OK') {
                const location = results[0].geometry.location;

                // Create map
                mapInstance = new google.maps.Map(document.getElementById('mapCanvas'), {
                    zoom: 16,
                    center: location,
                    mapTypeControl: true,
                    fullscreenControl: true,
                    streetViewControl: true,
                });

                // Add marker for shop
                markerInstance = new google.maps.Marker({
                    position: location,
                    map: mapInstance,
                    title: shopName,
                    icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                });

                // Search for nearby places
                const service = new google.maps.places.PlacesService(mapInstance);
                const request = {
                    location: location,
                    radius: 500,
                    type: ['restaurant', 'hospital', 'bank', 'shopping_mall', 'transit_station', 'gas_station']
                };

                service.nearbySearch(request, (results, status) => {
                    if (status === google.maps.places.PlacesServiceStatus.OK && results) {
                        clearNearbyMarkers();
                        results.forEach((place, index) => {
                            if (index < 10) { // Limit to 10 nearby places
                                const marker = new google.maps.Marker({
                                    position: place.geometry.location,
                                    map: mapInstance,
                                    title: place.name,
                                    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                                });

                                const infoWindow = new google.maps.InfoWindow({
                                    content: `
                                        <div style="font-size: 12px; max-width: 200px;">
                                            <strong>${place.name}</strong><br>
                                            <span style="color: #666;">${place.types ? place.types[0].replace('_', ' ') : ''}</span><br>
                                            ${place.rating ? `<span>⭐ ${place.rating}</span>` : ''}
                                        </div>
                                    `
                                });

                                marker.addListener('click', () => {
                                    infoWindow.open(mapInstance, marker);
                                });

                                nearbyMarkers.push(marker);
                            }
                        });
                    }
                });
            } else {
                // Show error message
                const mapContainer = document.getElementById('mapContainer');
                mapContainer.innerHTML = `
                    <div style="
                        width: 100%;
                        height: 500px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: linear-gradient(135deg, #ffeaa7, #fab1a0);
                        border-radius: 8px;
                        flex-direction: column;
                        gap: 15px;
                        text-align: center;
                        padding: 20px;
                        box-sizing: border-box;
                    ">
                        <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #d63031;"></i>
                        <div>
                            <h3 style="color: #d63031; margin: 0 0 8px 0;">⚠️ Location Not Found</h3>
                            <p style="color: #555; margin: 0; font-size: 14px;">
                                Could not find: <strong>"${address}"</strong><br><br>
                                <span style="font-size: 12px; color: #666;">
                                    💡 Tip: If you have a Google Maps or KML link, use that instead.<br>
                                    Otherwise, enter a valid address.
                                </span>
                            </p>
                        </div>
                    </div>
                `;
            }
        });
    }

    function clearNearbyMarkers() {
        nearbyMarkers.forEach(marker => marker.setMap(null));
        nearbyMarkers = [];
    }

    function showApiKeyError() {
        alert('⚠️ Google Maps API Key is not configured.\n\n' +
              'Please follow these steps:\n\n' +
              '1. Go to https://console.cloud.google.com/\n' +
              '2. Create a new project\n' +
              '3. Enable "Maps Embed API", "Maps JavaScript API", and "Places API"\n' +
              '4. Create an API key\n' +
              '5. Add this to your .env file:\n' +
              'GOOGLE_MAPS_API_KEY=your_api_key_here\n\n' +
              'Then restart your application.');
    }

    function closeMapModal() {
        const modal = document.getElementById('mapModal');
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        document.removeEventListener('keydown', handleEscapeKey);
        clearNearbyMarkers();
        mapInstance = null;
    }

    function handleEscapeKey(event) {
        if (event.key === 'Escape') {
            closeMapModal();
            closeShopDetailsModal();
            closeImageModal();
        }
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const mapModal = document.getElementById('mapModal');
        window.addEventListener('click', function(event) {
            if (event.target === mapModal) {
                closeMapModal();
            }
        });

        const shopModal = document.getElementById('shopDetailsModal');
        window.addEventListener('click', function(event) {
            if (event.target === shopModal) {
                closeShopDetailsModal();
            }
        });

        const imgModal = document.getElementById('imageModal');
        window.addEventListener('click', function(event) {
            if (event.target === imgModal) {
                closeImageModal();
            }
        });
    });

    function openImageModal(src, alt) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('imageModalImg');
        img.src = src;
        img.alt = alt;
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    function openShopDetailsModal(shopId) {
        // lazy load via fetch
        fetch("{{ url('/reports/shops/') }}/" + shopId)
            .then(response => response.text())
            .then(html => {
                const content = document.getElementById('shopDetailsContent');
                content.innerHTML = html;
                const modal = document.getElementById('shopDetailsModal');
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
    }

    function closeShopDetailsModal() {
        const modal = document.getElementById('shopDetailsModal');
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    function confirmDelete(shopId) {
        if (confirm('Are you sure you want to delete this shop? This action cannot be undone.')) {
            document.getElementById('delete-form-' + shopId).submit();
        }
    }
</script>
@endsection
