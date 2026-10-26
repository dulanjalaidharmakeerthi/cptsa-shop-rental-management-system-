@extends('layouts.app')

@section('title', 'Create User')

@push('head')
<style>
  :root {
    --primary: #1f6feb;
    --primary-dark: #1e40af;
    --primary-light: #3b82f6;
    --secondary: #8b5cf6;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --header: #0f1724;
    --text: #1e293b;
    --muted: #64748b;
    --border: #e2e8f0;
    --bg-light: #f8fafc;
    --bg-lighter: #f1f5f9;
    --shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
    --shadow-hover: 0 20px 50px rgba(15, 23, 42, 0.12);
    --radius: 12px;
    --font: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  body { background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%); min-height: 100vh; font-family: var(--font); }

  .create-user-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 30px 20px;
  }

  .create-user-card {
    background: white;
    border-radius: var(--radius);
    padding: 50px 45px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    width: 100%;
    max-width: 600px;
    animation: slideUp 0.4s ease;
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .form-header {
    margin-bottom: 40px;
    text-align: center;
  }

  .form-header h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 800;
    color: var(--header);
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 12px;
  }

  .form-header p {
    color: var(--muted);
    font-size: 15px;
    margin: 0;
  }

  .alert {
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 24px;
    animation: slideDown 0.3s ease;
  }

  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .alert-success {
    background: linear-gradient(135deg, rgba(16,185,129,0.1) 0%, rgba(5,150,105,0.05) 100%);
    border: 1px solid rgba(16,185,129,0.3);
    border-left: 4px solid var(--success);
  }

  .alert-success strong {
    color: #065f46;
  }

  .alert-danger {
    background: linear-gradient(135deg, rgba(239,68,68,0.1) 0%, rgba(220,38,38,0.05) 100%);
    border: 1px solid rgba(239,68,68,0.3);
    border-left: 4px solid var(--danger);
  }

  .alert-danger strong {
    color: #991b1b;
  }

  .alert ul {
    margin: 10px 0 0 20px;
    color: #991b1b;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-group:nth-child(3) {
    grid-column: 1 / 2;
  }

  .form-group:nth-child(4) {
    grid-column: 2 / 3;
  }

  .form-group:nth-child(5) {
    grid-column: 1 / 3;
  }

  .form-group:nth-child(6) {
    grid-column: 1 / 3;
  }

  .form-label {
    font-weight: 700;
    color: var(--header);
    margin-bottom: 8px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .form-control {
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-family: var(--font);
    font-size: 15px;
    transition: all 0.3s ease;
    background: white;
    color: var(--text);
  }

  .form-control:hover {
    border-color: var(--primary);
  }

  .form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(31,111,235,0.1);
    background: linear-gradient(135deg, rgba(31,111,235,0.02) 0%, rgba(139,92,246,0.02) 100%);
  }

  .form-control[type="password"] {
    letter-spacing: 2px;
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid var(--border);
  }

  .btn {
    flex: 1;
    padding: 14px 24px;
    border: none;
    border-radius: 8px;
    font-family: var(--font);
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(31,111,235,0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31,111,235,0.4);
  }

  .btn-primary:active {
    transform: translateY(0);
  }

  .btn-secondary {
    background: var(--bg-light);
    color: var(--primary);
    border: 1.5px solid var(--border);
  }

  .btn-secondary:hover {
    background: var(--bg-lighter);
    border-color: var(--primary);
  }

  @media (max-width: 600px) {
    .create-user-card {
      padding: 30px 20px;
    }

    .form-grid {
      grid-template-columns: 1fr;
      gap: 16px;
    }

    .form-group:nth-child(3),
    .form-group:nth-child(4),
    .form-group:nth-child(5),
    .form-group:nth-child(6) {
      grid-column: 1 / 2;
    }

    .form-header h1 {
      font-size: 24px;
    }
  }
</style>
@endpush

@section('content')
<div class="create-user-container">
    <div class="create-user-card">
        <div class="form-header">
            <h1>👤 Create New User</h1>
            <p>Add a new team member to your organization</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <strong>✓ Success:</strong> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>⚠ Please fix the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">👤 Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">📧 Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="john@example.com" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">📱 Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="+94 71 234 5678" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">🎯 Position</label>
                    <select name="position" class="form-control" required>
                        <option value="">-- Select Position --</option>
                            <option value="Cashier" {{ old('position')=='Cashier' ? 'selected':'' }}> Cashier</option>
                            <option value="Staff" {{ old('position')=='Staff' ? 'selected':'' }}> Staff</option>
                            <option value="Manager" {{ old('position')=='Manager' ? 'selected':'' }}> Manager</option>

                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">🔐 Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label class="form-label">🔐 Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✓ Create User</button>
                <a href="/managedashboard" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>
@endsection
