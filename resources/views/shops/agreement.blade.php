@extends('layouts.app')

@section('title', 'Agreement - ' . ($shop->shop_name ?? 'Shop'))

@section('content')
<div class="container" style="max-width:900px; margin-top:12px">
    <div class="dashboard-card">
        <div style="display:flex; justify-content:space-between; align-items:center">
            <div>
                <h1 style="margin:0">Agreement</h1>
                <div class="muted">Agreement document for {{ $shop->shop_name ?? $shop->shop_number }}</div>
            </div>
            <div>
                <button onclick="window.print()" class="btn" style="background:var(--primary-color); color:#fff; padding:8px 12px; border-radius:8px">Print</button>
            </div>
        </div>

        <hr>

        <div style="padding:8px">
            <h3 style="color:var(--primary-color)">Shop Details</h3>
            <table style="width:100%; border-collapse:collapse">
                <tr>
                    <td style="padding:6px; width:40%"><strong>Shop Name</strong></td>
                    <td style="padding:6px">{{ $shop->shop_name }}</td>
                </tr>
                <tr>
                    <td style="padding:6px"><strong>Shop Number</strong></td>
                    <td style="padding:6px">{{ $shop->shop_number }}</td>
                </tr>
                <tr>
                    <td style="padding:6px"><strong>Owner</strong></td>
                    <td style="padding:6px">{{ $shop->shop_owner_name }}</td>
                </tr>
                <tr>
                    <td style="padding:6px"><strong>Bus Stand</strong></td>
                    <td style="padding:6px">{{ $shop->busStand->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px"><strong>Agreement Start</strong></td>
                    <td style="padding:6px">{{ $shop->agreement_start ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px"><strong>Agreement End</strong></td>
                    <td style="padding:6px">{{ $shop->agreement_end ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px"><strong>Monthly Rent</strong></td>
                    <td style="padding:6px">{{ $shop->monthly_rent }}</td>
                </tr>
                <tr>
                    <td style="padding:6px"><strong>Advance Payment</strong></td>
                    <td style="padding:6px">{{ $shop->advance_payment }}</td>
                </tr>
            </table>

            <hr>

            <h3 style="color:var(--primary-color)">Remarks</h3>
            <p>{{ $shop->remarks ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
