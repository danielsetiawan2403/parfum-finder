@extends('layouts.admin')

@section('title', 'Chat Konsultasi')

@section('content')
    <div class="page-head">
        <h1 class="page-title">Chat Konsultasi Admin</h1>
        <p class="page-sub">Pilih nama user untuk membuka room chat. Percakapan yang belum dibalas tampil paling atas.</p>
    </div>

    {{-- Isi daftar dimuat ulang oleh JavaScript, jadi pesan baru muncul tanpa refresh --}}
    <div id="chat-list">
        @include('admin.chats._list')
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/live-chat.js') }}?v=1"></script>
    <script>
        (function () {
            var target = document.getElementById('chat-list');
            if (!target || !window.LiveChat) { return; }

            LiveChat.watchHtml({ target: target, url: @json(route('admin.chats.index')), interval: 6000 });
        })();
    </script>
@endpush
