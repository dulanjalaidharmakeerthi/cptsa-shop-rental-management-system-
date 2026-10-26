@extends('admin.dashboard')

@section('content')
    <h2>Edit Payment #{{ $payment->id }}</h2>

    <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}">
        @csrf
        @method('PUT')

        <input type="hidden" name="shop_id" value="{{ $payment->shop_id }}">

        <div style="margin-bottom:10px;">
            <label>Shop Number</label><br>
            <input type="text" value="{{ $payment->shop->shop_number ?? '' }}" readonly style="padding:4px; width:200px;">
        </div>

        <div style="margin-bottom:10px;">
            <label>Owner</label><br>
            <input type="text" value="{{ $payment->shop->shop_owner_name ?? '' }}" readonly style="padding:4px; width:300px;">
        </div>

        <div style="margin-bottom:10px;">
            <label for="receipt_number">Receipt Number</label><br>
            <input type="text" id="receipt_number" name="receipt_number" value="{{ old('receipt_number', $payment->receipt_number) }}" style="padding:4px; width:300px;">
            @error('receipt_number')<div style="color:red;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom:10px;">
            <label for="amount">Amount</label><br>
            <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount', $payment->amount) }}" style="padding:4px; width:150px;">
            @error('amount')<div style="color:red;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom:10px;">
            <label for="interest">Interest</label><br>
            <input type="number" step="0.01" id="interest" name="interest" value="{{ old('interest', $payment->interest) }}" style="padding:4px; width:150px;">
            @error('interest')<div style="color:red;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom:10px;">
            <label for="paid_at">Paid At</label><br>
            <input type="date" id="paid_at" name="paid_at" value="{{ old('paid_at', $payment->paid_at->format('Y/m/d')) }}" style="padding:4px; width:150px;">
            @error('paid_at')<div style="color:red;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-top:20px;">
            <button type="submit" style="padding:6px 12px;">Save Changes</button>
            <a href="{{ route('admin.payments.index') }}" style="margin-left:10px; font-size:12px; text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                <span style="font-size:12px; line-height:1; margin-right:4px;">←</span>Cancel
            </a>
        </div>
    </form>
@endsection
