@extends('layouts.admin')

@section('title', 'Create Admin')

@section('content')

<div style="max-width:1000px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;gap:12px;flex-wrap:wrap;">
        <h2 style="margin:0;color:#333;font-size:20px;font-weight:600;">Create New Admin</h2>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">Admin List</a>
    </div>

    <form action="{{ route('admin.admins.store') }}" method="POST" style="background:white;padding:30px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
        @csrf

        <div class="form-group">
            <label for="name">Full Name <span style="color:red">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name') <div style="color:#ef4444">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="username">Username <span style="color:red">*</span></label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required>
            @error('username') <div style="color:#ef4444">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @error('email') <div style="color:#ef4444">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Password <span style="color:red">*</span></label>
            <input type="password" id="password" name="password" required>
            @error('password') <div style="color:#ef4444">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password <span style="color:red">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div style="margin-top:18px;display:flex;gap:8px;">
            <button type="submit" class="btn btn-primary">Create Admin</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<div style="margin-top:28px;background:white;padding:24px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h3 style="margin:0;color:#333;font-size:18px;font-weight:600;">Registered Admins</h3>
        <span style="color:#64748b;">{{ $admins->count() }} admin(s)</span>
    </div>

    @if($admins->isEmpty())
        <p style="margin:0;color:#64748b;">No admin accounts registered yet.</p>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:600px;">
                <thead>
                    <tr style="background:#f8fafc;text-align:left;">
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">Name</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">Username</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">Email</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                        <tr>
                            <td style="padding:12px;border-bottom:1px solid #e5e7eb;">{{ $admin->name }}</td>
                            <td style="padding:12px;border-bottom:1px solid #e5e7eb;">{{ $admin->username }}</td>
                            <td style="padding:12px;border-bottom:1px solid #e5e7eb;">{{ $admin->email ?? '—' }}</td>
                            <td style="padding:12px;border-bottom:1px solid #e5e7eb;">{{ $admin->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
    .form-group{margin-bottom:14px}
    .form-group label{display:block;margin-bottom:6px;font-weight:600}
    .form-group input{width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:6px}
    .btn{padding:10px 16px;border-radius:6px;text-decoration:none;border:none;cursor:pointer}
    .btn-primary{background:#667eea;color:#fff}
    .btn-secondary{background:#e5e7eb;color:#333}
</style>

@endsection
