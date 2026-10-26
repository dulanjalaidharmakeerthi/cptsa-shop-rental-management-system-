<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CPPTSA</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="login-container">
    <img src="{{ asset('images/logo1.jpeg') }}" class="logo" alt="CPPTSA Logo">

    <h1 class="title-en">Reset Password</h1>
    <h2 class="subheading">Create your new password</h2>

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

    <form method="post" action="{{ route('password.reset') }}" class="login-form">
        @csrf
        
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        <div class="input-group">
            <label>New Password</label>
            <input type="password" name="password" placeholder="Minimum 8 characters" required>
        </div>

        <div class="input-group">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" placeholder="Confirm your password" required>
        </div>

        <button type="submit">Reset Password</button>

        <p class="register-text">
            Remember your password? <a href="{{ route('login') }}">Login here</a>
        </p>
    </form>

</div>

</body>
</html>
