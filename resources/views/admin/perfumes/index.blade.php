@extends('layouts.admin')

@section('title', 'Data Parfum')

@section('content')
    <div class="page-head">
        <h1 class="page-title">Data Parfum</h1>
        <p class="page-sub">Tambah, ubah, atau hapus parfum yang tampil di situs.</p>
    </div>

    <div class="toolbar">
        <form action="{{ route('admin.perfumes.index') }}" method="GET" class="search" role="search">
            <label for="q" class="sr-only">Cari nama parfum</label>
            <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Cari nama parfum...">

            <label for="kategori" class="sr-only">Kategori</label>
            <select id="kategori" name="kategori" style="width: auto;">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected($kategori === $category)>{{ $category }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-ghost">Cari</button>
            @if ($q !== '' || $kategori)
                <a href="{{ route('admin.perfumes.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.perfumes.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Parfum
        </a>
    </div>

    <section class="panel">
        @if ($perfumes->isEmpty())
            <div class="empty">
                @if ($q !== '' || $kategori)
                    <strong>Tidak ada parfum yang cocok</strong>
                    Coba kata kunci atau kategori lain.
                @else
                    <strong>Belum ada parfum</strong>
                    Tambahkan parfum pertama untuk mengisi katalog.
                    <br><a href="{{ route('admin.perfumes.create') }}" class="btn btn-primary">Tambah Parfum</a>
                @endif
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th>Parfum</th>
                            <th>Aroma</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th class="col-act">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($perfumes as $perfume)
                            <tr>
                                <td class="col-no">{{ $perfumes->firstItem() + $loop->index }}</td>

                                <td>
                                    <div class="perfume-cell">
                                        <div class="thumb" aria-hidden="true">
                                            {{ mb_strtoupper(mb_substr($perfume->name, 0, 1)) }}
                                            @if ($perfume->image)
                                                <img src="{{ asset($perfume->image) }}" alt="" loading="lazy" onerror="this.remove()">
                                            @endif
                                        </div>
                                        <span class="strong">{{ $perfume->name }}</span>
                                    </div>
                                </td>

                                <td data-label="Aroma">
                                    @if ($perfume->top_notes || $perfume->middle_notes || $perfume->base_notes)
                                        <dl class="notes">
                                            @foreach (['top_notes' => 'Top', 'middle_notes' => 'Middle', 'base_notes' => 'Base'] as $field => $label)
                                                @if ($perfume->$field)
                                                    <div><dt>{{ $label }}</dt><dd>{{ $perfume->$field }}</dd></div>
                                                @endif
                                            @endforeach
                                        </dl>
                                    @else
                                        <span class="muted">Belum diisi</span>
                                    @endif
                                </td>

                                <td data-label="Kategori">{{ $perfume->category }}</td>
                                <td data-label="Harga" style="white-space: nowrap;">Rp {{ number_format($perfume->price, 0, ',', '.') }}</td>

                                <td class="col-act">
                                    <div class="cell-actions">
                                        <a href="{{ route('admin.perfumes.edit', $perfume) }}" class="btn btn-edit">Edit</a>

                                        <form action="{{ route('admin.perfumes.destroy', $perfume) }}" method="POST"
                                              data-confirm="Parfum &quot;{{ $perfume->name }}&quot; dan semua ulasannya akan dihapus permanen."
                                              data-confirm-title="Hapus parfum?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $perfumes->links('admin.partials.pagination') }}
        @endif
    </section>
@endsection
