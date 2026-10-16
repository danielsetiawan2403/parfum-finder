@extends('layouts.admin')

@section('title', 'Data Ulasan')

@section('content')
    <div class="page-head">
        <h1 class="page-title">Data Ulasan</h1>
        <p class="page-sub">Pantau ulasan dari member Premium dan hapus ulasan yang melanggar.</p>
    </div>

    {{-- Ringkasan: mengikuti filter yang sedang dipakai --}}
    <div class="menu-grid" style="margin-bottom: 24px;">
        <div class="menu-card">
            <h2>Total Ulasan</h2>
            <p>{{ $isFiltered ? 'Sesuai filter yang dipilih' : 'Dari semua parfum' }}</p>
            <span class="stat">{{ $total }} ulasan</span>
        </div>
        <div class="menu-card">
            <h2>Rata-rata Rating</h2>
            <p>{{ $isFiltered ? 'Sesuai filter yang dipilih' : 'Dari semua parfum' }}</p>
            <span class="stat">{{ $average !== null ? number_format($average, 1, ',', '.') . ' dari 5' : 'Belum ada' }}</span>
        </div>
        <div class="menu-card">
            <h2>Rating Rendah</h2>
            <p>Ulasan dengan 1 sampai 2 bintang</p>
            <span class="stat {{ $lowCount > 0 ? 'stat--alert' : '' }}">{{ $lowCount }} ulasan</span>
        </div>
    </div>

    <div class="toolbar">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="search" role="search">
            <label for="q" class="sr-only">Cari ulasan</label>
            <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Cari isi ulasan atau nama user...">

            <label for="parfum" class="sr-only">Parfum</label>
            <select id="parfum" name="parfum" onchange="this.form.submit()">
                <option value="">Semua parfum</option>
                @foreach ($perfumes as $perfume)
                    <option value="{{ $perfume->id }}" @selected($perfumeId === $perfume->id)>{{ $perfume->name }}</option>
                @endforeach
            </select>

            <label for="rating" class="sr-only">Rating</label>
            <select id="rating" name="rating" onchange="this.form.submit()">
                <option value="">Semua rating</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected($rating === $i)>{{ $i }} bintang</option>
                @endfor
            </select>

            <label for="urut" class="sr-only">Urutkan</label>
            <select id="urut" name="urut" onchange="this.form.submit()">
                <option value="terbaru" @selected($sort === 'terbaru')>Terbaru</option>
                <option value="terlama" @selected($sort === 'terlama')>Terlama</option>
                <option value="rating-terendah" @selected($sort === 'rating-terendah')>Rating terendah</option>
                <option value="rating-tertinggi" @selected($sort === 'rating-tertinggi')>Rating tertinggi</option>
            </select>

            <button type="submit" class="btn btn-ghost">Cari</button>
            @if ($isCustom)
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
    </div>

    <section class="panel">
        @if ($reviews->isEmpty())
            <div class="empty">
                @if ($isFiltered)
                    <strong>Tidak ada ulasan yang cocok</strong>
                    Coba ubah filter atau kata kunci.
                @else
                    <strong>Belum ada ulasan</strong>
                    Ulasan dari member Premium akan muncul di sini.
                @endif
            </div>
        @else
            <div class="review-list">
                @foreach ($reviews as $review)
                    @php $name = $review->user->name ?? '(user dihapus)'; @endphp
                    <article class="review {{ $review->rating <= 2 ? 'is-low' : '' }}">
                        <span class="review-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</span>

                        <div class="review-main">
                            <div class="review-head">
                                <strong>{{ $name }}</strong>
                                <span class="review-meta">
                                    {{ $review->user->username ?? '' }} &middot; pada
                                    <span class="review-perfume">{{ $review->perfume->name ?? '(parfum dihapus)' }}</span>
                                </span>
                                <time class="review-time" datetime="{{ $review->created_at->toIso8601String() }}">
                                    {{ $review->created_at->locale('id')->translatedFormat('j M Y, H:i') }}
                                </time>
                            </div>

                            <div class="review-stars" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'on' : '' }}">★</span>
                                @endfor
                            </div>

                            <p class="review-body">{{ $review->body }}</p>
                        </div>

                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="review-act"
                              data-confirm="Ulasan dari &quot;{{ $name }}&quot; akan dihapus permanen. Akun user tidak ikut terhapus."
                              data-confirm-title="Hapus ulasan?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </article>
                @endforeach
            </div>

            {{ $reviews->links('admin.partials.pagination') }}
        @endif
    </section>
@endsection
