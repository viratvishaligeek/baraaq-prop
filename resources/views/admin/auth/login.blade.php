<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('admin/css/admin.css') }}" rel="stylesheet">
</head>

<body class="login-page">
    <form class="login-card" method="POST" action="{{ route('admin.login.store') }}">@csrf<h1>Welcome back</h1>
        <p class="text-muted">Sign in to manage your website.</p>
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <label class="form-label">Email address</label>
        <input class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" required autofocus>
        <label class="form-label">Password</label>
        <input class="form-control mb-3" type="password" name="password" required>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button class="btn btn-primary w-100">Sign in</button>
    </form>
</body>

</html>
