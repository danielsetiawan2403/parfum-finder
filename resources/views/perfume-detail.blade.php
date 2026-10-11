@extends('layouts.app')

@section('title', $perfume->name . ' - Parfum Finder')

@section('content')
    {{-- Judul --}}
    <section class="bg-black text-center px-3 py-4">
        <h1 class="fw-bold fs-3 m-0">{{ $perfume->name }}</h1>
    </section>

    <section class="px-3 py-4">
        <div class="container" style="max-width: 56rem;">

            @if (session('success'))
                <div role="alert" class="alert alert-success small">{{ session('success') }}</div>
            @endif

            {{-- Baris 1: gambar, aroma, informasi --}}
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <div class="pf-img-h rounded-3" style="height: 13rem;">
                        <img src="{{ asset($perfume->image) }}" alt="{{ $perfume->name }}"
                             onerror="this.style.display='none'">
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="bg-pf-card rounded-3 p-3 h-100">
                        <h2 class="fs-6 fw-bold text-pf-red">Aroma</h2>
                        @if ($perfume->top_notes)
                            <p class="pf-xs mb-2"><span class="fw-semibold">Top Notes:</span> {{ $perfume->top_notes }}</p>
                        @endif
                        @if ($perfume->middle_notes)
                            <p class="pf-xs mb-2"><span class="fw-semibold">Middle Notes:</span> {{ $perfume->middle_notes }}</p>
                        @endif
                        @if ($perfume->base_notes)
                            <p class="pf-xs mb-0"><span class="fw-semibold">Bottom Notes:</span> {{ $perfume->base_notes }}</p>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="bg-pf-card rounded-3 p-3 h-100">
                        <h2 class="fs-6 fw-bold text-pf-red">Informasi</h2>
                        <p class="pf-xs mb-1"><span class="fw-semibold">Kategori:</span> {{ $perfume->category }}</p>
                        <p class="pf-xs mb-0">
                            <span class="fw-semibold">Harga:</span>
                            @if ($perfume->price > 0)
                                Rp {{ number_format($perfume->price, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Baris 2: deskripsi, lalu ulasan --}}
            <div class="row g-3 mt-0">
                <div class="col-12 col-md-6">
                    <div class="bg-pf-card rounded-3 p-3 h-100">
                        <h2 class="fs-6 fw-bold text-pf-red">Deskripsi</h2>
                        <p class="pf-xs mb-0" style="line-height: 1.6;">{{ $perfume->description ?: 'Deskripsi belum tersedia.' }}</p>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    @if ($canReview)
                        <div class="d-flex flex-column gap-3">

                            {{-- Form tulis ulasan --}}
                            <div class="bg-pf-card rounded-3 p-3">
                                <h2 class="fs-6 fw-bold text-pf-red">Tulis Ulasan</h2>

                                <form action="{{ route('reviews.store', $perfume) }}" method="POST" novalidate>
                                    @csrf

                                    <div class="mb-2">
                                        <div class="pf-stars" role="radiogroup" aria-label="Rating bintang">
                                            @for ($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" id="star-{{ $i }}" value="{{ $i }}"
                                                       @checked((int) old('rating') === $i)>
                                                <label for="star-{{ $i }}" title="{{ $i }} bintang">
                                                    <span class="visually-hidden">{{ $i }} bintang</span>★
                                                </label>
                                            @endfor
                                        </div>
                                        @error('rating')
                                            <div class="pf-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <label for="review-body" class="visually-hidden">Ulasan</label>
                                    <textarea id="review-body" name="body" rows="3" maxlength="1000"
                                              placeholder="Tulis pengalaman kamu..."
                                              class="form-control pf-xs mb-2">{{ old('body') }}</textarea>
                                    @error('body')
                                        <div class="pf-error mb-2">{{ $message }}</div>
                                    @enderror

                                    <button type="submit" class="btn btn-pf-red btn-sm rounded-pill px-3 pf-xs fw-semibold">Kirim</button>
                                </form>
                            </div>

                            {{-- Daftar ulasan --}}
                            <div class="bg-pf-card rounded-3 p-3">
                                <h2 class="fs-6 fw-bold text-pf-red">Ulasan Premium</h2>

                                @if ($reviews->isNotEmpty())
                                    <p class="text-white-50 mb-2" style="font-size: .7rem;">
                                        <span class="text-warning">★</span> {{ number_format($average, 1) }} dari 5
                                        &middot; {{ $reviews->count() }} ulasan
                                    </p>
                                @endif

                                <div class="pf-review-list">
                                    @forelse ($reviews as $review)
                                        <div class="pb-2 mb-2 {{ $loop->last ? '' : 'border-bottom border-secondary' }}">
                                            <div class="small fw-bold">
                                                {{ $review->user?->username ?? $review->user?->name }}
                                                @if ($review->user_id === auth()->id())
                                                    <span class="badge text-bg-secondary fw-normal">Kamu</span>
                                                @endif
                                            </div>
                                            <div class="text-white-50" style="font-size: .65rem;">
                                                {{ $review->created_at->locale('id')->translatedFormat('j M Y H:i') }}
                                            </div>
                                            <div class="my-1" style="line-height: 1;" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="{{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}">★</span>
                                                @endfor
                                            </div>
                                            <div class="pf-xs">{!! nl2br(e($review->body)) !!}</div>
                                        </div>
                                    @empty
                                        <p class="pf-xs text-white-50 m-0">Belum ada ulasan untuk parfum ini. Jadilah yang pertama!</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Terkunci untuk tamu dan user non-premium --}}
                        <div class="bg-pf-card rounded-3 p-3 h-100">
                            <h2 class="pf-xs fw-bold">🔒 Ulasan hanya untuk Premium</h2>
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-pf btn-sm rounded-pill pf-xs fw-semibold mt-2">Login</a>
                            @else
                                <a href="{{ route('premium') }}" class="btn btn-pf btn-sm rounded-pill pf-xs fw-semibold mt-2">Upgrade Premium</a>
                            @endguest
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ auth()->check() ? route('perfumes.index') : url('/') }}" class="btn btn-pf-red btn-sm rounded-pill pf-xs fw-semibold px-3">&larr; Kembali</a>
            </div>
        </div>
    </section>
@endsection