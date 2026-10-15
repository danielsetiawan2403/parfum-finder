{{-- Memakai layout utama aplikasi --}}
@extends('layouts.app')

{{-- Judul tab browser --}}
@section('title', $perfume->name . ' - Parfum Finder')

@section('content')
    {{-- Judul halaman: nama parfum --}}
    <section class="bg-black text-center px-3 py-4">
        <h1 class="fw-bold fs-3 m-0">{{ $perfume->name }}</h1>
    </section>

    <section class="px-3 py-4">
        <div class="container" style="max-width: 56rem;">

            {{-- Pesan sukses (misal setelah ulasan terkirim) --}}
            @if (session('success'))
                <div role="alert" class="alert alert-success small">{{ session('success') }}</div>
            @endif

            {{-- Pesan peringatan (misal admin mencoba menulis ulasan) --}}
            @if (session('warning'))
                <div role="alert" class="alert alert-warning small">{{ session('warning') }}</div>
            @endif

            {{-- Baris 1: gambar, aroma, informasi --}}
            <div class="row g-3">
                {{-- Kolom gambar parfum --}}
                <div class="col-12 col-md-3">
                    <div class="pf-img-h rounded-3" style="height: 13rem;">
                        {{-- Jika gambar gagal dimuat, sembunyikan elemen img --}}
                        <img src="{{ asset($perfume->image) }}" alt="{{ $perfume->name }}"
                             onerror="this.style.display='none'">
                    </div>
                </div>

                {{-- Kolom aroma (top, middle, base notes) --}}
                <div class="col-12 col-md-6">
                    <div class="bg-pf-card rounded-3 p-3 h-100">
                        <h2 class="fs-6 fw-bold text-pf-red">Aroma</h2>
                        {{-- Tiap note hanya tampil jika datanya ada --}}
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

                {{-- Kolom informasi: kategori & harga --}}
                <div class="col-12 col-md-3">
                    <div class="bg-pf-card rounded-3 p-3 h-100">
                        <h2 class="fs-6 fw-bold text-pf-red">Informasi</h2>
                        <p class="pf-xs mb-1"><span class="fw-semibold">Kategori:</span> {{ $perfume->category }}</p>
                        <p class="pf-xs mb-0">
                            <span class="fw-semibold">Harga:</span>
                            {{-- Format harga ke Rupiah, tampilkan "-" jika belum ada harga --}}
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
                {{-- Kolom deskripsi --}}
                <div class="col-12 col-md-6">
                    <div class="bg-pf-card rounded-3 p-3 h-100">
                        <h2 class="fs-6 fw-bold text-pf-red">Deskripsi</h2>
                        <p class="pf-xs mb-0" style="line-height: 1.6;">{{ $perfume->description ?: 'Deskripsi belum tersedia.' }}</p>
                    </div>
                </div>

                {{-- Kolom ulasan --}}
                <div class="col-12 col-md-6">
                    {{-- Admin & user premium boleh MELIHAT ulasan --}}
                    @if ($canViewReviews)
                        <div class="d-flex flex-column gap-3">

                            {{-- Form tulis ulasan: HANYA untuk user premium (admin tidak melihat form ini) --}}
                            @if ($canWriteReview)
                                <div class="bg-pf-card rounded-3 p-3">
                                    <h2 class="fs-6 fw-bold text-pf-red">Tulis Ulasan</h2>

                                    <form action="{{ route('reviews.store', $perfume) }}" method="POST" novalidate>
                                        {{-- Token CSRF wajib untuk form POST --}}
                                        @csrf

                                        {{-- Input rating bintang (5 sampai 1, dibalik lewat CSS) --}}
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
                                            {{-- Pesan error validasi rating --}}
                                            @error('rating')
                                                <div class="pf-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Input isi ulasan --}}
                                        <label for="review-body" class="visually-hidden">Ulasan</label>
                                        <textarea id="review-body" name="body" rows="3" maxlength="1000"
                                                  placeholder="Tulis pengalaman kamu..."
                                                  class="form-control pf-xs mb-2">{{ old('body') }}</textarea>
                                        {{-- Pesan error validasi isi ulasan --}}
                                        @error('body')
                                            <div class="pf-error mb-2">{{ $message }}</div>
                                        @enderror

                                        <button type="submit" class="btn btn-pf-red btn-sm rounded-pill px-3 pf-xs fw-semibold">Kirim</button>
                                    </form>
                                </div>
                            @endif

                            {{-- Daftar ulasan: tampil untuk admin & user premium --}}
                            <div class="bg-pf-card rounded-3 p-3">
                                <h2 class="fs-6 fw-bold text-pf-red">Ulasan Premium</h2>

                                {{-- Ringkasan rata-rata rating & jumlah ulasan --}}
                                @if ($reviews->isNotEmpty())
                                    <p class="text-white-50 mb-2" style="font-size: .7rem;">
                                        <span class="text-warning">★</span> {{ number_format($average, 1) }} dari 5
                                        &middot; {{ $reviews->count() }} ulasan
                                    </p>
                                @endif

                                <div class="pf-review-list">
                                    @forelse ($reviews as $review)
                                        {{-- Garis pemisah antar ulasan (kecuali ulasan terakhir) --}}
                                        <div class="pb-2 mb-2 {{ $loop->last ? '' : 'border-bottom border-secondary' }}">
                                            <div class="small fw-bold">
                                                {{ $review->user?->username ?? $review->user?->name }}
                                                {{-- Penanda untuk ulasan milik user yang sedang login --}}
                                                @if ($review->user_id === auth()->id())
                                                    <span class="badge text-bg-secondary fw-normal">Kamu</span>
                                                @endif
                                            </div>
                                            {{-- Tanggal ulasan dalam format Indonesia --}}
                                            <div class="text-white-50" style="font-size: .65rem;">
                                                {{ $review->created_at->locale('id')->translatedFormat('j M Y H:i') }}
                                            </div>
                                            {{-- Bintang rating: kuning jika terisi, abu-abu jika kosong --}}
                                            <div class="my-1" style="line-height: 1;" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="{{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}">★</span>
                                                @endfor
                                            </div>
                                            {{-- Isi ulasan: di-escape dulu (e) agar aman dari XSS, baru newline diubah jadi <br> --}}
                                            <div class="pf-xs">{!! nl2br(e($review->body)) !!}</div>
                                        </div>
                                    @empty
                                        {{-- Pesan jika belum ada ulasan; ajakan menulis hanya untuk yang boleh menulis --}}
                                        <p class="pf-xs text-white-50 m-0">
                                            Belum ada ulasan untuk parfum ini.
                                            @if ($canWriteReview) Jadilah yang pertama! @endif
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Terkunci untuk tamu dan user non-premium --}}
                        <div class="bg-pf-card rounded-3 p-3 h-100">
                            <h2 class="pf-xs fw-bold">🔒 Ulasan hanya untuk Premium</h2>
                            @guest
                                {{-- Tamu diarahkan ke halaman login --}}
                                <a href="{{ route('login') }}" class="btn btn-pf btn-sm rounded-pill pf-xs fw-semibold mt-2">Login</a>
                            @else
                                {{-- User non-premium diarahkan ke halaman upgrade --}}
                                <a href="{{ route('premium') }}" class="btn btn-pf btn-sm rounded-pill pf-xs fw-semibold mt-2">Upgrade Premium</a>
                            @endguest
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tombol kembali: user login ke daftar parfum, tamu ke beranda --}}
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ auth()->check() ? route('perfumes.index') : url('/') }}" class="btn btn-pf-red btn-sm rounded-pill pf-xs fw-semibold px-3">&larr; Kembali</a>
            </div>
        </div>
    </section>
@endsection