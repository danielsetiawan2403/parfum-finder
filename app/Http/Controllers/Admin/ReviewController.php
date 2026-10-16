<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perfume;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Pilihan urutan yang boleh dipakai di dropdown "Urutkan".
    private const SORTS = ['terbaru', 'terlama', 'rating-terendah', 'rating-tertinggi'];

    // Daftar ulasan + filter (cari, parfum, rating) + urutan.
    // Tiga kartu ringkasan dihitung dari semua hasil filter, bukan hanya dari halaman yang tampil.
    public function index(Request $request)
    {
        $q         = trim((string) $request->query('q', ''));
        $perfumeId = (int) $request->query('parfum', 0);
        $rating    = (int) $request->query('rating', 0);
        $sort      = (string) $request->query('urut', 'terbaru');

        if ($rating < 1 || $rating > 5) {
            $rating = 0;
        }
        if (! in_array($sort, self::SORTS, true)) {
            $sort = 'terbaru';
        }

        $base = Review::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('body', 'like', "%{$q}%")
                      ->orWhereHas('user', fn ($u) => $u
                          ->where('name', 'like', "%{$q}%")
                          ->orWhere('username', 'like', "%{$q}%"));
                });
            })
            ->when($perfumeId > 0, fn ($query) => $query->where('perfume_id', $perfumeId))
            ->when($rating > 0, fn ($query) => $query->where('rating', $rating));

        // Ringkasan dihitung sebelum urutan ditambahkan.
        $summary = (clone $base)->toBase()->selectRaw(
            'COUNT(*) AS total, AVG(rating) AS average, COALESCE(SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END), 0) AS low'
        )->first();

        $list = (clone $base)->with(['user:id,name,username', 'perfume:id,name']);

        match ($sort) {
            'terlama'          => $list->orderBy('created_at')->orderBy('id'),
            'rating-terendah'  => $list->orderBy('rating')->orderByDesc('id'),
            'rating-tertinggi' => $list->orderByDesc('rating')->orderByDesc('id'),
            default            => $list->orderByDesc('created_at')->orderByDesc('id'),
        };

        $reviews = $list->paginate(10)->withQueryString();

        // Setelah menghapus ulasan terakhir di sebuah halaman, pindah ke halaman terakhir yang masih ada.
        if ($reviews->isEmpty() && $reviews->currentPage() > 1) {
            return redirect()->route('admin.reviews.index', array_merge(
                $request->query(),
                ['page' => $reviews->lastPage()]
            ));
        }

        $perfumes = Perfume::orderBy('name')->get(['id', 'name']);

        $total      = (int) $summary->total;
        $average    = $total > 0 ? (float) $summary->average : null;
        $lowCount   = (int) $summary->low;
        $isFiltered = $q !== '' || $perfumeId > 0 || $rating > 0;
        $isCustom   = $isFiltered || $sort !== 'terbaru';

        return view('admin.reviews.index', compact(
            'reviews', 'perfumes', 'q', 'perfumeId', 'rating', 'sort',
            'total', 'average', 'lowCount', 'isFiltered', 'isCustom'
        ));
    }

    // Hapus satu ulasan. Akun user tidak ikut terhapus.
    public function destroy(Review $review): RedirectResponse
    {
        $review->loadMissing(['user:id,name', 'perfume:id,name']);

        $who     = $review->user->name ?? 'user';
        $perfume = $review->perfume->name ?? 'parfum';

        $review->delete();

        // back() mempertahankan filter dan halaman yang sedang dibuka.
        return back()->with('success', "Ulasan dari {$who} untuk {$perfume} dihapus.");
    }
}
