@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="profile-page">
        <div class="profile-header" style="margin-bottom: 20px;">
            <h1>Profile</h1>
            <p>View, update and secure your account details.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom:16px;">
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="profile-grid">
            <aside class="profile-card">
                <div class="avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->position ?? 'Manager' }}</p>
                <ul class="profile-meta">
                    <li><strong>Email</strong><span>{{ $user->email }}</span></li>
                    <li><strong>Phone</strong><span>{{ $user->phone ?? '-' }}</span></li>
                    <li><strong>Joined</strong><span>{{ $user->created_at->format('M d, Y') }}</span></li>
                    <li><strong>Last updated</strong><span>{{ $user->updated_at->format('M d, Y') }}</span></li>
                </ul>
                @if(request()->routeIs('dashboard.profile.show'))
                    <a href="{{ route('dashboard.profile.edit') }}" class="btn btn-primary" style="width:100%;margin-top:12px;">Edit Profile</a>
                    <a href="{{ route('dashboard.profile.password.edit') }}" class="btn btn-secondary" style="width:100%;margin-top:8px;">Change Password</a>
                @endif
            </aside>

            <section class="profile-edit">
                <div class="elevated-panel">
                    <h2>{{ request()->routeIs('dashboard.profile.show') ? 'Profile Details' : 'Edit Information' }}</h2>

                    <form method="POST" action="{{ route('dashboard.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required {{ request()->routeIs('dashboard.profile.show') ? 'readonly' : '' }}>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required {{ request()->routeIs('dashboard.profile.show') ? 'readonly' : '' }}>
                            </div>

                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" {{ request()->routeIs('dashboard.profile.show') ? 'readonly' : '' }}>
                            </div>

                            <div class="form-group">
                                <label>Position</label>
                                <input type="text" value="{{ $user->position }}" readonly>
                            </div>
                        </div>

                        <div class="actions">
                            @if(request()->routeIs('dashboard.profile.show'))
                                <a href="{{ url()->previous() ?? route('managedashboard') }}" class="btn btn-secondary">Back</a>
                            @else
                                <button type="submit" class="btn btn-success">Save Changes</button>
                                <a href="{{ route('dashboard.profile.show') }}" class="btn btn-secondary">Cancel</a>
                            @endif
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <style>
        .profile-page {
            padding: 0;
        }

        .profile-header h1 {
            font-size: 2rem;
            margin-bottom: 4px;
            color: #1d4ed8;
        }

        .profile-header p {
            color: #475569;
            margin-bottom: 22px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
            align-items: start;
        }

        .profile-card {
            background: linear-gradient(135deg, #eef2ff 0%, #eef7ff 100%);
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 16px 28px rgba(15,23,42,.08);
        }

        .avatar {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: #4f46e5;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(79,70,229,.35);
        }

        .profile-card h3 {
            text-align: center;
            color: #1e3a8a;
            margin-top: 0;
            margin-bottom: 4px;
            font-size: 1.35rem;
        }

        .profile-card p {
            text-align: center;
            color: #475569;
            margin-bottom: 16px;
            font-size: .95rem;
        }

        .profile-meta {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 8px;
        }

        .profile-meta li {
            background: #fff;
            border: 1px solid #dbeafe;
            padding: 10px 12px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .profile-meta li strong {
            color: #1f2937;
            margin-right: 8px;
            font-weight: 600;
        }

        .profile-edit .elevated-panel {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 12px 24px rgba(15,23,42,.06);
        }

        .profile-edit h2 {
            margin-top: 0;
            margin-bottom: 16px;
            color: #111827;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .profile-edit .form-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        }

        .profile-edit .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #334155;
        }

        .profile-edit .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            color: #0f172a;
            font-size: 0.95rem;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .profile-edit .form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
            outline: none;
            background: white;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn {
            text-decoration: none;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 600;
            font-size: .95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary,
        .btn-success {
            background: #4f46e5;
            color: #ffffff;
        }

        .btn-success {
            background: #16a34a;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #1f2937;
        }

        @media (max-width: 980px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection