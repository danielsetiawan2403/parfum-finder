@extends('layouts.admin')

@section('title', 'Chat ' . $user->name)

@section('content')
    <section class="panel chat-card chat-room">
        <div class="chat-head">
            <div class="chat-who">
                <strong>{{ $user->name }}</strong>
                <span>{{ $user->username }}</span>
            </div>
        </div>

        <div class="thread thread--room" data-thread>
            @foreach ($messages as $message)
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
            <label for="reply-body" class="sr-only">Balasan untuk {{ $user->name }}</label>
            <textarea id="reply-body" name="body" rows="2" maxlength="1000"
                      placeholder="Tulis balasan admin..." required
                      class="{{ $errors->has('body') ? 'is-invalid' : '' }}">{{ old('body') }}</textarea>
            @error('body')
                <p class="field-error">{{ $message }}</p>
            @enderror
            <div class="reply-actions">
                <button type="submit" class="btn btn-primary">Kirim balasan</button>
                <a href="{{ route('admin.chats.index') }}" class="btn btn-ghost">&larr; Kembali</a>
            </div>
        </form>
    </section>
@endsection
