@extends('layouts.app')

@section('title', 'Dashboard - Parfum Finder')

@section('content')
    {{-- Header: sapaan untuk user yang login, judul biasa untuk tamu --}}
    <section class="bg-black text-center px-3 py-5">
        @auth
            <h1 class="fw-bold fs-2 text-break">Hi, {{ auth()->user()->username ?? auth()->user()->name }} 👋</h1>
        @else
            <h1 class="fw-bold fs-2">Explore Parfum</h1>
        @endauth

        <p class="pf-xs text-white-50 mt-2 mb-0">Temukan parfum favoritmu sesuai kategori dan aroma yang diinginkan.</p>

        @if ($q !== '' || $kategori)
            <p class="pf-xs text-white-50 mt-3 mb-0">
                {{ $perfumes->count() }} hasil
                @if ($q !== '') untuk "{{ $q }}" @endif
                @if ($kategori) di kategori {{ $kategori }} @endif
                &middot;
                <a href="{{ route('perfumes.index') }}" class="text-white">Reset</a>
            </p>
        @endif
    </section>

    {{-- Daftar parfum --}}
    <section class="px-3 py-4">
        <div class="container" style="max-width: 64rem;">
            @if ($perfumes->isEmpty())
                <p class="small text-white-50 text-center py-5">Parfum tidak ditemukan. Coba kata kunci atau kategori lain.</p>
            @else
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
                    @foreach ($perfumes as $perfume)
                        <div class="col">
                            <a href="{{ route('perfumes.show', $perfume) }}"
                               class="pf-card d-block h-100 bg-pf-card rounded-3 overflow-hidden text-white text-decoration-none">
                                <div class="pf-img">
                                    <img src="{{ asset($perfume->image) }}" alt="{{ $perfume->name }}"
                                         loading="lazy" onerror="this.style.display='none'">
                                </div>
                                <p class="pf-xs fw-bold text-center lh-sm m-0 px-2 py-2 d-flex align-items-center justify-content-center" style="min-height: 3rem;">{{ $perfume->name }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection