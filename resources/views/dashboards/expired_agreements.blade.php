@extends('layouts.app')

@section('title', 'Expired Agreements')

@section('content')
<div class="expired-agreements-page" style="padding: 24px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <h1 style="margin:0; font-size: 1.8rem;">Expired & Near-Expiry Agreements</h1>
            <p style="margin:4px 0 0; color:#555;">All shops with agreements expired or ending within 3 months.</p>
        </div>
        <div>
            <a href="{{ url('/managedashboard') }}" style="color:#fff; background:#4f46e5; padding: 8px 12px; border-radius: 8px; text-decoration:none; font-weight:600;">Back to Dashboard</a>
        </div>
    </div>

    <div style="overflow-x:auto; background:#fff; border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.06);">
        <table style="width:100%; border-collapse:collapse; min-width:900px;">
            <thead>
            <tr style="background:#f8f9ff; color:#1f2937; text-align:left; font-weight:600; font-size:13px;">
                <th style="padding:10px 12px; border-bottom:1px solid #e5e7eb;">Shop #</th>
                <th style="padding:10px 12px; border-bottom:1px solid #e5e7eb;">Shop Name</th>
                <th style="padding:10px 12px; border-bottom:1px solid #e5e7eb;">Owner</th>
                <th style="padding:10px 12px; border-bottom:1px solid #e5e7eb;">Agreement Start</th>
                <th style="padding:10px 12px; border-bottom:1px solid #e5e7eb;">Agreement End</th>
                <th style="padding:10px 12px; border-bottom:1px solid #e5e7eb;">Status</th>
                <th style="padding:10px 12px; border-bottom:1px solid #e5e7eb;">Monthly Rent</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($expired_shops) && $expired_shops->count() > 0)
                @foreach($expired_shops as $shop)
                    @php
                        $endDate = $shop->agreement_end ? \Carbon\Carbon::parse($shop->agreement_end) : null;
                        $days = $endDate ? (int) floor(now()->diffInDays($endDate, false)) : null;
                        $status = $endDate ? ($days < 0 ? "Expired " . abs($days) . " days" : ($days == 0 ? 'Expires today' : "$days days remaining")) : 'No end date';
                    @endphp
                    <tr style="border-bottom:1px solid #eef2ff;">
                        <td style="padding:10px 12px;">{{ $shop->shop_number }}</td>
                        <td style="padding:10px 12px;">{{ $shop->shop_name }}</td>
                        <td style="padding:10px 12px;">{{ $shop->shop_owner_name }}</td>
                        <td style="padding:10px 12px;">{{ $shop->agreement_start ? \Carbon\Carbon::parse($shop->agreement_start)->format('Y-m-d') : '-' }}</td>
                        <td style="padding:10px 12px;">{{ $shop->agreement_end ? \Carbon\Carbon::parse($shop->agreement_end)->format('Y-m-d') : '-' }}</td>
                        <td style="padding:10px 12px; color: {{ $days !== null && $days < 0 ? '#dc2626' : '#047857' }};">{{ $status }}</td>
                        <td style="padding:10px 12px;">Rs {{ number_format($shop->monthly_rent ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" style="padding:14px; text-align:center; color:#6b7280;">No expired or near-expiry agreements found.</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
