@extends('admin.dashboard')

@section('title', 'User Profile')

@section('content')
    <style>
        .profile-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(15, 23, 42, 0.06);
            padding: 28px;
            max-width: 900px;
            margin: 0 auto;
        }

        .profile-card h2 {
            margin-bottom: 8px;
            color: #0f172a;
        }

        .profile-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .profile-field {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 12px 24px;
            margin-bottom: 16px;
            align-items: center;
        }

        .profile-field strong {
            color: #475569;
        }

        .profile-field span {
            color: #0f172a;
        }

        .profile-badge {
            display: inline-flex;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13px;
        }

        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-blocked { background: #fee2e2; color: #991b1b; }
        .badge-position { background: #e0e7ff; color: #4338ca; }

        .btn-sm {
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .btn-sm.primary { background: #6366f1; color: white; }
        .btn-sm.secondary { background: #e2e8f0; color: #0f172a; }
        .btn-sm.warning { background: #f59e0b; color: white; }
        .btn-sm.danger { background: #ef4444; color: white; }
    </style>

    <div class="profile-card">
        <div class="profile-actions">
            <a href="{{ route('admin.users.index') }}" class="btn-sm secondary">← Back to user list</a>
            <form action="{{ route('admin.users.toggle_block', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-sm warning">{{ $user->is_blocked ? 'Unblock User' : 'Block User' }}</button>
            </form>
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this account permanently?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-sm danger">Delete Account</button>
            </form>
        </div>

        <h2>{{ $user->name }}</h2>
        <p style="color:#64748b; margin-bottom: 24px;">Review the user profile and manage account access.</p>

        <div class="profile-field">
            <strong>Email:</strong>
            <span>{{ $user->email }}</span>
        </div>
        <div class="profile-field">
            <strong>Phone:</strong>
            <span>{{ $user->phone ?? 'Not provided' }}</span>
        </div>
        <div class="profile-field">
            <strong>Position:</strong>
            <span><span class="profile-badge badge-position">{{ $user->position ?? 'User' }}</span></span>
        </div>
        <div class="profile-field">
            <strong>Registered:</strong>
            <span>{{ $user->created_at->format('F j, Y') }}</span>
        </div>
        <div class="profile-field">
            <strong>Current Status:</strong>
            <span>
                <span class="profile-badge {{ $user->is_blocked ? 'badge-blocked' : 'badge-active' }}">
                    {{ $user->is_blocked ? 'Blocked' : 'Active' }}
                </span>
            </span>
        </div>
    </div>
@endsection
