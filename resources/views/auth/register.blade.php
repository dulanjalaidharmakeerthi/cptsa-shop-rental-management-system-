<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CPPTSA</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="login-container">
    <img src="{{ asset('images/logo1.jpeg') }}" class="logo" alt="CPPTSA Logo">

    <h1 class="title-en">Create an account</h1>
    <h2 class="subheading">Register for Shop Rental Management System</h2>

    @if ($errors->any())
        <div style="color:#b00020;margin-bottom:12px;text-align:left;max-width:820px;margin-left:auto;margin-right:auto;">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ url('/register') }}" class="login-form">
        @csrf
        <div class="input-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="input-group">
            <label>Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required>
        </div>

        <div class="input-group">
            <label>Position</label>
            <select name="position" required>
                <option value="">-- Select position --</option>
                <option value="Manager" {{ old('position')=='Manager' ? 'selected' : '' }}>Manager</option>
                <option value="Officer" {{ old('position')=='Officer' ? 'selected' : '' }}>Officer</option>
                <option value="Staff" {{ old('position')=='Staff' ? 'selected' : '' }}>Staff</option>
                <option value="Vendor" {{ old('position')=='Vendor' ? 'selected' : '' }}>Vendor</option>
                <option value="Cashier" {{ old('position')=='Cashier' ? 'selected' : '' }}>Cashier</option>
                <option value="Other" {{ old('position')=='Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="input-group">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>

        <button type="submit">Register</button>

        <p class="register-text">
            Already have an account? <a href="{{ url('/') }}">Login</a>
        </p>
    </form>

</div>

</body>
</html>
