@extends('layouts.app')

@section('title', 'Chat Konsultasi - Parfum Finder')

@section('content')
    <section class="px-3 py-4">
        <div class="container" style="max-width: 40rem;">

            <div class="bg-pf-card rounded-3 p-3 p-md-4">
                <h1 class="fs-6 fw-bold mb-3">💬 Chat Konsultasi Premium</h1>

                {{-- Riwayat pesan --}}
                <div id="chat-box" class="pf-chat-box d-flex flex-column gap-2 mb-3">
                    @forelse ($messages as $message)
                        @php $mine = $message->sender === 'user'; @endphp
                        <div class="d-flex {{ $mine ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="pf-bubble rounded-3 px-3 py-2 {{ $mine ? 'bg-pf-red' : 'bg-secondary' }}">
                                <div class="small">{{ $message->body }}</div>
                                <div class="text-white-50 text-end" style="font-size: .65rem;">
                                    {{ $mine ? 'Kamu' : 'Ahli' }} &middot; {{ $message->created_at->locale('id')->translatedFormat('j M, H:i') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="pf-xs text-white-50 m-0">Belum ada pesan. Tulis pertanyaanmu seputar parfum di bawah ini.</p>
                    @endforelse
                </div>

                {{-- Form kirim pesan --}}
                <form action="{{ route('chat.store') }}" method="POST" novalidate>
                    @csrf
                    <label for="body" class="visually-hidden">Pesan</label>
                    <textarea id="body" name="body" rows="3" maxlength="1000" placeholder="Tulis pesan..."
                              class="form-control pf-xs @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                    @error('body')
                        <div class="pf-error">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-pf-red btn-sm rounded-pill px-4 mt-3 fw-semibold">Kirim</button>
                </form>
            </div>

            <a href="{{ route('perfumes.index') }}" class="btn btn-pf-slate btn-sm rounded-pill pf-xs fw-semibold mt-3">&larr; Kembali ke Dashboard</a>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Gulir otomatis ke pesan terbaru.
        var box = document.getElementById('chat-box');
        if (box) { box.scrollTop = box.scrollHeight; }
    </script>
@endpush