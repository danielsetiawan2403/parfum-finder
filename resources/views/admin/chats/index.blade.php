@extends('layouts.admin')

@section('title', 'Chat Konsultasi')

@section('content')
    <div class="page-head">
        <h1 class="page-title">Chat Konsultasi Admin</h1>
        <p class="page-sub">Balas pertanyaan user premium. Percakapan yang belum dibalas tampil paling atas.</p>
    </div>

    @if ($users->isEmpty())
        <section class="panel">
            <div class="empty">
                <strong>Belum ada pesan masuk</strong>
                Pesan dari member Premium akan muncul di sini.
            </div>
        </section>
    @else
        <div class="chat-list">
            @foreach ($users as $user)
                @php
                    $all      = $user->chatMessages;
                    $shown    = $all->slice(-20);
                    $pending  = $all->last()->sender === 'user';
                    $bag      = $errors->getBag("reply-{$user->id}");
                    $hasError = $bag->has('body');
                @endphp

                <section class="panel chat-card" id="chat-{{ $user->id }}">
                    <div class="chat-head">
                        <div class="chat-who">
                            <strong>{{ $user->name }}</strong>
                            <span>{{ $user->username }}</span>
                        </div>

                        @if ($pending)
                            <span class="badge badge--warn">Perlu dibalas</span>
                        @else
                            <span class="badge badge--ok">Sudah dibalas</span>
                        @endif
                    </div>

                    @if ($all->count() > $shown->count())
                        <p class="thread-note">Menampilkan 20 pesan terakhir dari {{ $all->count() }} pesan.</p>
                    @endif

                    <div class="thread" data-thread>
                        @foreach ($shown as $message)
                            <div class="msg {{ $message->sender === 'user' ? 'msg--user' : 'msg--admin' }}">
                                <div class="bubble">{{ $message->body }}</div>
                                <time datetime="{{ $message->created_at->toIso8601String() }}">
                                    {{ $message->sender === 'user' ? $user->name : 'Kamu (admin)' }},
                                    {{ $message->created_at->locale('id')->translatedFormat('j M, H:i') }}
                                </time>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('admin.chats.reply', $user) }}" method="POST" class="reply">
                        @csrf
                        <label for="reply-{{ $user->id }}" class="sr-only">Balasan untuk {{ $user->name }}</label>
                        <textarea id="reply-{{ $user->id }}" name="body" rows="2" maxlength="1000"
                                  placeholder="Tulis balasan admin..." required
                                  class="{{ $hasError ? 'is-invalid' : '' }}">{{ $hasError ? old('body') : '' }}</textarea>
                        @if ($hasError)
                            <p class="field-error">{{ $bag->first('body') }}</p>
                        @endif
                        <button type="submit" class="btn btn-primary">Kirim balasan</button>
                    </form>
                </section>
            @endforeach
        </div>
    @endif
@endsection
