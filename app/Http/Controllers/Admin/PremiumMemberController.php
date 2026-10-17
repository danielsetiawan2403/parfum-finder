<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

    // Laporan pendapatan langganan: halaman siap cetak (Ctrl+P atau tombol Cetak, bisa disimpan sebagai PDF).
    // Periode difilter berdasarkan tanggal pembayaran. Transaksi dari akun yang sudah dihapus tetap dihitung.
    public function report(Request $request)
    {
        $from = $this->parseDate($request->query('dari'));
        $to   = $this->parseDate($request->query('sampai'));

        // Kalau terbalik, tukar saja.
        if ($from && $to && $to->lt($from)) {
            [$from, $to] = [$to, $from];
        }

        $paid = Subscription::query()
            ->with('user')
            ->where('status', 'paid')
            ->when($from, fn ($q) => $q->where('paid_at', '>=', $from->copy()->startOfDay()))
            ->when($to, fn ($q) => $q->where('paid_at', '<=', $to->copy()->endOfDay()))
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get();

        // Data per transaksi, sudah dirapikan supaya tampilan tinggal menampilkan.
        $transactions = $paid->map(function (Subscription $s) {
            return (object) [
                'user_id'   => $s->user_id,
                'paid_at'   => $s->paid_at ?? $s->starts_at ?? $s->created_at,
                'invoice'   => $s->invoice,
                'name'      => $s->user?->name ?? '(akun tidak ditemukan)',
                'username'  => $s->user?->username,
                'deleted'   => (bool) $s->user?->trashed(),
                'label'     => $s->label,
                'months'    => (int) $s->months,
                'price'     => (int) $s->price,
                'starts_at' => $s->starts_at,
                'ends_at'   => $s->ends_at,
                'active'    => $s->ends_at && $s->ends_at->isFuture(),
            ];
        });

        $revenue = (int) $transactions->sum('price');
        $count   = $transactions->count();

        $summary = [
            'revenue'   => $revenue,
            'count'     => $count,
            'members'   => $transactions->pluck('user_id')->unique()->count(),
            'average'   => $count > 0 ? (int) round($revenue / $count) : 0,
            'activeNow' => self::activeCount(), // kondisi saat laporan dicetak, bukan per periode
        ];

        // Pendapatan per paket (urut dari paket terpendek).
        $byPlan = $transactions
            ->groupBy('label')
            ->map(fn ($group, $label) => (object) [
                'label'   => $label,
                'months'  => $group->first()->months,
                'count'   => $group->count(),
                'revenue' => (int) $group->sum('price'),
            ])
            ->sortBy('months')
            ->values();

        // Pendapatan per bulan (urut kronologis, karena data sudah diurutkan menurut tanggal bayar).
        $byMonth = $transactions
            ->groupBy(fn ($t) => $t->paid_at->format('Y-m'))
            ->map(fn ($group, $ym) => (object) [
                'label'   => Carbon::createFromFormat('Y-m-d', $ym . '-01')->locale('id')->translatedFormat('F Y'),
                'count'   => $group->count(),
                'revenue' => (int) $group->sum('price'),
            ])
            ->values();

        $fmt = fn (Carbon $d) => $d->copy()->locale('id')->translatedFormat('j F Y');

        $periodLabel = match (true) {
            $from && $to => $fmt($from) . ' sampai ' . $fmt($to),
            (bool) $from => 'Mulai ' . $fmt($from),
            (bool) $to   => 'Sampai ' . $fmt($to),
            default      => 'Semua periode',
        };

        // Pintasan periode untuk tombol cepat di atas laporan.
        $now     = now();
        $lastMon = $now->copy()->subMonthNoOverflow();
        $presets = [
            'Semua periode' => [],
            'Bulan ini'     => [
                'dari'   => $now->copy()->startOfMonth()->toDateString(),
                'sampai' => $now->copy()->endOfMonth()->toDateString(),
            ],
            'Bulan lalu'    => [
                'dari'   => $lastMon->copy()->startOfMonth()->toDateString(),
                'sampai' => $lastMon->copy()->endOfMonth()->toDateString(),
            ],
            'Tahun ini'     => [
                'dari'   => $now->copy()->startOfYear()->toDateString(),
                'sampai' => $now->copy()->endOfYear()->toDateString(),
            ],
        ];

        return view('admin.premium.report', [
            'transactions' => $transactions,
            'summary'      => $summary,
            'byPlan'       => $byPlan,
            'byMonth'      => $byMonth,
            'periodLabel'  => $periodLabel,
            'presets'      => $presets,
            'from'         => $from,
            'to'           => $to,
            'printedAt'    => $now,
            'printedBy'    => $request->user()->name,
        ]);
    }

    // Jumlah user dengan langganan aktif (dipakai juga di dashboard).
    // Akun yang sudah dihapus tidak dihitung sebagai member aktif.
    public static function activeCount(): int
    {
        return Subscription::where('status', 'paid')
            ->where('ends_at', '>', now())
            ->whereIn('user_id', User::query()->select('id'))
            ->distinct('user_id')
            ->count('user_id');
    }

    // Terima tanggal format Y-m-d yang valid; selain itu dianggap kosong.
    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }

        // Tolak tanggal yang tidak ada di kalender (misalnya 2026-02-31).
        if (! $date || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $date->startOfDay();
    }
}
