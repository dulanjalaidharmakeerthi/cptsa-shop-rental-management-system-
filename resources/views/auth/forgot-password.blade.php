<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CPPTSA</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="login-container">
    <img src="{{ asset('images/logo1.jpeg') }}" class="logo" alt="CPPTSA Logo">

    <h1 class="title-en">Forgot Password</h1>
    <h2 class="subheading">Reset Your Password</h2>

    @if ($errors->any())
        <div style="color:#b00020;margin-bottom:12px;text-align:left;max-width:820px;margin-left:auto;margin-right:auto;">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div style="color:#4caf50;margin-bottom:12px;text-align:left;max-width:820px;margin-left:auto;margin-right:auto;background:#f1f8f4;padding:10px;border-radius:4px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="color:#b00020;margin-bottom:12px;text-align:left;max-width:820px;margin-left:auto;margin-right:auto;background:#fce4ec;padding:10px;border-radius:4px;">
            {{ session('error') }}
        </div>
    @endif

    <form method="post" action="{{ route('password.send-otp') }}" class="login-form">
        @csrf
        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
        </div>

        <button type="submit">Send OTP</button>

        <p class="register-text">
            Remember your password? <a href="{{ route('login') }}">Login here</a>
        </p>
    </form>

</div>

</body>
</html>
