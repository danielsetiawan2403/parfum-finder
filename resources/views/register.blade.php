<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Parfum Finder</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/parfum.css') }}" rel="stylesheet">
</head>

<body class="auth-bg d-flex align-items-center justify-content-center p-3">

    <main class="glass p-4 w-100 my-3" style="max-width: 24rem;">
        <h1 class="fs-3 fw-bold text-center">Daftar Parfum Finder</h1>

        <form action="{{ route('register') }}" method="POST" class="mt-4 d-grid gap-3" novalidate>
            @csrf

            <div>
                <label for="name" class="visually-hidden">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                       placeholder="Nama lengkap" autocomplete="name" required autofocus class="form-control py-2">
                @error('name') <div class="pf-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="username" class="visually-hidden">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}"
                       placeholder="Username" autocomplete="username" required class="form-control py-2">
                @error('username') <div class="pf-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="email" class="visually-hidden">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       placeholder="Email" autocomplete="email" required class="form-control py-2">
                @error('email') <div class="pf-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="password" class="visually-hidden">Password</label>
                <input id="password" name="password" type="password"
                       placeholder="Password (minimal 8 karakter)" autocomplete="new-password" required class="form-control py-2">
                @error('password') <div class="pf-error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="visually-hidden">Konfirmasi password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       placeholder="Ulangi password" autocomplete="new-password" required class="form-control py-2">
            </div>

            <button type="submit" class="btn btn-dark rounded-pill py-2 fw-bold">Daftar</button>
        </form>

        <p class="text-center small mt-4 mb-0">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="btn btn-pf btn-sm rounded-pill fw-bold ms-1">Login di sini</a>
        </p>

        <p class="text-center mt-3 mb-0">
            <a href="{{ url('/') }}" class="btn btn-pf-slate btn-sm rounded-pill pf-xs fw-bold">&larr; Kembali ke Beranda</a>
        </p>
    </main>

</body>
</html>