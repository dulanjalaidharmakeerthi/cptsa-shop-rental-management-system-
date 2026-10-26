@extends('layouts.app')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecf1 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .shop-dashboard {
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecf1 100%);
        min-height: 100vh;
        padding: 16px;
    }

    /* Header Section */
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
        box-shadow: 0 10px 26px rgba(102, 126, 234, 0.2);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        position: relative;
        z-index: 1;
    }

    .header-title {
        flex: 1;
    }

    .header-title h1 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-title p {
        font-size: 12px;
        opacity: 0.9;
    }

    .shop-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.25);
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .header-actions {
        display: flex;
        gap: 12px;
        z-index: 2;
    }

    .btn-header {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }

    .btn-header:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    .header-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        position: relative;
        z-index: 1;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 16px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }

    .stat-card .label {
        font-size: 10px;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .stat-card .value {
        font-size: 16px;
        font-weight: 700;
    }

    /* Content Grid */
    .dashboard-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
        max-width: 980px;
        margin-left: auto;
        margin-right: auto;
        justify-items: stretch;
    }

    .card {
        background: #ffffff;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
        min-height: 0;
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 100%;
    }

    .card:hover {
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    .card-header {
        margin-bottom: 10px;
        border-bottom: 1px solid #edf0f6;
        padding-bottom: 8px;
    }

    .card-header h2 {
        font-size: 14px;
        margin: 0;
        font-weight: 700;
    }

    .info-group {
        gap: 10px;
    }

    .info-item {
        padding: 10px 12px;
        border-left: 4px solid #667eea;
        border-radius: 10px;
        background: #f7f8fc;
    }

    .info-item .label {
        font-size: 10px;
    }

    .info-item .value {
        font-size: 12px;
    }

    .financial-card {
        padding: 12px;
        border-radius: 10px;
    }

    .financial-card .label {
        font-size: 10px;
    }

    .financial-card .value {
        font-size: 14px;
    }

    .card:hover {
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 3px solid #f0f2f5;
    }

    .card-header h2 {
        font-size: 16px; /* smaller heading */
        color: #2c3e50;
        margin: 0;
        font-weight: 700;
    }

    .card-header i {
        color: #667eea;
        font-size: 22px;
    }

    .info-group {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: #f8f9fa;
        border-radius: 12px;
        border-left: 4px solid #667eea;
    }

    .info-item .label {
        font-size: 11px;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .info-item .value {
        font-size: 13px; /* slightly smaller values */
        color: #2c3e50;
        font-weight: 700;
    }

    .info-item.financial .value {
        color: #667eea;
        font-weight: 700;
        font-size: 16px;
    }

    /* Financial Cards */
    .financial-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .financial-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #e0e4ff;
        text-align: center;
    }

    .financial-card .label {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 16px;
        letter-spacing: 0.4px;
    }

    .financial-card .value {
        font-size: 16px;
        font-weight: 700;
        color: #667eea;
    }

    /* Image Gallery */
    .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        aspect-ratio: 1;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover {
        transform: scale(1.05);
        border-color: #667eea;
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2);
    }

    .gallery-item:hover img {
        transform: scale(1.1);
    }

    .gallery-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f2f5;
        color: #999;
        font-size: 32px;
    }

    /* Agreement Timeline */
    .timeline {
        display: flex;
        gap: 20px;
        margin-top: 16px;
    }

    .timeline-item {
        flex: 1;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #667eea;
        text-align: center;
    }

    .timeline-item .label {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .timeline-item .value {
        font-size: 14px;
        color: #667eea;
        font-weight: 700;
    }

    /* Full Width Card */
    .card-full {
        grid-column: 1 / -1;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .dashboard-content {
            grid-template-columns: 1fr;
        }

        .card-full {
            grid-column: 1;
        }

        .financial-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .header-top {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 24px;
        }

        .header-title h1 {
            font-size: 24px;
        }

        .header-stats {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .financial-grid {
            grid-template-columns: 1fr;
        }

        .timeline {
            flex-direction: column;
        }

        .image-gallery {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>

<div class="shop-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="header-top">
            <div class="header-title">
                <h1>
                    <i class="fas fa-store"></i>
                    {{ $shop->shop_name ?? 'Shop Details' }}
                </h1>
                <p><i class="fas fa-user"></i> Owner: <strong>{{ $shop->shop_owner_name }}</strong></p>
            </div>
            <div class="header-actions">
                <a href="{{ route('reports.shops.edit', $shop->id) }}" class="btn-header">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('reports.shops.index') }}" class="btn-header">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="header-stats">
            <div class="stat-card">
                <div class="label">Shop Number</div>
                <div class="value">#{{ $shop->shop_number }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Monthly Rent</div>
                <div class="value">Rs {{ number_format($shop->monthly_rent, 0) }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Interest Rate</div>
                <div class="value">{{ $shop->rate ?? '0' }}%</div>
            </div>
            <div class="stat-card">
                <div class="label">Bus Stand</div>
                <div class="value">{{ $shop->busStand?->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-content">
        <!-- Owner Information Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-circle"></i>
                <h2>Owner Information</h2>
            </div>
            <div class="info-group">
                <div class="info-item">
                    <div>
                        <div class="label"><i class="fas fa-user"></i> Current Owner</div>
                        <div class="value" style="font-size: 14px; color: #2d3748;">{{ $shop->shop_owner_name }}</div>
                    </div>
                    <span class="badge" style="background: #e8f0ff; color:#22355d; border-radius:999px; padding:4px 10px; font-weight:700; font-size:11px;">Active</span>
                </div>
                <div class="info-item">
                    <div>
                        <div class="label">NIC</div>
                        <div class="value">{{ $shop->shop_owner_nic ?? '—' }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div>
                        <div class="label">Phone</div>
                        <div class="value">{{ $shop->shop_owner_phone ?? '—' }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div>
                        <div class="label">Address</div>
                        <div class="value">{{ $shop->shop_owner_address ?? '—' }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div>
                        <div class="label">Location</div>
                        <div class="value">{{ $shop->location ?? $shop->shop_address ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Owner History Card -->
        <div class="card card-full">
            <div class="card-header">
                <i class="fas fa-history"></i>
                <h2>Owner History</h2>
            </div>
            <div class="timeline">
                @if($shop->ownerHistories->count())
                    @foreach($shop->ownerHistories as $history)
                        <div class="timeline-item">
                            <div class="timeline-date">{{ $history->changed_at->format('Y-m-d H:i') }}</div>
                            <div class="timeline-title">{{ $history->shop_owner_name }} (NIC: {{ $history->shop_owner_nic ?? '—' }})</div>
                            <div class="timeline-desc">
                                <span>Phone: {{ $history->shop_owner_phone ?? '—' }}</span><br>
                                <span>Agreement: {{ optional($history->agreement_start)->format('Y-m-d') ?? 'N/A' }} → {{ optional($history->agreement_end)->format('Y-m-d') ?? 'N/A' }}</span><br>
                                <span>Reason: {{ $history->reason ?? 'Owner changed' }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="timeline-item">
                        <div class="timeline-date">No previous owner records</div>
                        <div class="timeline-desc">This shop has no owner history yet.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i>
                <h2>Financial Summary</h2>
            </div>
            <div class="financial-grid">
                <div class="financial-card">
                    <div class="label">Advance Payment</div>
                    <div class="value">Rs {{ number_format($shop->advance_payment ?? 0, 0) }}</div>
                </div>
                <div class="financial-card">
                    <div class="label">Opening Balance</div>
                    <div class="value">Rs {{ number_format($shop->opening_balance ?? 0, 0) }}</div>
                </div>
                <div class="financial-card">
                    <div class="label">Due Payment</div>
                    <div class="value">Rs {{ number_format($shop->due_payment ?? 0, 0) }}</div>
                </div>
                <div class="financial-card">
                    <div class="label">Due Interest</div>
                    <div class="value">Rs {{ number_format($shop->due_interest ?? 0, 0) }}</div>
                </div>
            </div>
        </div>

        <!-- Agreement Information Card -->
        @if($shop->agreement_start || $shop->agreement_end || $shop->agreement_duration)
        <div class="card">
            <div class="card-header">
                <i class="fas fa-handshake"></i>
                <h2>Agreement Details</h2>
            </div>
            {{-- use same grid style as financial cards for consistency and visual clarity --}}
            <div class="financial-grid">
                @if($shop->agreement_duration)
                <div class="financial-card">
                    <div class="label">Duration</div>
                    <div class="value">{{ $shop->agreement_duration }} Months</div>
                </div>
                @endif
                @if($shop->agreement_start)
                <div class="financial-card">
                    <div class="label">Start Date</div>
                    <div class="value">{{ $shop->agreement_start->format('M d, Y') }}</div>
                </div>
                @endif
                @if($shop->agreement_end)
                <div class="financial-card">
                    <div class="label">End Date</div>
                    <div class="value">{{ $shop->agreement_end->format('M d, Y') }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="card card-full">
            <div class="card-header">
                <i class="fas fa-file-alt"></i>
                <h2>Documentation</h2>
            </div>
            <div class="info-group">
                <div class="info-item">
                    <div>
                        <div class="label">Document</div>
                        <div class="value">
                            @if($shop->document_path)
                                <a href="{{ $shop->getAttachmentUrl($shop->document_path) }}" target="_blank" rel="noopener noreferrer">View / download document</a>
                            @else
                                No document uploaded
                            @endif
                        </div>
                    </div>
                </div>
                @if($shop->remarks)
                    <div class="info-item">
                        <div>
                            <div class="label">Notes</div>
                            <div class="value">{{ $shop->remarks }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Image Gallery Card -->
        @if($shop->image_path || $shop->image1_path || $shop->image2_path || $shop->image3_path)
        <div class="card card-full">
            <div class="card-header">
                <i class="fas fa-images"></i>
                <h2>Gallery</h2>
            </div>
            <div class="image-gallery">
                @if($shop->image_path)
                <div class="gallery-item" onclick="openImageModal('{{ $shop->getAttachmentUrl($shop->image_path) }}')">
                    <img src="{{ $shop->getAttachmentUrl($shop->image_path) }}" alt="Shop Image">
                </div>
                @endif
                @if($shop->image1_path)
                <div class="gallery-item" onclick="openImageModal('{{ $shop->getAttachmentUrl($shop->image1_path) }}')">
                    <img src="{{ $shop->getAttachmentUrl($shop->image1_path) }}" alt="Shop Image 1">
                </div>
                @endif
                @if($shop->image2_path)
                <div class="gallery-item" onclick="openImageModal('{{ $shop->getAttachmentUrl($shop->image2_path) }}')">
                    <img src="{{ $shop->getAttachmentUrl($shop->image2_path) }}" alt="Shop Image 2">
                </div>
                @endif
                @if($shop->image3_path)
                <div class="gallery-item" onclick="openImageModal('{{ $shop->getAttachmentUrl($shop->image3_path) }}')">
                    <img src="{{ $shop->getAttachmentUrl($shop->image3_path) }}" alt="Shop Image 3">
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function openImageModal(imageUrl) {
        // Create and show modal with image
        const modal = document.createElement('div');
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:9999;display:flex;align-items:center;justify-content:center;';
        modal.innerHTML = `
            <div style="position:relative;background:white;border-radius:12px;padding:20px;max-width:90%;max-height:90%;">
                <button onclick="this.parentElement.parentElement.remove()" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:28px;cursor:pointer;color:#666;">&times;</button>
                <img src="${imageUrl}" style="max-width:100%;max-height:90vh;border-radius:8px;"/>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', (e) => {
            if(e.target === modal) modal.remove();
        });
    }
</script>

@endsection