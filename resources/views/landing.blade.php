@extends('layouts.app')

@section('title', 'Parfum Finder')

@section('content')
    {{-- HERO --}}
    <section class="bg-black px-3 py-5">
        <div class="container text-center" style="max-width: 48rem;">
            <h1 class="fw-bold fs-2">Temukan Parfum Sesuai Karakter Kamu</h1>
            <p class="small text-white-50 mx-auto mt-3" style="max-width: 28rem;">
                Sistem informasi untuk membantu menemukan parfum berdasarkan aroma, aktivitas, dan preferensi pribadi.
            </p>

            <div class="row g-3 mt-3">
                @forelse ($perfumes as $perfume)
                    <div class="col-12 col-sm-4">
                        <a href="{{ route('perfumes.show', $perfume) }}"
                           class="pf-card d-block bg-pf-card rounded-3 overflow-hidden text-white text-decoration-none">
                            <div class="pf-img-h">
                                <img src="{{ asset($perfume->image) }}" alt="{{ $perfume->name }}"
                                     onerror="this.style.display='none'">
                            </div>
                            <p class="pf-xs fw-semibold text-center m-0 p-3">{{ $perfume->name }}</p>
                        </a>
                    </div>
                @empty
                    <p class="small text-white-50">Belum ada data parfum.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- JELAJAHI --}}
    <section class="px-3 py-5">
        <div class="container text-center" style="max-width: 48rem;">
            <h2 class="fw-bold fs-2">Jelajahi <span class="text-pf-red-dark">Lebih Lanjut</span></h2>

            <div class="bg-pf-card rounded-3 px-4 py-4 mt-4">
                <h3 class="fs-6 fw-semibold">Explore Parfum</h3>
                <p class="pf-xs text-white-50 mt-2">Lihat lebih banyak parfum beserta aroma dan karakteristiknya.</p>
                <a href="{{ route('perfumes.index') }}" class="btn btn-pf btn-sm rounded-pill px-4 fw-semibold">Explore</a>
            </div>
        </div>
    </section>
@endsection