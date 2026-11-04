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

            {{-- Akun sudah dihapus admin: jelaskan kenapa tidak bisa login --}}
            @if (session('account_deleted'))
                <div role="alert" class="alert alert-danger py-2 px-3 small m-0">
                    <strong class="d-block">Akun ini sudah dihapus oleh admin, jadi tidak bisa dipakai untuk login.</strong>
                    <span class="d-block mt-1 text-break">Alasan: {{ session('account_deleted') }}</span>
                    <span class="d-block mt-1">Merasa ini keliru? Hubungi admin Parfum Finder.</span>
                </div>
            @elseif ($errors->any())
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
                <div class="input-group">
                    <input id="password" name="password" type="password"
                           placeholder="Password" autocomplete="current-password" required
                           class="form-control py-2">
                    <button type="button" class="btn btn-light border" data-toggle-password="password"
                            aria-label="Tampilkan password" aria-pressed="false">
                        {{-- Mata terbuka: tampil saat password tersembunyi --}}
                        <svg data-eye="show" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{-- Mata dicoret: tampil saat password terlihat --}}
                        <svg data-eye="hide" class="d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
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

    <script>
        // Tombol mata: tampilkan/sembunyikan isi kolom password
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.getAttribute('data-toggle-password'));
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-pressed', show);
                btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
                btn.querySelector('[data-eye="show"]').classList.toggle('d-none', show);
                btn.querySelector('[data-eye="hide"]').classList.toggle('d-none', !show);
            });
        });
    </script>
</body>
</html>