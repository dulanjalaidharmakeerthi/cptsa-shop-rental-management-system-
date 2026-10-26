<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - CPPTSA</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .otp-input-group {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 20px 0;
        }
        .otp-input {
            width: 50px;
            height: 50px;
            font-size: 24px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-weight: bold;
        }
        .otp-input:focus {
            outline: none;
            border-color: #1976d2;
        }
    </style>
</head>
<body>

<div class="login-container">
    <img src="{{ asset('images/logo1.jpeg') }}" class="logo" alt="CPPTSA Logo">

    <h1 class="title-en">Verify OTP</h1>
    <h2 class="subheading">Enter the OTP sent to your email</h2>

    @if ($errors->any())
        <div style="color:#b00020;margin-bottom:12px;text-align:left;max-width:820px;margin-left:auto;margin-right:auto;">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div style="color:#b00020;margin-bottom:12px;text-align:left;max-width:820px;margin-left:auto;margin-right:auto;background:#fce4ec;padding:10px;border-radius:4px;">
            {{ session('error') }}
        </div>
    @endif

    <form method="post" action="{{ route('password.verify-otp') }}" class="login-form">
        @csrf
        
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        <p style="text-align:center;color:#666;margin-bottom:20px;">
            We've sent a 6-digit OTP to:<br>
            <strong>{{ $email ?? old('email') }}</strong>
        </p>

        <div class="input-group">
            <label>Enter OTP</label>
            <input type="text" name="otp" value="{{ old('otp') }}" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required style="letter-spacing: 10px; font-size: 24px; text-align: center; font-weight: bold;">
        </div>

        <button type="submit">Verify OTP</button>

        <p class="register-text">
            Didn't receive the code? <a href="{{ route('password.forgot') }}">Request new OTP</a>
        </p>
    </form>

</div>

</body>
</html>
