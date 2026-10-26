@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div style="background:#fff; padding:24px; border-radius:12px; box-shadow:0 4px 12px rgba(15,23,42,.08);">
    <h1 style="margin:0 0 20px; color:#1e3a8a;">Payment History</h1>

    @if(session('success'))
        <div role="status" style="padding:12px; margin-bottom:16px; background:#d1fae5; color:#065f46; border-radius:6px;">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('accountant.payments.index') }}" style="display:flex; gap:8px; align-items:center; margin-bottom:20px;">
        <label for="shop_number">Filter by shop number:</label>
        <input type="text" id="shop_number" name="shop_number" value="{{ $shopNumber }}" style="padding:8px; border:1px solid #cbd5e1; border-radius:6px;">
        <button type="submit" style="padding:8px 14px; border:0; border-radius:6px; background:#2563eb; color:#fff; cursor:pointer;">Search</button>
    </form>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead style="background:#f1f5f9;">
                <tr>
                    <th style="padding:10px;">ID</th>
                    <th style="padding:10px;">Shop #</th>
                    <th style="padding:10px;">Owner</th>
                    <th style="padding:10px;">Amount</th>
                    <th style="padding:10px;">Interest</th>
                    <th style="padding:10px;">Receipt</th>
                    <th style="padding:10px;">Paid At</th>
                    <th style="padding:10px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:10px;">{{ $payment->id }}</td>
                        <td style="padding:10px;">{{ $payment->shop->shop_number ?? '-' }}</td>
                        <td style="padding:10px;">{{ $payment->shop->shop_owner_name ?? '-' }}</td>
                        <td style="padding:10px;">{{ number_format($payment->amount, 2) }}</td>
                        <td style="padding:10px;">{{ number_format($payment->interest, 2) }}</td>
                        <td style="padding:10px;">{{ $payment->receipt_number }}</td>
                        <td style="padding:10px;">{{ $payment->paid_at?->format('Y/m/d') ?? '-' }}</td>
                        <td style="padding:10px;"><a href="{{ route('accountant.payments.edit', $payment->id) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:16px; text-align:center;">No payment records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $payments->appends(['shop_number' => $shopNumber])->links() }}</div>
</div>
@endsection
