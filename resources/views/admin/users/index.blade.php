@extends('layouts.admin')

@section('title', 'Data User')

@section('content')
    <div class="page-head">
        <h1 class="page-title">Kelola Data User (Customer)</h1>
        <p class="page-sub">Akun admin tidak ditampilkan di daftar ini.</p>
    </div>

    <div class="toolbar">
        <form action="{{ route('admin.users.index') }}" method="GET" class="search" role="search">
            <label for="q" class="sr-only">Cari user</label>
            <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Cari nama, email, atau username...">
            <button type="submit" class="btn btn-ghost">Cari</button>
            @if ($q !== '')
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
    </div>

    <section class="panel">
        @if ($users->isEmpty())
            <div class="empty">
                @if ($q !== '')
                    <strong>Tidak ada user yang cocok</strong>
                    Coba kata kunci lain.
                @else
                    <strong>Belum ada user terdaftar</strong>
                    Akun customer akan muncul di sini setelah mereka registrasi.
                @endif
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th class="col-act">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            @php $active = $user->subscriptions->first(); @endphp
                            <tr>
                                <td class="col-no">{{ $users->firstItem() + $loop->index }}</td>
                                <td data-label="Nama Lengkap" class="strong">{{ $user->name }}</td>
                                <td data-label="Email" style="overflow-wrap: anywhere;">{{ $user->email }}</td>
                                <td data-label="Username">{{ $user->username }}</td>
                                <td data-label="Status">
                                    @if ($active)
                                        <span class="badge badge--pro">Premium s/d {{ $active->ends_at->locale('id')->translatedFormat('j M Y') }}</span>
                                    @else
                                        <span class="badge badge--free">Gratis</span>
                                    @endif
                                </td>
                                <td class="col-act">
                                    {{-- data-reason: dialog konfirmasi meminta alasan, lalu diisikan ke input "reason" --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                          data-confirm="Akun &quot;{{ $user->name }}&quot; akan dihapus dan tidak bisa login lagi. Ulasan dan riwayat chat-nya ikut dihapus, sedangkan riwayat pembayarannya tetap disimpan untuk laporan pendapatan."
                                          data-confirm-title="Hapus user?"
                                          data-reason>
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="reason" value="">
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links('admin.partials.pagination') }}
        @endif
    </section>
@endsection
