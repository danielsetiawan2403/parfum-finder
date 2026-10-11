@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran - Parfum Finder')

@section('content')
    <section class="px-3 py-5">
        <div class="container" style="max-width: 24rem;">

            <div class="bg-white text-dark rounded-4 shadow overflow-hidden">
                <div class="bg-light text-center border-bottom py-3 px-3">
                    <h1 class="fs-6 fw-bold m-0">Konfirmasi Pembayaran</h1>
                    <div class="text-secondary" style="font-size: .7rem;">{{ $subscription->invoice }}</div>
                </div>

                <div class="p-4">
                    <div class="text-center">
                        <div class="small text-secondary">Total Tagihan:</div>
                        <div class="fs-2 fw-bold text-danger">Rp {{ number_format($subscription->price, 0, ',', '.') }}</div>
                    </div>

                    <div class="bg-light border rounded-3 py-2 px-3 small my-3">
                        <div class="d-flex justify-content-between"><span>Produk:</span><strong>{{ $subscription->label }}</strong></div>
                        <div class="d-flex justify-content-between"><span>Metode:</span><strong>QRIS Dinamis</strong></div>
                    </div>

                    <div class="border rounded-3 p-3 text-center">
                        <div id="qrcode" class="d-flex justify-content-center mb-2"></div>
                        <div class="fw-bold small">QRIS</div>
                        <div class="text-secondary" style="font-size: .7rem;">
                            Silakan scan QR di atas menggunakan aplikasi m-Banking atau E-Wallet pilihan Anda.
                        </div>
                    </div>

                    <form action="{{ route('premium.confirm', $subscription) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 fw-bold text-uppercase">Konfirmasi Sudah Bayar</button>
                    </form>

                    <form action="{{ route('premium.cancel', $subscription) }}" method="POST" class="text-center mt-2">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-secondary text-decoration-none">&larr; Ganti Paket atau Batalkan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- QR dibuat di browser dari data invoice (simulasi, bukan QRIS asli). --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        (function () {
            var target = document.getElementById('qrcode');
            if (target && window.QRCode) {
                new QRCode(target, {
                    text: @json('PARFUMFINDER|' . $subscription->invoice . '|' . $subscription->price),
                    width: 160,
                    height: 160,
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        })();
    </script>
@endpush