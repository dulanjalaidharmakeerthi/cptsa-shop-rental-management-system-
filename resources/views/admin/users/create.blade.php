@extends('layouts.admin')

@section('title', 'Create User')

@section('content')

<div style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('admin.users.store') }}" method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf

        <h2 style="margin-bottom: 30px; color: #333; font-size: 24px; font-weight: 600;">Create New User</h2>

        <!-- Name -->
        <div class="form-group">
            <label for="name">Full Name <span style="color: red;">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required @error('name') aria-invalid="true" @enderror>
            @error('name')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email Address <span style="color: red;">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="user@example.com" required @error('email') aria-invalid="true" @enderror>
            @error('email')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Phone -->
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+94712345678" @error('phone') aria-invalid="true" @enderror>
            @error('phone')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Position -->
        <div class="form-group">
            <label for="position">Position <span style="color: red;">*</span></label>
            <select id="position" name="position" required @error('position') aria-invalid="true" @enderror>
                <option value="Manager" {{ old('position', 'Manager') === 'Manager' ? 'selected' : '' }}>Manager</option>
                <option value="Accountant" {{ old('position') === 'Accountant' ? 'selected' : '' }}>Accountant</option>
                <option value="Staff" {{ old('position') === 'Staff' ? 'selected' : '' }}>Staff</option>
                <option value="Cashier" {{ old('position') === 'Cashier' ? 'selected' : '' }}>Cashier</option>
            </select>
            @error('position')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password <span style="color: red;">*</span></label>
            <input type="password" id="password" name="password" placeholder="At least 8 characters" required @error('password') aria-invalid="true" @enderror>
            @error('password')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation">Confirm Password <span style="color: red;">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create User</button>
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
