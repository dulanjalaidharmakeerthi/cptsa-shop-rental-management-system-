@extends('admin.dashboard')

@section('content')
    <style>
        /* Ensure all text and arrows are small and readable  */
        h2 { font-size: 18px !important; line-height: 1.2 !important; }
        a { font-size: 14px !important; max-width: 300px; overflow: hidden; }
        button { font-size: 12px !important; }
        table { font-size: 12px !important; }
        td, th { font-size: 12px !important; }
        /* Keep arrows and special characters small */
        a::after, a::before { font-size: 12px !important; display: inline; }
        /* Constrain any large elements */
        svg { max-width: 24px; max-height: 24px; }
        img { max-width: 100%; height: auto; }
        /* Override any oversized text */
        * { font-size: inherit !important; }
        p { font-size: 14px !important; }
        label { font-size: 12px !important; }
    </style>
    <h2>Payment History</h2>
    @if(session('success'))
        <div style="padding:10px;background:#d1fae5;color:#065f46;border-radius:4px;margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.payments.index') }}" style="margin-bottom:20px;">
        <label for="shop_number">Filter by Shop Number:</label>
        <input type="text" name="shop_number" id="shop_number" value="{{ old('shop_number', $shopNumber) }}" placeholder="Enter shop number" style="padding:4px;">
        <button type="submit" style="padding:4px 8px;">Search</button>
        <a href="{{ route('admin.payments.index') }}" style="margin-left:10px;">Reset</a>
    </form>

    <table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:14px;">
        <thead style="background:#f3f4f6;">
            <tr>
                <th>ID</th>
                <th>Shop #</th>
                <th>Owner</th>
                <th>Amount</th>
                <th>Interest</th>
                <th>Receipt</th>
                <th>Paid At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->shop->shop_number ?? '-' }}</td>
                    <td>{{ $p->shop->shop_owner_name ?? '-' }}</td>
                    <td>{{ number_format($p->amount,2) }}</td>
                    <td>{{ number_format($p->interest,2) }}</td>
                    <td>{{ $p->receipt_number }}</td>
                    <td>{{ $p->paid_at->format('Y/m/d') }}</td>
                    <td><a href="{{ route('admin.payments.edit', $p->id) }}">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:15px;">
        {{ $payments->appends(['shop_number' => $shopNumber])->links() }}
    </div>
@endsection
