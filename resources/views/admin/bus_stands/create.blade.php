@extends('layouts.admin')

@section('title', 'Create Bus Stand')

@section('content')

<div style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('admin.bus_stands.store') }}" method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf

        <h2 style="margin-bottom: 30px; color: #333; font-size: 24px; font-weight: 600;">Create New Bus Stand</h2>

        <!-- Bus Stand Name -->
        <div class="form-group">
            <label for="name">Bus Stand Name <span style="color: red;">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Central Bus Stand" required @error('name') aria-invalid="true" @enderror>
            @error('name')
                <span style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <div style="margin-top: 30px;">
            <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                Create Bus Stand
            </button>
            <a href="{{ route('admin.dashboard') }}" style="color: #666; text-decoration: none; margin-left: 15px; font-size: 16px;">Cancel</a>
        </div>
    </form>
</div>

@endsection