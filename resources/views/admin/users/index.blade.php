@extends('admin.dashboard')

@section('title', 'User Management')

@section('content')
    <style>
        .user-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .user-list-header h2 {
            margin: 0;
        }

        .table-wrapper {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #f3f4f6;
            color: #334155;
        }

        th {
            background: #f8fafc;
            font-weight: 700;
            color: #0f172a;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-active { background: #d1fae5; color: #065f46; }
        .status-blocked { background: #fee2e2; color: #991b1b; }

        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .action-group form {
            display: inline;
        }

        .btn-sm {
            padding: 8px 12px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-sm.primary { background: #6366f1; color: white; }
        .btn-sm.secondary { background: #e2e8f0; color: #0f172a; }
        .btn-sm.danger { background: #ef4444; color: white; }
        .btn-sm.warning { background: #f59e0b; color: white; }
    </style>

    <div class="user-list-header">
        <div>
            <h2>All Registered Users</h2>
            <p style="color:#475569; margin-top: 6px;">View, block, unblock, and delete user accounts from the admin portal.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create New User</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>{{ $user->position ?? 'User' }}</td>
                        <td>
                            <span class="status-pill {{ $user->is_blocked ? 'status-blocked' : 'status-active' }}">
                                {{ $user->is_blocked ? 'Blocked' : 'Active' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn-sm secondary">Profile</a>
                                <form action="{{ route('admin.users.toggle_block', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-sm warning">{{ $user->is_blocked ? 'Unblock' : 'Block' }}</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user account? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding: 20px; color:#64748b;">No registered users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
@endsection
