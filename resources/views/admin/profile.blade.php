@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
    <div class="page-header" style="margin-bottom:20px;">
        <h2>My Profile</h2>
        <p>View and edit your account details from here.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:16px; padding:12px 16px; background:#ecfdf3; color:#166534; border-radius:8px; border:1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="background:white;border-radius:10px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        @if($readonly)
            <div class="profile-view">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" value="{{ $admin->name }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="{{ $admin->username }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="{{ $admin->email }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Created At</label>
                        <input type="text" value="{{ $admin->created_at->format('Y-m-d H:i') }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Updated At</label>
                        <input type="text" value="{{ $admin->updated_at->format('Y-m-d H:i') }}" readonly>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Department / Role</label>
                        <input type="text" value="{{ $admin->role ?? 'Admin' }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" value="********" readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="{{ old('username', $admin->username) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Created At</label>
                        <input type="text" value="{{ $admin->created_at->format('Y-m-d H:i') }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Updated At</label>
                        <input type="text" value="{{ $admin->updated_at->format('Y-m-d H:i') }}" readonly>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Department / Role</label>
                        <input type="text" value="{{ $admin->role ?? 'Admin' }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>New Password (leave blank to keep existing)</label>
                        <input type="password" name="password" autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        @endif
    </div>
@endsection