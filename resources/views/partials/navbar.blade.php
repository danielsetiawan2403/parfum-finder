@php
    // User yang sedang login (null jika tamu)
    $authUser     = auth()->user();
    // Admin juga dianggap premium oleh isPremium()
    $isPremium    = $authUser?->isPremium() ?? false;
    $isAdmin      = $authUser?->role === 'admin';
    // Tanggal berakhir langganan (null jika admin atau tidak punya langganan)
    $premiumUntil = $authUser?->activeSubscription()?->ends_at;
    // Di halaman Premium (pilih paket, pembayaran, status) navbar hanya menampilkan Logout.
    $premiumFlow  = request()->routeIs('premium', 'premium.*');

    // Menu hamburger hanya dipakai kalau item-nya banyak.
    // Tamu (hanya tombol Login) dan halaman Premium (hanya Logout) cukup satu tombol di kanan.
    $collapsible  = $authUser && ! $premiumFlow;

    // Lonceng notifikasi chat: hanya untuk user premium biasa (bukan admin, bukan alur Premium)
    $showBell      = $authUser && ! $premiumFlow && ! $isAdmin && $isPremium;
    $hasUnreadChat = $showBell && $authUser->hasUnreadChat();

    // Daftar notifikasi lonceng: 5 balasan admin terbaru (yang belum dibaca ditandai)
    $chatNotifs = $showBell
        ? $authUser->chatMessages()->where('sender', 'admin')->latest('id')->limit(5)->get()
        : collect();
@endphp

<header class="bg-pf-red">
    {{-- navbar-expand-lg: di layar lebar menu sejajar, di bawah 992px menu dilipat jadi hamburger --}}
    <nav class="navbar navbar-dark px-3 px-sm-4 py-2 {{ $collapsible ? 'navbar-expand-lg' : '' }}" aria-label="Navigasi utama">
        <div class="container-fluid p-0">
            {{-- Logo: user login ke daftar parfum, tamu ke beranda --}}
            <a href="{{ auth()->check() ? route('perfumes.index') : url('/') }}" class="navbar-brand fw-bold m-0">Parfum Finder</a>

            @if ($collapsible)
                <button class="navbar-toggler border-0 px-2" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mainNav"
                        aria-controls="mainNav" aria-expanded="false" aria-label="Buka atau tutup menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            @endif

            <div id="mainNav" class="{{ $collapsible ? 'collapse navbar-collapse' : 'ms-auto' }}">
                <div class="pf-nav {{ $collapsible ? 'pf-nav--stack' : '' }}">

                    {{-- Pencarian dan filter: tampil di halaman yang menerima $categories (dashboard) --}}
                    @if (isset($categories))
                        <form action="{{ route('perfumes.index') }}" method="GET" class="pf-nav-search" role="search">
                            <label for="nav-q" class="visually-hidden">Cari parfum</label>
                            <input id="nav-q" name="q" type="search" value="{{ request('q') }}" placeholder="Search parfum..."
                                   class="form-control form-control-sm">

                            <label for="nav-kategori" class="visually-hidden">Kategori</label>
                            <select id="nav-kategori" name="kategori" class="form-select form-select-sm">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(request('kategori') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-dark btn-sm">Go</button>
                        </form>
                    @endif

                    @auth
                        {{-- Tombol Panel Admin: hanya untuk admin --}}
                        @if ($isAdmin)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm rounded-pill pf-xs fw-semibold">Panel Admin</a>
                        @endif

                        @if ($premiumFlow)
                            {{-- Di alur Premium: hanya Logout --}}
                        @elseif ($isAdmin)
                            {{-- Admin: tidak ada menu Chat dan tidak ada tombol Upgrade.
                                 Cabang ini HARUS sebelum $isPremium, karena admin juga dianggap premium. --}}
                        @elseif ($isPremium)
                            {{-- User premium: menu Chat --}}
                            <a href="{{ route('chat.index') }}" class="btn btn-pf-gray btn-sm rounded-pill pf-xs fw-semibold">💬 Chat</a>

                            {{-- Lonceng notifikasi: klik membuka daftar notifikasi (dropdown).
                                 Titik merah muncul jika ada balasan admin yang belum dibaca.
                                 Klik salah satu notifikasi mengarahkan ke room Chat Konsultasi. --}}
                            <div class="dropdown pf-notif">
                                <button type="button"
                                        class="btn btn-pf-gray btn-sm rounded-pill pf-xs fw-semibold pf-bell"
                                        data-bs-toggle="dropdown" aria-expanded="false"
                                        aria-label="{{ $hasUnreadChat ? 'Notifikasi: ada balasan baru dari admin' : 'Notifikasi' }}">
                                    🔔
                                    @if ($hasUnreadChat)
                                        <span class="pf-bell-dot" aria-hidden="true"></span>
                                    @endif
                                </button>

                                <div class="dropdown-menu dropdown-menu-dark dropdown-menu-end pf-notif-menu shadow">
                                    <h6 class="dropdown-header fw-bold">Notifikasi</h6>

                                    @forelse ($chatNotifs as $notif)
                                        @php $unread = is_null($notif->read_at); @endphp
                                        <a href="{{ route('chat.index') }}" class="dropdown-item pf-notif-item {{ $unread ? '' : 'opacity-50' }}">
                                            <div class="d-flex align-items-start gap-2">
                                                <span class="fs-5 lh-1">💬</span>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="pf-xs {{ $unread ? 'fw-bold' : '' }}">Admin membalas pesanmu</div>
                                                    <div class="pf-xs text-white-50 text-truncate">{{ \Illuminate\Support\Str::limit($notif->body, 60) }}</div>
                                                    <div class="text-white-50" style="font-size: .65rem;">{{ $notif->created_at->locale('id')->diffForHumans() }}</div>
                                                </div>
                                                @if ($unread)
                                                    <span class="pf-notif-new" aria-label="Belum dibaca"></span>
                                                @endif
                                            </div>
                                        </a>
                                    @empty
                                        <div class="dropdown-item-text pf-xs text-white-50 py-3 text-center">Belum ada notifikasi.</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Badge masa aktif premium --}}
                            @if ($premiumUntil)
                                <a href="{{ route('premium') }}" class="btn btn-light btn-sm rounded-pill pf-xs fw-semibold">
                                    👑 Premium s/d {{ $premiumUntil->locale('id')->translatedFormat('j M Y') }}
                                </a>
                            @endif
                        @else
                            {{-- User biasa: tombol upgrade --}}
                            <a href="{{ route('premium') }}" class="btn btn-pf btn-sm rounded-pill pf-xs fw-semibold">Upgrade Premium</a>
                        @endif

                        {{-- Tombol logout --}}
                        <form action="{{ route('logout') }}" method="POST" class="pf-nav-logout">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-sm rounded-pill pf-xs fw-semibold">Logout</button>
                        </form>
                    @else
                        {{-- Tamu: tombol login --}}
                        <a href="{{ route('login') }}" class="btn btn-sm text-white fw-semibold">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
