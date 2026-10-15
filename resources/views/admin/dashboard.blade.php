@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="page-head">
        <h1 class="page-title">Dashboard Admin</h1>
        <p class="page-sub">Ringkasan Sistem Parfum Finder</p>
    </div>

    <div class="menu-grid">
        <a href="{{ route('admin.perfumes.index') }}" class="menu-card">
            <h2>Data Parfum</h2>
            <p>Menu untuk manajemen parfum</p>
            <span class="stat">{{ $perfumeCount }} parfum</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="menu-card">
            <h2>Data User</h2>
            <p>Menu untuk manajemen akun</p>
            <span class="stat">{{ $userCount }} user</span>
        </a>

        <a href="{{ route('admin.chats.index') }}" class="menu-card">
            <h2>Chat Konsultasi</h2>
            <p>Menu untuk chat konsultasi dengan customer</p>
            @if ($pendingChats > 0)
                <span class="stat stat--alert">{{ $pendingChats }} perlu dibalas</span>
            @else
                <span class="stat">Semua sudah dibalas</span>
            @endif
        </a>
    </div>

    <section class="info">
        <h2>Informasi</h2>
        <p>Gunakan menu di samping untuk mengelola data parfum, akun user, dan chat konsultasi member Premium.</p>
    </section>
@endsection
