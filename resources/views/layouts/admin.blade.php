<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - Parfum Finder</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>

    {{-- Bar atas: hanya tampil di layar kecil --}}
    <header class="topbar">
        <button type="button" class="burger" id="navToggle" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
        <strong>Admin Panel</strong>
    </header>
    <div class="overlay" data-nav-close></div>

    <aside class="sidebar" id="sidebar">
        <h1 class="brand">Admin Panel</h1>

        <nav class="nav" aria-label="Menu admin">
            <a href="{{ route('admin.dashboard') }}" class="nav-link" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>Dashboard</a>
            <a href="{{ route('admin.perfumes.index') }}" class="nav-link" @if (request()->routeIs('admin.perfumes.*')) aria-current="page" @endif>Data Parfum</a>
            <a href="{{ route('admin.users.index') }}" class="nav-link" @if (request()->routeIs('admin.users.*')) aria-current="page" @endif>Data User</a>
            <a href="{{ route('admin.premium.index') }}" class="nav-link" @if (request()->routeIs('admin.premium.*')) aria-current="page" @endif>Member Premium</a>
            <a href="{{ route('admin.reviews.index') }}" class="nav-link" @if (request()->routeIs('admin.reviews.*')) aria-current="page" @endif>Data Ulasan</a>
            <a href="{{ route('admin.chats.index') }}" class="nav-link" @if (request()->routeIs('admin.chats.*')) aria-current="page" @endif>Chat Konsultasi</a>

            <a href="{{ route('perfumes.index') }}" class="nav-link nav-link--muted">Lihat situs</a>

            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="nav-logout">Logout</button>
            </form>
        </nav>
    </aside>

    <main class="main">
        <div class="page @yield('page-class')">
            @foreach (['success', 'error', 'warning'] as $type)
                @if (session($type))
                    <div class="alert alert-{{ $type }}" role="status">{{ session($type) }}</div>
                @endif
            @endforeach

            @yield('content')
        </div>
    </main>

    {{-- Dialog konfirmasi hapus (dipakai form dengan atribut data-confirm) --}}
    <dialog class="confirm" id="confirmDialog" aria-labelledby="confirmTitle">
        <form method="dialog">
            <h2 id="confirmTitle" data-confirm-title>Konfirmasi</h2>
            <p data-confirm-message></p>
            <div class="confirm-actions">
                <button type="submit" value="cancel" class="btn btn-ghost">Batal</button>
                <button type="submit" value="ok" class="btn btn-danger-solid" data-confirm-ok>Hapus</button>
            </div>
        </form>
    </dialog>

    <script>
        (function () {
            var body = document.body;
            var toggle = document.getElementById('navToggle');

            // Menu samping (layar kecil)
            function setNav(open) {
                body.classList.toggle('nav-open', open);
                if (toggle) { toggle.setAttribute('aria-expanded', open ? 'true' : 'false'); }
            }
            if (toggle) { toggle.addEventListener('click', function () { setNav(!body.classList.contains('nav-open')); }); }
            document.querySelectorAll('[data-nav-close]').forEach(function (el) {
                el.addEventListener('click', function () { setNav(false); });
            });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setNav(false); } });

            // Konfirmasi sebelum menghapus
            var dialog = document.getElementById('confirmDialog');
            var pending = null;

            document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    if (!dialog || typeof dialog.showModal !== 'function') {
                        if (window.confirm(form.dataset.confirm)) { form.submit(); }
                        return;
                    }

                    pending = form;
                    dialog.querySelector('[data-confirm-title]').textContent = form.dataset.confirmTitle || 'Konfirmasi';
                    dialog.querySelector('[data-confirm-message]').textContent = form.dataset.confirm;
                    dialog.querySelector('[data-confirm-ok]').textContent = form.dataset.confirmOk || 'Hapus';
                    dialog.showModal();
                });
            });

            if (dialog) {
                dialog.addEventListener('close', function () {
                    if (dialog.returnValue === 'ok' && pending) { pending.submit(); }
                    pending = null;
                    dialog.returnValue = '';
                });
            }

            // Gulir ke pesan error pertama dan ke pesan chat terbaru
            var firstError = document.querySelector('.field-error');
            if (firstError) { firstError.scrollIntoView({ block: 'center' }); }

            document.querySelectorAll('[data-thread]').forEach(function (box) { box.scrollTop = box.scrollHeight; });
        })();
    </script>

    @stack('scripts')
</body>
</html>
