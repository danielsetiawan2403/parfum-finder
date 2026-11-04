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

        {{-- data-id dipakai JavaScript supaya pesan yang sama tidak tampil dua kali --}}
        <div class="thread thread--room" data-thread id="thread">
            @foreach ($messages as $message)
                <div class="msg {{ $message->sender === 'user' ? 'msg--user' : 'msg--admin' }}" data-id="{{ $message->id }}">
                    <div class="bubble">{{ $message->body }}</div>
                    <time datetime="{{ $message->created_at->toIso8601String() }}">
                        {{ $message->sender === 'user' ? $user->name : 'Kamu (admin)' }},
                        {{ $message->created_at->locale('id')->translatedFormat('j M, H:i') }}
                    </time>
                </div>
            @endforeach
        </div>

        <form id="reply-form" action="{{ route('admin.chats.reply', $user) }}" method="POST" class="reply">
            @csrf
            <label for="reply-body" class="sr-only">Balasan untuk {{ $user->name }}</label>
            <textarea id="reply-body" name="body" rows="2" maxlength="1000"
                      placeholder="Tulis balasan admin..." required
                      class="{{ $errors->has('body') ? 'is-invalid' : '' }}">{{ old('body') }}</textarea>
            {{-- Pesan error validasi (dari server, atau dari JavaScript saat kirim gagal) --}}
            <p id="reply-error" class="field-error" @unless ($errors->has('body')) hidden @endunless>{{ $errors->first('body') }}</p>
            <div class="reply-actions">
                <button type="submit" class="btn btn-primary">Kirim balasan</button>
                <a href="{{ route('admin.chats.index') }}" class="btn btn-ghost">&larr; Kembali</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/live-chat.js') }}?v=1"></script>
    <script>
        (function () {
            var box = document.getElementById('thread');
            var form = document.getElementById('reply-form');
            var errorBox = document.getElementById('reply-error');
            if (!box || !form || !window.LiveChat) { return; }

            var userName = @json($user->name);

            // Membuat satu pesan dengan tampilan yang sama seperti yang dibuat Blade di atas.
            function render(m) {
                var fromUser = m.sender === 'user';

                var wrap = document.createElement('div');
                wrap.className = 'msg ' + (fromUser ? 'msg--user' : 'msg--admin');

                var bubble = document.createElement('div');
                bubble.className = 'bubble';
                bubble.textContent = m.body;

                var time = document.createElement('time');
                time.textContent = (fromUser ? userName : 'Kamu (admin)') + ', ' + m.time;

                wrap.appendChild(bubble);
                wrap.appendChild(time);
                return wrap;
            }

            function showError(text) {
                errorBox.textContent = text;
                errorBox.hidden = !text;
            }

            var live = LiveChat.watchMessages({
                box: box,
                pollUrl: @json(route('admin.chats.poll', $user)),
                lastId: {{ (int) ($messages->last()->id ?? 0) }},
                render: render
            });

            // Balasan yang baru dikirim langsung tampil tanpa menunggu polling berikutnya.
            LiveChat.bindForm(form, {
                onMessage: function (m) { live.add([m]); box.scrollTop = box.scrollHeight; },
                onError: showError
            });
        })();
    </script>
@endpush
