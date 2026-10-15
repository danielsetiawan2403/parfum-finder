<?php

namespace App\Http\Controllers;

use App\Models\Perfume;
use Illuminate\Http\Request;

class PerfumeController extends Controller
{
    // Beranda (landing page): tampilkan 3 parfum pertama.
    public function home()
    {
        // Ambil 3 parfum dengan id terkecil
        $perfumes = Perfume::orderBy('id')->take(3)->get();

        return view('landing', compact('perfumes'));
    }

    // Halaman Explore (dashboard user): semua parfum + pencarian + filter kategori.
    public function index(Request $request)
    {
        // Hanya untuk user yang sudah login. Tamu diarahkan ke form login,
        // dan setelah login otomatis kembali ke halaman ini.
        if (! $request->user()) {
            return redirect()->guest(route('login'))
                ->with('warning', 'Silakan login terlebih dahulu untuk melihat parfum lainnya.');
        }

        // Ambil kata kunci pencarian dan kategori dari query string
        $q        = trim((string) $request->query('q', ''));
        $kategori = $request->query('kategori');

        $perfumes = Perfume::query()
            ->when($q !== '', function ($query) use ($q) {
                // Cari di nama parfum dan di notes aroma.
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('top_notes', 'like', "%{$q}%")
                      ->orWhere('middle_notes', 'like', "%{$q}%")
                      ->orWhere('base_notes', 'like', "%{$q}%");
                });
            })
            // Filter kategori hanya jika dipilih
            ->when($kategori, fn ($query) => $query->where('category', $kategori))
            ->orderBy('id')
            ->get();

        // Daftar kategori unik untuk dropdown filter
        $categories = Perfume::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('dashboard', compact('perfumes', 'categories', 'q', 'kategori'));
    }

    // Halaman detail satu parfum.
    // - Admin & user premium boleh MELIHAT ulasan.
    // - Hanya user premium (yang bukan admin) boleh MENULIS ulasan.
    public function show(Request $request, Perfume $perfume)
    {
        $user = $request->user();

        // Cek peran user (tamu = null, jadi hasilnya false)
        $isAdmin   = $user?->isAdmin() ?? false;
        $isPremium = $user?->isPremium() ?? false;

        // Boleh melihat daftar ulasan: admin atau premium
        $canViewReviews = $isAdmin || $isPremium;

        // Boleh menulis ulasan: premium, tapi bukan admin
        $canWriteReview = $isPremium && ! $isAdmin;

        // Ulasan hanya dimuat jika user berhak melihatnya
        $reviews = collect();
        $average = null;

        if ($canViewReviews) {
            $reviews = $perfume->reviews()->with('user')->latest()->get();
            $average = $reviews->avg('rating');
        }

        return view('perfume-detail', compact(
            'perfume', 'canViewReviews', 'canWriteReview', 'reviews', 'average'
        ));
    }
}