<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
    <img src="{{ asset('images/logo1.jpeg') }}" class="logo" alt="Logo">

    <h1 class="title-en">Administrator Portal</h1>
    <hr>

    <div class="login-container acrd makeit">
        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert error">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('admin.login.post') }}" class="login-form">
            @csrf
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Enter your username">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••">
            </div>

            <button type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>
