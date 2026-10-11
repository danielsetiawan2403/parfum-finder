@extends('layouts.app')

@section('title', 'Premium Membership - Parfum Finder')

@section('content')
    <section class="px-3 py-5">
        <div class="container" style="max-width: 43rem;">

            @if (session('warning'))
                <div role="alert" class="alert alert-warning small">{{ session('warning') }}</div>
            @endif

            <div class="bg-white text-dark rounded-4 shadow p-4 p-md-5">
                <h1 class="fs-3 fw-bold text-center">👑 Premium Membership</h1>
                <p class="text-center text-secondary small">Buka akses penuh ke konsultasi ahli dan rekomendasi parfum terbaik.</p>

                <div class="d-flex flex-wrap justify-content-center gap-2 my-3">
                    <span class="badge rounded-pill text-bg-light border fw-normal py-2 px-3">✓ Konsultasi Ahli</span>
                    <span class="badge rounded-pill text-bg-light border fw-normal py-2 px-3">✓ Rekomendasi Eksklusif</span>
                    <span class="badge rounded-pill text-bg-light border fw-normal py-2 px-3">✓ Tanpa Iklan</span>
                </div>

                <form action="{{ route('premium.checkout') }}" method="POST" novalidate>
                    @csrf

                    @error('plan')
                        <div role="alert" class="alert alert-danger py-2 small">{{ $message }}</div>
                    @enderror

                    <div class="row g-3">
                        @foreach ($plans as $key => $plan)
                            <div class="col-12 col-md-4">
                                <label for="plan-{{ $key }}" class="pf-plan d-block h-100 text-center border rounded-3 p-3">
                                    <input type="radio" name="plan" id="plan-{{ $key }}" value="{{ $key }}"
                                           class="form-check-input d-block mx-auto mb-2" @checked(old('plan') === $key) required>
                                    <span class="d-block fw-bold">{{ $plan['label'] }}</span>
                                    <span class="d-block fw-bold text-danger small">Rp {{ number_format($plan['price'], 0, ',', '.') }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-pf-red w-100 fw-bold text-uppercase py-2 mt-4">Upgrade Sekarang</button>
                </form>
            </div>

            <p class="text-center mt-3 mb-0">
                <a href="{{ route('perfumes.index') }}" class="text-white-50 small text-decoration-none">&larr; Kembali ke Dashboard</a>
            </p>
        </div>
    </section>
@endsection