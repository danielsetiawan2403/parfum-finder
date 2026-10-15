@extends('layouts.admin')

@section('title', 'Chat Konsultasi')

@section('content')
    <div class="page-head">
        <h1 class="page-title">Chat Konsultasi Admin</h1>
        <p class="page-sub">Pilih nama user untuk membuka room chat. Percakapan yang belum dibalas tampil paling atas.</p>
    </div>

    @if ($users->isEmpty())
        <section class="panel">
            <div class="empty">
                <strong>Belum ada pesan masuk</strong>
                Pesan dari member Premium akan muncul di sini.
            </div>
        </section>
    @else
        <section class="panel chat-users">
            @foreach ($users as $user)
                <a href="{{ route('admin.chats.show', $user) }}" class="chat-user {{ $user->unread_count > 0 ? 'is-pending' : '' }}">
                    <span class="chat-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>

                    <span class="chat-user-info">
                        <strong>{{ $user->name }}</strong>
                        <small>{{ $user->username }} &middot; {{ $user->last_message_at->locale('id')->translatedFormat('j M, H:i') }}</small>
                    </span>

                    @if ($user->unread_count > 0)
                        <span class="badge badge--warn">{{ $user->unread_count }} pesan baru</span>
                    @else
                        <span class="badge badge--ok">Sudah dibalas</span>
                    @endif
                </a>
            @endforeach
        </section>
    @endif
@endsection
