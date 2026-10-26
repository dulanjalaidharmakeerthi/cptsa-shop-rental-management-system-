@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div style="max-width: 1200px; margin: auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
        <div>
            <h2 style="margin: 0; font-size: 24px;">System Audit Logs</h2>
            <p style="margin: 4px 0 0; color: #555;">All create/update/delete actions are recorded with actor and object details.</p>
        </div>
        <form method="GET" style="display: flex; gap: 8px; align-items: center;">
            <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Search by action, actor, model" style="padding: 8px; border: 1px solid #d4d4d8; border-radius: 6px; width: 250px;" />
            <button class="btn btn-primary" style="padding: 8px 12px;">Search</button>
        </form>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; overflow: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f3f4f6;">
                <tr>
                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Timestamp</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">User / Role</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Action</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Object</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Details</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Contact</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 10px; vertical-align: top;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td style="padding: 10px; vertical-align: top;">{{ $log->actor_name ?? 'System' }} <br><small style="color:#6b7280">{{ $log->actor_role ?? $log->actor_type ?? 'unknown' }}</small></td>
                        <td style="padding: 10px; vertical-align: top;">{{ ucfirst($log->action) }}</td>
                        <td style="padding: 10px; vertical-align: top;">{{ $log->auditable_type ? class_basename($log->auditable_type) : '-' }}<br><small>#{{ $log->auditable_id ?? '-' }}</small></td>
                        <td style="padding: 10px; vertical-align: top;">{{ $log->description ?? 'No details' }} @if($log->new_values) <br><small style="color:#52525b">{{ strlen($log->new_values) > 120 ? substr($log->new_values, 0, 120) . '...' : $log->new_values }}</small>@endif</td>
                        <td style="padding: 10px; vertical-align: top;">{{ $log->actor_contact ?? '-' }}</td>
                        <td style="padding: 10px; vertical-align: top;"><small style="color:#6b7280">{{ $log->ip_address ?? '-' }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 16px; text-align: center; color: #6b7280;">No audit logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination with arrow navigation -->
    <div style="margin-top: 16px; display: flex; align-items: center; justify-content: center; gap: 2px; font-size: 12px;">
        {{-- First Page Arrow --}}
        @if ($logs->onFirstPage())
            <span style="color: #d1d5db; padding: 4px 6px; cursor: not-allowed;" title="First page">⏮</span>
        @else
            <a href="{{ $logs->url(1) }}" style="color: #2563eb; padding: 4px 6px; text-decoration: none; cursor: pointer;" title="First page">⏮</a>
        @endif

        {{-- Previous Arrow --}}
        @if ($logs->onFirstPage())
            <span style="color: #d1d5db; padding: 4px 6px; cursor: not-allowed;" title="Previous">◀</span>
        @else
            <a href="{{ $logs->previousPageUrl() }}" style="color: #2563eb; padding: 4px 6px; text-decoration: none; cursor: pointer;" title="Previous">◀</a>
        @endif

        {{-- Page Numbers (show limited range for large pagination) --}}
        @php
            $current = $logs->currentPage();
            $last = $logs->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
            
            if ($start > 1) {
                echo '<span style="color: #6b7280; padding: 4px 4px;">...</span>';
            }
        @endphp

        @foreach ($logs->getUrlRange($start, $end) as $page => $url)
            @if ($page == $current)
                <span style="background: #2563eb; color: white; padding: 4px 6px; border-radius: 3px; font-weight: bold;">{{ $page }}</span>
            @else
                <a href="{{ $url }}" style="color: #2563eb; padding: 4px 6px; text-decoration: none; cursor: pointer;">{{ $page }}</a>
            @endif
        @endforeach

        @php
            if ($end < $last) {
                echo '<span style="color: #6b7280; padding: 4px 4px;">...</span>';
            }
        @endphp

        {{-- Next Arrow --}}
        @if ($logs->hasMorePages())
            <a href="{{ $logs->nextPageUrl() }}" style="color: #2563eb; padding: 4px 6px; text-decoration: none; cursor: pointer;" title="Next">▶</a>
        @else
            <span style="color: #d1d5db; padding: 4px 6px; cursor: not-allowed;" title="Next">▶</span>
        @endif

        {{-- Last Page Arrow --}}
        @if ($logs->hasMorePages())
            <a href="{{ $logs->url($logs->lastPage()) }}" style="color: #2563eb; padding: 4px 6px; text-decoration: none; cursor: pointer;" title="Last page">⏭</a>
        @else
            <span style="color: #d1d5db; padding: 4px 6px; cursor: not-allowed;" title="Last page">⏭</span>
        @endif
    </div>

    <!-- Result count -->
    <div style="margin-top: 12px; text-align: center; font-size: 12px; color: #6b7280;">
        Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
    </div>
</div>
@endsection
