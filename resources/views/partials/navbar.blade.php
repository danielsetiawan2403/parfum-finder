@php
    $authUser     = auth()->user();
    $isPremium    = $authUser?->isPremium() ?? false;
    $premiumUntil = $authUser?->activeSubscription()?->ends_at;
    // Di halaman Premium (pilih paket, pembayaran, status) navbar hanya menampilkan Logout.
    $premiumFlow  = request()->routeIs('premium', 'premium.*');
@endphp

    <header class="bg-pf-red">
        <div class="container-fluid px-3 px-sm-4 py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <a href="{{ auth()->check() ? route('perfumes.index') : url('/') }}" class="text-white fw-bold text-decoration-none">Parfum Finder</a>

            <div class="d-flex flex-wrap align-items-center gap-2">

                {{-- Pencarian dan filter: tampil di halaman yang menerima $categories (dashboard) --}}
                @if (isset($categories))
                    <form action="{{ route('perfumes.index') }}" method="GET" class="d-flex align-items-center gap-1">
                        <label for="nav-q" class="visually-hidden">Cari parfum</label>
                        <input id="nav-q" name="q" type="search" value="{{ request('q') }}" placeholder="Search parfum..."
                               class="form-control form-control-sm pf-xs" style="width: 9rem;">

                        <label for="nav-kategori" class="visually-hidden">Kategori</label>
                        <select id="nav-kategori" name="kategori" class="form-select form-select-sm pf-xs" style="width: auto;">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('kategori') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-dark btn-sm pf-xs">Go</button>
                    </form>
                @endif

                @auth
                    @if ($premiumFlow)
                        {{-- hanya Logout --}}
                    @elseif ($isPremium)
                        <a href="{{ route('chat.index') }}" class="btn btn-pf-gray btn-sm rounded-pill pf-xs fw-semibold">💬 Chat</a>

                        @if ($premiumUntil)
                            <a href="{{ route('premium') }}" class="btn btn-light btn-sm rounded-pill pf-xs fw-semibold">
                                👑 Premium s/d {{ $premiumUntil->locale('id')->translatedFormat('j M Y') }}
                            </a>
                        @endif
                    @else
                        <a href="{{ route('premium') }}" class="btn btn-pf btn-sm rounded-pill pf-xs fw-semibold">Upgrade Premium</a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-dark btn-sm rounded-pill pf-xs fw-semibold">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm text-white fw-semibold">Login</a>
                @endauth
            </div>
        </div>
    </header>