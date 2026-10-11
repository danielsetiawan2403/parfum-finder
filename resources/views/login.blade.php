<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Parfum Finder</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/parfum.css') }}" rel="stylesheet">
</head>

{{-- Gambar latar: public/images/bg-login.jpg (opsional) --}}
<body class="auth-bg d-flex align-items-center justify-content-center p-3">

    <main class="glass p-4 w-100" style="max-width: 24rem;">
        <h1 class="fs-3 fw-bold text-center">Login Parfum Finder</h1>

        <form action="{{ route('login') }}" method="POST" class="mt-4 d-grid gap-3" novalidate>
            @csrf

            @if (session('warning'))
                <div role="alert" class="alert alert-warning py-2 px-3 small fw-semibold m-0">{{ session('warning') }}</div>
            @endif

            @if ($errors->any())
                <div role="alert" class="alert alert-danger py-2 px-3 small m-0">{{ $errors->first() }}</div>
            @endif

            <div>
                <label for="username" class="visually-hidden">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}"
                       placeholder="Username" autocomplete="username" required autofocus
                       class="form-control py-2">
            </div>

            <div>
                <label for="password" class="visually-hidden">Password</label>
                <input id="password" name="password" type="password"
                       placeholder="Password" autocomplete="current-password" required
                       class="form-control py-2">
            </div>

            <button type="submit" class="btn btn-dark rounded-pill py-2 fw-bold">Login</button>
        </form>

        <p class="text-center small mt-4 mb-0">
            Belum punya akun?
            <a href="{{ route('register') }}" class="btn btn-pf btn-sm rounded-pill fw-bold ms-1">Daftar di sini</a>
        </p>

        <p class="text-center mt-3 mb-0">
            <a href="{{ url('/') }}" class="btn btn-pf-slate btn-sm rounded-pill pf-xs fw-bold">&larr; Kembali ke Beranda</a>
        </p>
    </main>

</body>
</html>