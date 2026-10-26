@extends('layouts.app')

@section('content')
<div class="tenants-report-container">
    <div class="tenants-header" style="margin-bottom:18px; padding:18px;">
        <div class="header-content">
            <h1><i class="fas fa-user-tie"></i> Tenant Detail</h1>
            <p style="font-size:13px; opacity:0.9;">Details and financial summary for <strong>{{ $shop->shop_owner_name }}</strong></p>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <a href="{{ route('reports.tenants.index') }}" class="btn-action" style="background:#f0f2f5; color:#333;">&larr; Back</a>
            <a href="javascript:window.print()" class="btn-action btn-view">Print</a>
        </div>
    </div>

    <div class="table-wrapper" style="padding:18px;">
        <div style="display:grid; grid-template-columns: 1fr 340px; gap:18px; align-items:start;">
            <div style="background:transparent;">
                <div style="background:white; padding:16px; border-radius:10px; box-shadow: 0 6px 20px rgba(0,0,0,0.05);">
                    <h3 style="margin-bottom:12px;">Owner & Shop</h3>
                    <div style="display:flex; gap:12px; align-items:flex-start;">
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:16px; color:#2c3e50;">{{ $shop->shop_owner_name }}</div>
                            <div style="color:#777; font-size:13px; margin-top:4px;">{{ $shop->shop_owner_address ?? 'No owner address' }}</div>
                            <div style="margin-top:10px;">
                                <a href="tel:{{ $shop->shop_owner_phone }}" class="phone-link">{{ $shop->shop_owner_phone }}</a>
                            </div>
                        </div>
                        <div style="min-width:120px; text-align:right;">
                            <div style="background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding:8px 12px; border-radius:8px; display:inline-block; font-weight:700;">Rs {{ number_format($shop->monthly_rent,2) }}</div>
                            <div style="font-size:12px; color:#999; margin-top:8px;">Monthly Rent</div>
                        </div>
                    </div>

                    <div style="margin-top:16px; display:grid; grid-template-columns:repeat(2,1fr); gap:10px;">
                        <div>
                            <div style="color:#999;font-size:12px;">Agreement</div>
                            <div style="font-weight:600;">{{ $shop->agreement_duration ? $shop->agreement_duration . ' months' : '—' }}</div>
                            <div style="font-size:12px; color:#777;">{{ optional($shop->agreement_start)->format('M d, Y') ?? 'N/A' }} — {{ optional($shop->agreement_end)->format('M d, Y') ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div style="color:#999;font-size:12px;">Location</div>
                            <div style="font-weight:600;">{{ $shop->location ?? '—' }}</div>
                            <div style="font-size:12px; color:#777;">{{ $shop->shop_address ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <div style="background:white; padding:14px; border-radius:10px; margin-top:12px; box-shadow: 0 6px 20px rgba(0,0,0,0.04);">
                    <h4 style="margin-bottom:10px;">Financial Summary</h4>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <div style="flex:1; min-width:160px; background:#f8fafc; padding:10px; border-radius:8px;">
                            <div style="font-size:12px; color:#999;">Due Payment</div>
                            <div style="font-weight:700; color:#667eea;">Rs {{ number_format($shop->due_payment ?? 0,2) }}</div>
                        </div>
                        <div style="flex:1; min-width:160px; background:#f8fafc; padding:10px; border-radius:8px;">
                            <div style="font-size:12px; color:#999;">Due Interest</div>
                            <div style="font-weight:700; color:#667eea;">Rs {{ number_format($shop->due_interest ?? 0,2) }}</div>
                        </div>
                        <div style="flex:1; min-width:160px; background:#f8fafc; padding:10px; border-radius:8px;">
                            <div style="font-size:12px; color:#999;">Year Total Taken</div>
                            <div style="font-weight:700; color:#667eea;">Rs {{ number_format($shop->year_total_taken ?? ($computed['year_total_taken'] ?? 0),2) }}</div>
                        </div>
                    </div>

                    <div style="margin-top:12px; display:flex; gap:10px;">
                        <div style="flex:1;">
                            <div style="font-size:12px; color:#999;">Total Paid</div>
                            <div style="font-weight:700;">Rs {{ number_format($computed['total_paid'] ?? ($shop->total_paid ?? 0),2) }}</div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:12px; color:#999;">Total Due</div>
                            <div style="font-weight:700; color:#e74c3c;">Rs {{ number_format($computed['total_due'] ?? ($shop->total_due ?? 0),2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <aside style="width:100%; max-width:340px;">
                <div style="background:white; padding:14px; border-radius:10px; text-align:center; box-shadow: 0 6px 20px rgba(0,0,0,0.04);">
                    @if($shop->image_path)
                        <img src="{{ asset($shop->image_path) }}" alt="shop image" style="width:100%; height:auto; border-radius:8px; object-fit:cover;" />
                    @else
                        <div style="height:180px; display:flex; align-items:center; justify-content:center; color:#bbb;">No Image</div>
                    @endif

                    <div style="margin-top:12px; text-align:left;">
                        <div style="font-size:12px; color:#999;">Owner NIC</div>
                        <div style="font-weight:600;">{{ $shop->shop_owner_nic ?? '—' }}</div>

                        <div style="margin-top:8px; font-size:12px; color:#999;">Remarks</div>
                        <div style="font-size:13px; color:#444;">{{ $shop->remarks ?? '—' }}</div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <style>
        .tenants-report-container { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding:22px; }
        .btn-action { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:8px; text-decoration:none; }
        .btn-view { background:linear-gradient(135deg,#667eea,#764ba2); color:white; }
        .phone-link { color:#667eea; text-decoration:none; }
    </style>
</div>

@endsection
