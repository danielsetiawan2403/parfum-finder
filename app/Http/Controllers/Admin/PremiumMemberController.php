<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class PremiumMemberController extends Controller
{
    // Riwayat langganan Premium (hanya yang sudah dibayar) + pencarian dan filter status.
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status', '');

        $subscriptions = Subscription::query()
            ->with('user')
            ->where('status', 'paid')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('invoice', 'like', "%{$q}%")
                      ->orWhereHas('user', fn ($u) => $u
                          ->where('name', 'like', "%{$q}%")
                          ->orWhere('username', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%"));
                });
            })
            ->when($status === 'aktif', fn ($query) => $query->where('ends_at', '>', now()))
            ->when($status === 'berakhir', fn ($query) => $query->where('ends_at', '<=', now()))
            ->latest('starts_at')
            ->paginate(10)
            ->withQueryString();

        $totalPendapatan = Subscription::where('status', 'paid')->sum('price');
        $totalAktif      = self::activeCount();

        return view('admin.premium.index', compact('subscriptions', 'q', 'status', 'totalPendapatan', 'totalAktif'));
    }

    // Jumlah user dengan langganan aktif (dipakai juga di dashboard).
    public static function activeCount(): int
    {
        return Subscription::where('status', 'paid')
            ->where('ends_at', '>', now())
            ->distinct('user_id')
            ->count('user_id');
    }
}
