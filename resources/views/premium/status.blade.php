@extends('layouts.app')

@section('title', 'Premium Membership - Parfum Finder')

@section('content')
    <section class="px-3 py-5">
        <div class="container" style="max-width: 32rem;">

            @if (session('success'))
                <div role="alert" class="alert alert-success small">{{ session('success') }}</div>
            @endif

            <div class="bg-white text-dark rounded-4 shadow p-4">
                <h1 class="fs-3 fw-bold text-center">👑 Premium Membership</h1>
                <p class="text-center text-secondary small">Nikmati semua fitur eksklusif kamu.</p>

                <div class="text-center rounded-3 p-3 border" style="background: #fff3f3; border-color: #f5c2c7 !important;">
                    <div class="fw-bold text-danger">Status: Aktif</div>

                    @if ($subscription)
                        <div class="small">Berlaku hingga:</div>
                        <div class="fw-bold">{{ $subscription->ends_at->locale('id')->translatedFormat('j M Y') }}</div>
                        <div class="text-secondary mt-1" style="font-size: .7rem;">{{ $subscription->label }} &middot; {{ $subscription->invoice }}</div>
                    @else
                        <div class="small">Akses tanpa batas waktu (akun admin).</div>
                    @endif
                </div>

                <div class="d-grid mt-3">
                    <a href="{{ route('chat.index') }}" class="btn btn-pf-red rounded-pill fw-semibold">💬 Buka Chat Konsultasi</a>
                </div>
            </div>

            <p class="text-center mt-3 mb-0">
                <a href="{{ route('perfumes.index') }}" class="text-white-50 small text-decoration-none">&larr; Kembali ke Dashboard</a>
            </p>
        </div>
    </section>
@endsection