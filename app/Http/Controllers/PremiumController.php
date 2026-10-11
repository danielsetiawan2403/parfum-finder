<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PremiumController extends Controller
{
    // Halaman Premium: pilihan paket (belum premium) atau status aktif (sudah premium).
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isPremium()) {
            return view('premium.status', [
                'subscription' => $user->activeSubscription(),
            ]);
        }

        return view('premium.index', ['plans' => Subscription::PLANS]);
    }

    // Klik "Upgrade Sekarang": buat tagihan lalu ke halaman pembayaran.
    public function checkout(Request $request)
    {
        $user = $request->user();

        if ($user->isPremium()) {
            return redirect()->route('premium');
        }

        $data = $request->validate([
            'plan' => ['required', Rule::in(array_keys(Subscription::PLANS))],
        ], [
            'plan.required' => 'Pilih salah satu paket terlebih dahulu.',
            'plan.in'       => 'Paket yang dipilih tidak valid.',
        ]);

        $plan = Subscription::PLANS[$data['plan']];

        // Tagihan lama yang belum dibayar dibatalkan, supaya tidak menumpuk.
        $user->subscriptions()->where('status', 'pending')->update(['status' => 'cancelled']);

        $subscription = $user->subscriptions()->create([
            'invoice' => 'INV-' . strtoupper(Str::random(8)),
            'plan'    => $data['plan'],
            'label'   => 'Premium ' . $plan['label'],
            'months'  => $plan['months'],
            'price'   => $plan['price'],
            'status'  => 'pending',
        ]);

        return redirect()->route('premium.payment', $subscription);
    }

    // Halaman pembayaran (QRIS).
    public function payment(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === $request->user()->id, 404);

        if ($subscription->status !== 'pending') {
            return redirect()->route('premium');
        }

        return view('premium.payment', compact('subscription'));
    }

    // Klik "Konfirmasi Sudah Bayar": aktifkan premium.
    public function confirm(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === $request->user()->id, 404);

        if ($subscription->status === 'pending') {
            $start = now();

            $subscription->update([
                'status'    => 'paid',
                'paid_at'   => $start,
                'starts_at' => $start,
                'ends_at'   => $start->copy()->addMonths($subscription->months),
            ]);
        }

        return redirect()->route('premium')
            ->with('success', 'Pembayaran berhasil! Sekarang kamu member Premium.');
    }

    // Klik "Ganti Paket atau Batalkan".
    public function cancel(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === $request->user()->id, 404);

        if ($subscription->status === 'pending') {
            $subscription->update(['status' => 'cancelled']);
        }

        return redirect()->route('premium');
    }
}