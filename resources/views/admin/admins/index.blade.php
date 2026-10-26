@extends('layouts.admin')

@section('title', 'Admin List')

@section('content')
<div style="max-width:1100px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;gap:12px;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0;color:#333;font-size:20px;font-weight:600;">Registered Admins</h2>
            <p style="margin:4px 0 0;color:#64748b;">All previously added admin accounts are listed below.</p>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">Create Admin</a>
    </div>

    <div style="background:white;padding:24px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
        @if($admins->isEmpty())
            <p style="margin:0;color:#64748b;">No admin accounts have been added yet.</p>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:700px;">
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
</div>

<style>
    .btn{padding:10px 16px;border-radius:6px;text-decoration:none;border:none;cursor:pointer}
    .btn-primary{background:#667eea;color:#fff}
</style>

@endsection
