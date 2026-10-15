<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Halaman chat konsultasi (khusus user premium, bukan admin).
    public function index(Request $request)
    {
        // Tolak admin dan user non-premium
        if ($redirect = $this->guardAccess($request)) {
            return $redirect;
        }

        // Ambil riwayat chat milik user yang sedang login, urut dari yang terlama
        $messages = $request->user()
            ->chatMessages()
            ->orderBy('id')
            ->get();

        return view('chat.index', compact('messages'));
    }

    // Kirim pesan.
    public function store(Request $request)
    {
        // Tolak admin dan user non-premium
        if ($redirect = $this->guardAccess($request)) {
            return $redirect;
        }

        // Validasi isi pesan
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ], [
            'body.required' => 'Pesan tidak boleh kosong.',
            'body.max'      => 'Pesan maksimal 1000 karakter.',
        ]);

        // Simpan pesan sebagai pesan dari user
        $request->user()->chatMessages()->create([
            'sender' => 'user',
            'body'   => trim($data['body']),
        ]);

        return redirect()->route('chat.index');
    }

    // Penjaga akses chat:
    // - Admin dialihkan ke daftar parfum (admin punya panel sendiri, tidak memakai chat user).
    // - User non-premium dialihkan ke halaman upgrade.
    // - Selain itu (user premium) lolos dan mengembalikan null.
    private function guardAccess(Request $request): ?RedirectResponse
    {
        $user = $request->user();

        // Dicek lebih dulu, karena isPremium() mengembalikan true untuk admin
        if ($user->isAdmin()) {
            return redirect()->route('perfumes.index');
        }

        if (! $user->isPremium()) {
            return redirect()->route('premium')
                ->with('warning', 'Chat konsultasi hanya untuk member Premium.');
        }

        return null;
    }
}