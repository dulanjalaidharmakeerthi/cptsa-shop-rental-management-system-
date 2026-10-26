<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPPTSA Login</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            background-image: url('{{ asset('images/bus.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            font-family: sans-serif;
        }
    </style>
</head>
<body>

<div class="login-container">

    <img src="{{ asset('images/logo1.jpeg') }}" class="logo" alt="CPPTSA Logo">

    <h1 class="title-en">Central Province Passenger Transport Services Authority</h1>
    <h1 class="title-si">මධ්‍යම පළාත් මගී ප්‍රවාහන සේවා අධිකාරිය</h1>

    <hr>

    <h2 class="subheading">Shop Rental Management System for Central Province</h2>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    @if($errors->has('login'))
        <div>{{ $errors->first('login') }}</div>
    @endif
<div class="login-container acrd makeit">

    <form method="post" action="{{ route('login') }}" class="login-form">
        @csrf
        <div class="input-group">
            <label>Username or Email</label>
            <input type="text" name="username" value="{{ old('username') }}" placeholder="Enter your username or email">
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••">
        </div>

        <button type="submit">Login</button>

        
        <p class="register-text">
            Forgot your password?
            <a href="{{ route('password.forgot') }}">Reset here</a>
        </p>
        <p class="register-text">
            <a href="{{ route('admin.login') }}">Admin Login</a>
        </p>
</div>

</div>

</body>
</html>
