<?php

namespace App\Http\Controllers;

use App\Models\Perfume;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Simpan ulasan baru, lalu kembali ke halaman detail dengan form yang sudah kosong.
    public function store(Request $request, Perfume $perfume)
    {
        $user = $request->user();

        // Admin hanya boleh melihat ulasan, tidak boleh menulis.
        // Dicek lebih dulu karena admin bisa saja juga lolos cek premium.
        if ($user->isAdmin()) {
            return redirect()->route('perfumes.show', $perfume)
                ->with('warning', 'Admin hanya dapat melihat ulasan.');
        }

        // User non-premium diarahkan ke halaman upgrade
        if (! $user->isPremium()) {
            return redirect()->route('premium')
                ->with('warning', 'Ulasan hanya untuk member Premium.');
        }

        // Validasi input rating dan isi ulasan
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'body'   => ['required', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Pilih rating 1 sampai 5 bintang.',
            'rating.integer'  => 'Rating tidak valid.',
            'rating.between'  => 'Rating harus antara 1 sampai 5 bintang.',
            'body.required'   => 'Ulasan tidak boleh kosong.',
            'body.max'        => 'Ulasan maksimal 1000 karakter.',
        ]);

        // Simpan ulasan milik user yang sedang login
        Review::create([
            'perfume_id' => $perfume->id,
            'user_id'    => $user->id,
            'rating'     => $data['rating'],
            'body'       => trim($data['body']),
        ]);

        return redirect()->route('perfumes.show', $perfume)
            ->with('success', 'Ulasanmu tersimpan. Terima kasih!');
    }
}