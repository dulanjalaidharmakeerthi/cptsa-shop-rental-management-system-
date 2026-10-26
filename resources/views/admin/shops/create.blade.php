@extends('layouts.admin')

@section('title', 'Create Shop')

@section('content')

<div style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('admin.shops.store') }}" method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf

        <h2 style="margin-bottom: 30px; color: #333; font-size: 24px; font-weight: 600;">Register New Shop</h2>

        <!-- Shop Number -->
        <div class="form-group">
            <label for="shop_number">Shop Number <span style="color: red;">*</span></label>
            <input type="text" id="shop_number" name="shop_number" value="{{ old('shop_number') }}" placeholder="e.g., SH-001" required @error('shop_number') aria-invalid="true" @enderror>
            @error('shop_number')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Shop Name -->
        <div class="form-group">
            <label for="shop_name">Shop Name <span style="color: red;">*</span></label>
            <input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name') }}" placeholder="e.g., Main Store" required @error('shop_name') aria-invalid="true" @enderror>
            @error('shop_name')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Owner Name -->
        <div class="form-group">
            <label for="shop_owner_name">Owner Name <span style="color: red;">*</span></label>
            <input type="text" id="shop_owner_name" name="shop_owner_name" value="{{ old('shop_owner_name') }}" placeholder="Full name" required @error('shop_owner_name') aria-invalid="true" @enderror>
            @error('shop_owner_name')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Owner NIC -->
        <div class="form-group">
            <label for="shop_owner_nic">Owner NIC <span style="color: red;">*</span></label>
            <input type="text" id="shop_owner_nic" name="shop_owner_nic" value="{{ old('shop_owner_nic') }}" placeholder="National ID" required @error('shop_owner_nic') aria-invalid="true" @enderror>
            @error('shop_owner_nic')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Owner Contact -->
        <div class="form-group">
            <label for="shop_owner_phone">Owner Phone <span style="color: red;">*</span></label>
            <input type="text" id="shop_owner_phone" name="shop_owner_phone" value="{{ old('shop_owner_phone') }}" placeholder="Phone number" required @error('shop_owner_phone') aria-invalid="true" @enderror>
            @error('shop_owner_phone')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Owner Address -->
        <div class="form-group">
            <label for="shop_owner_address">Owner Address</label>
            <input type="text" id="shop_owner_address" name="shop_owner_address" value="{{ old('shop_owner_address') }}" placeholder="Street address, city, etc." @error('shop_owner_address') aria-invalid="true" @enderror>
            @error('shop_owner_address')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Shop Location -->
        <div class="form-group">
            <label for="location">Shop Location</label>
            <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="Shop location details" @error('location') aria-invalid="true" @enderror>
            @error('location')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Monthly Rent -->
        <div class="form-group">
            <label for="monthly_rent">Monthly Rent (Rs) <span style="color: red;">*</span></label>
            <input type="number" id="monthly_rent" name="monthly_rent" step="0.01" value="{{ old('monthly_rent') }}" placeholder="0.00" required @error('monthly_rent') aria-invalid="true" @enderror>
            @error('monthly_rent')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Advance Payment -->
        <div class="form-group">
            <label for="advance_payment">Advance Payment (Rs)</label>
            <input type="number" id="advance_payment" name="advance_payment" step="0.01" value="{{ old('advance_payment', '0.00') }}" placeholder="0.00" min="0" @error('advance_payment') aria-invalid="true" @enderror>
            @error('advance_payment')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Bus Stand -->
        <div class="form-group">
            <label for="bus_stand_id">Bus Stand <span style="color: red;">*</span></label>
            <select id="bus_stand_id" name="bus_stand_id" required @error('bus_stand_id') aria-invalid="true" @enderror>
                <option value="">-- Select Bus Stand --</option>
                @if(isset($busStands))
                    @foreach($busStands as $busStand)
                        <option value="{{ $busStand->id }}" {{ old('bus_stand_id') == $busStand->id ? 'selected' : '' }}>
                            {{ $busStand->name ?? $busStand->id }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('bus_stand_id')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Agreement Start Date -->
        <div class="form-group">
            <label for="agreement_start">Agreement Start Date</label>
            <input type="date" id="agreement_start" name="agreement_start" value="{{ old('agreement_start') }}" @error('agreement_start') aria-invalid="true" @enderror>
            @error('agreement_start')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Agreement End Date -->
        <div class="form-group">
            <label for="agreement_end">Agreement End Date</label>
            <input type="date" id="agreement_end" name="agreement_end" value="{{ old('agreement_end') }}" @error('agreement_end') aria-invalid="true" @enderror>
            @error('agreement_end')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Interest Rate (%) -->
        <div class="form-group">
            <label for="rate">Interest Rate (%)</label>
            <input type="number" id="rate" name="rate" step="0.01" value="{{ old('rate', '0.00') }}" placeholder="e.g., 12.50" min="0" @error('rate') aria-invalid="true" @enderror>
            @error('rate')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Due Interest -->
        <div class="form-group">
            <label for="due_interest">Due Interest (Rs)</label>
            <input type="number" id="due_interest" name="due_interest" step="0.01" value="{{ old('due_interest', '0.00') }}" placeholder="0.00" min="0" @error('due_interest') aria-invalid="true" @enderror>
            @error('due_interest')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Shop</button>
            <a href="/admin" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group input[aria-invalid="true"],
    .form-group select[aria-invalid="true"] {
        border-color: #ef4444;
    }

    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #764ba2;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #333;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }
</style>

@endsection
