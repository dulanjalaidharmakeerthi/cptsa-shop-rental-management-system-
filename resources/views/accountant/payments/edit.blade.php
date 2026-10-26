@extends('layouts.app')

@section('title', 'Edit Payment')

@section('content')
<div style="max-width:640px; margin:0 auto; background:#fff; padding:24px; border-radius:12px; box-shadow:0 4px 12px rgba(15,23,42,.08);">
    <h1 style="margin:0 0 20px; color:#1e3a8a;">Edit Payment #{{ $payment->id }}</h1>

    <form method="POST" action="{{ route('accountant.payments.update', $payment->id) }}">
        @csrf
        @method('PUT')

        <input type="hidden" name="shop_id" value="{{ $payment->shop_id }}">

        <div style="margin-bottom:14px;">
            <label for="shop_number">Shop Number</label>
            <input id="shop_number" type="text" value="{{ $payment->shop->shop_number ?? '' }}" readonly style="display:block; width:100%; padding:9px; border:1px solid #cbd5e1; border-radius:6px;">
        </div>
        <div style="margin-bottom:14px;">
            <label for="owner">Owner</label>
            <input id="owner" type="text" value="{{ $payment->shop->shop_owner_name ?? '' }}" readonly style="display:block; width:100%; padding:9px; border:1px solid #cbd5e1; border-radius:6px;">
        </div>
        <div style="margin-bottom:14px;">
            <label for="receipt_number">Receipt Number</label>
            <input id="receipt_number" name="receipt_number" type="text" value="{{ old('receipt_number', $payment->receipt_number) }}" required style="display:block; width:100%; padding:9px; border:1px solid #cbd5e1; border-radius:6px;">
            @error('receipt_number')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:14px;">
            <label for="amount">Amount</label>
            <input id="amount" name="amount" type="number" step="0.01" min="0" value="{{ old('amount', $payment->amount) }}" style="display:block; width:100%; padding:9px; border:1px solid #cbd5e1; border-radius:6px;">
            @error('amount')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:14px;">
            <label for="interest">Interest</label>
            <input id="interest" name="interest" type="number" step="0.01" min="0" value="{{ old('interest', $payment->interest) }}" style="display:block; width:100%; padding:9px; border:1px solid #cbd5e1; border-radius:6px;">
            @error('interest')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:20px;">
            <label for="paid_at">Paid At</label>
            <input id="paid_at" name="paid_at" type="date" value="{{ old('paid_at', $payment->paid_at?->format('Y-m-d')) }}" required style="display:block; width:100%; padding:9px; border:1px solid #cbd5e1; border-radius:6px;">
            @error('paid_at')<div style="color:#b91c1c;">{{ $message }}</div>@enderror
        </div>

        <button type="submit" style="padding:10px 16px; border:0; border-radius:6px; background:#2563eb; color:#fff; cursor:pointer;">Save Changes</button>
        <a href="{{ route('accountant.payments.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection
