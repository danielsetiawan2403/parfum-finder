{{-- Memakai layout utama aplikasi --}}
@extends('layouts.app')

{{-- Judul tab browser --}}
@section('title', 'Chat Konsultasi - Parfum Finder')

@section('content')
    <section class="px-3 py-4">
        <div class="container" style="max-width: 40rem;">

            {{-- Kartu utama chat --}}
            <div class="bg-pf-card rounded-3 p-3 p-md-4">
                <h1 class="fs-6 fw-bold mb-3">💬 Chat Konsultasi Premium</h1>

                {{-- Riwayat pesan --}}
                <div id="chat-box" class="pf-chat-box d-flex flex-column gap-2 mb-3">
                    @forelse ($messages as $message)
                        {{-- $mine = true jika pesan dikirim oleh user (tampil di kanan) --}}
                        @php $mine = $message->sender === 'user'; @endphp

                        {{-- Baris pembungkus: flex-shrink-0 agar tinggi baris tidak ikut dipadatkan/diregangkan.
                             data-id dipakai JavaScript supaya pesan yang sama tidak tampil dua kali. --}}
                        <div class="d-flex flex-shrink-0 {{ $mine ? 'justify-content-end' : 'justify-content-start' }}"
                             data-id="{{ $message->id }}">

                            {{-- Bubble: lebar mengikuti isi teks (fit-content), maksimal 75% dari kotak chat.
                                 Style inline dipakai untuk menimpa aturan lebar/tinggi dari class .pf-bubble. --}}
                            <div class="pf-bubble rounded-3 px-3 py-2 {{ $mine ? 'bg-pf-red' : 'bg-secondary' }}"
                                 style="width: fit-content; max-width: 75%; min-width: 0; height: auto; min-height: 0;">

                                {{-- Isi pesan: text-break memecah kata yang terlalu panjang --}}
                                <div class="small text-break">{{ $message->body }}</div>

                                {{-- Pengirim dan waktu pesan --}}
                                <div class="text-white-50 text-end" style="font-size: .65rem;">
                                    {{ $mine ? 'Kamu' : 'Ahli' }} &middot; {{ $message->created_at->locale('id')->translatedFormat('j M, H:i') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Pesan jika riwayat chat masih kosong (dihapus JavaScript saat pesan pertama muncul) --}}
                        <p class="pf-xs text-white-50 m-0" data-empty>Belum ada pesan. Tulis pertanyaanmu seputar parfum di bawah ini.</p>
                    @endforelse
                </div>

                {{-- Form kirim pesan --}}
                <form id="chat-form" action="{{ route('chat.store') }}" method="POST" novalidate>
                    {{-- Token CSRF wajib untuk form POST --}}
                    @csrf
                    <label for="body" class="visually-hidden">Pesan</label>
                    <textarea id="body" name="body" rows="3" maxlength="1000" placeholder="Tulis pesan..."
                              class="form-control pf-xs @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>

                    {{-- Pesan error validasi (dari server, atau dari JavaScript saat kirim gagal) --}}
                    <div id="chat-error" class="pf-error" @unless ($errors->has('body')) hidden @endunless>{{ $errors->first('body') }}</div>

                    <button type="submit" class="btn btn-pf-red btn-sm rounded-pill px-4 mt-3 fw-semibold">Kirim</button>
                </form>
            </div>

            {{-- Tombol kembali ke dashboard --}}
            <a href="{{ route('perfumes.index') }}" class="btn btn-pf-slate btn-sm rounded-pill pf-xs fw-semibold mt-3">&larr; Kembali ke Dashboard</a>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/live-chat.js') }}?v=1"></script>
    <script>
        (function () {
            var box = document.getElementById('chat-box');
            var form = document.getElementById('chat-form');
            var errorBox = document.getElementById('chat-error');
            if (!box || !form || !window.LiveChat) { return; }

            // Gulir otomatis ke pesan terbaru.
            box.scrollTop = box.scrollHeight;

            // Membuat satu baris pesan dengan tampilan yang sama seperti yang dibuat Blade di atas.
            function render(m) {
                var mine = m.sender === 'user';

                var row = document.createElement('div');
                row.className = 'd-flex flex-shrink-0 ' + (mine ? 'justify-content-end' : 'justify-content-start');

                var bubble = document.createElement('div');
                bubble.className = 'pf-bubble rounded-3 px-3 py-2 ' + (mine ? 'bg-pf-red' : 'bg-secondary');
                bubble.style.cssText = 'width: fit-content; max-width: 75%; min-width: 0; height: auto; min-height: 0;';

                var text = document.createElement('div');
                text.className = 'small text-break';
                text.textContent = m.body;

                var meta = document.createElement('div');
                meta.className = 'text-white-50 text-end';
                meta.style.fontSize = '.65rem';
                meta.textContent = (mine ? 'Kamu' : 'Ahli') + ' · ' + m.time;

                bubble.appendChild(text);
                bubble.appendChild(meta);
                row.appendChild(bubble);
                return row;
            }

            function showError(text) {
                errorBox.textContent = text;
                errorBox.hidden = !text;
            }

            // id pesan terakhir yang sudah tampil (0 kalau belum ada pesan).
            var lastId = {{ (int) ($messages->last()->id ?? 0) }};

            var live = LiveChat.watchMessages({
                box: box,
                pollUrl: @json(route('chat.poll')),
                lastId: lastId,
                render: render
            });

            // Pesan yang baru dikirim langsung tampil tanpa menunggu polling berikutnya.
            LiveChat.bindForm(form, {
                onMessage: function (m) { live.add([m]); box.scrollTop = box.scrollHeight; },
                onError: showError
            });
        })();
    </script>
@endpush
