<?php

namespace App\Http\Controllers;

use App\Models\Perfume;
use Illuminate\Http\Request;

class PerfumeController extends Controller
{
    // Beranda (landing page): tampilkan 3 parfum pertama.
    public function home()
    {
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
            ->when($kategori, fn ($query) => $query->where('category', $kategori))
            ->orderBy('id')
            ->get();

        $categories = Perfume::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('dashboard', compact('perfumes', 'categories', 'q', 'kategori'));
    }

    // Halaman detail satu parfum. Ulasan hanya dimuat untuk member premium.
    public function show(Request $request, Perfume $perfume)
    {
        $user      = $request->user();
        $canReview = $user?->isPremium() ?? false;

        $reviews = collect();
        $average = null;

        if ($canReview) {
            $reviews = $perfume->reviews()->with('user')->latest()->get();
            $average = $reviews->avg('rating');
        }

        return view('perfume-detail', compact('perfume', 'canReview', 'reviews', 'average'));
    }
}