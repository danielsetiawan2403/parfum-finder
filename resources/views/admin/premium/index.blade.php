@extends('layouts.admin')

@section('title', 'Member Premium')

@section('content')
    <div class="page-head">
        <h1 class="page-title">Member Premium</h1>
        <p class="page-sub">Riwayat langganan Premium: tanggal mulai, tanggal selesai, dan harga.</p>
    </div>

    <div class="menu-grid" style="margin-bottom: 24px;">
        <div class="menu-card">
            <h2>Member Aktif</h2>
            <p>Langganan yang masih berjalan</p>
            <span class="stat">{{ $totalAktif }} user</span>
        </div>
        <div class="menu-card">
            <h2>Total Pendapatan</h2>
            <p>Dari semua langganan yang sudah dibayar</p>
            <span class="stat">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="toolbar">
        <form action="{{ route('admin.premium.index') }}" method="GET" class="search" role="search">
            <label for="q" class="sr-only">Cari member</label>
            <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Cari nama, email, username, atau invoice...">

            <label for="status" class="sr-only">Status</label>
            <select id="status" name="status">
                <option value="">Semua status</option>
                <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                <option value="berakhir" @selected($status === 'berakhir')>Berakhir</option>
            </select>

            <button type="submit" class="btn btn-ghost">Cari</button>
            @if ($q !== '' || $status !== '')
                <a href="{{ route('admin.premium.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>

        {{-- Laporan pendapatan: halaman siap cetak, dibuka di tab baru --}}
        <a href="{{ route('admin.premium.report') }}" target="_blank" rel="noopener" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z"/></svg>
            Cetak Laporan
        </a>
    </div>

    <section class="panel">
        @if ($subscriptions->isEmpty())
            <div class="empty">
                <strong>Belum ada member premium</strong>
                Data akan muncul setelah ada user yang menyelesaikan pembayaran.
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th>User</th>
                            <th>Invoice</th>
                            <th>Paket</th>
                            <th>Harga</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscriptions as $s)
                            @php $aktif = $s->ends_at && $s->ends_at->isFuture(); @endphp
                            <tr>
                                <td class="col-no">{{ $subscriptions->firstItem() + $loop->index }}</td>
                                <td data-label="User">
                                    <span class="strong">{{ $s->user->name ?? '(user dihapus)' }}</span>
                                    {{-- Akun sudah dihapus admin, tapi pembayarannya tetap tercatat --}}
                                    @if ($s->user?->trashed())
                                        <span class="badge badge--free">akun dihapus</span>
                                    @endif
                                    <br>
                                    <small style="color: var(--muted); overflow-wrap: anywhere;">{{ $s->user->email ?? '' }}</small>
                                </td>
                                <td data-label="Invoice">{{ $s->invoice }}</td>
                                <td data-label="Paket">{{ $s->label }}</td>
                                <td data-label="Harga">Rp {{ number_format($s->price, 0, ',', '.') }}</td>
                                <td data-label="Mulai">{{ $s->starts_at?->locale('id')->translatedFormat('j M Y') }}</td>
                                <td data-label="Selesai">{{ $s->ends_at?->locale('id')->translatedFormat('j M Y') }}</td>
                                <td data-label="Status">
                                    @if ($aktif)
                                        <span class="badge badge--ok">Aktif</span>
                                    @else
                                        <span class="badge badge--free">Berakhir</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $subscriptions->links('admin.partials.pagination') }}
        @endif
    </section>
@endsection
