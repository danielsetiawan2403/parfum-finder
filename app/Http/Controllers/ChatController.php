<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
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

        // User membuka chat = semua balasan admin dianggap sudah dibaca (titik merah hilang)
        $request->user()
            ->chatMessages()
            ->where('sender', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('chat.index', compact('messages'));
    }

    // Dipanggil JavaScript tiap beberapa detik: kirim pesan yang id-nya lebih besar dari ?after=.
    // Hanya jalan saat tab chat terlihat, jadi balasan yang ikut terkirim ke sini otomatis dianggap sudah dibaca.
    public function poll(Request $request): JsonResponse
    {
        if ($this->guardAccess($request)) {
            return response()->json(['messages' => []], 403);
        }

        $after = max(0, (int) $request->query('after', 0));

        $messages = $request->user()
            ->chatMessages()
            ->where('id', '>', $after)
            ->orderBy('id')
            ->limit(100)
            ->get();

        $adminIds = $messages->where('sender', 'admin')->pluck('id');

        if ($adminIds->isNotEmpty()) {
            $request->user()
                ->chatMessages()
                ->whereIn('id', $adminIds)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'messages' => $messages->map(fn (ChatMessage $m) => $this->format($m))->values(),
        ]);
    }

    // Kirim pesan. Dari JavaScript (AJAX) jawabannya JSON, dari form biasa tetap redirect.
    public function store(Request $request)
    {
        // Tolak admin dan user non-premium
        if ($redirect = $this->guardAccess($request)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Akses ditolak.'], 403)
                : $redirect;
        }

        // Validasi isi pesan
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ], [
            'body.required' => 'Pesan tidak boleh kosong.',
            'body.max'      => 'Pesan maksimal 1000 karakter.',
        ]);

        // Simpan pesan sebagai pesan dari user
        $message = $request->user()->chatMessages()->create([
            'sender' => 'user',
            'body'   => trim($data['body']),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => $this->format($message)], 201);
        }

        return redirect()->route('chat.index');
    }

    // Bentuk pesan yang dikirim ke JavaScript.
    private function format(ChatMessage $message): array
    {
        return [
            'id'     => $message->id,
            'sender' => $message->sender,
            'body'   => $message->body,
            'time'   => $message->created_at->locale('id')->translatedFormat('j M, H:i'),
        ];
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
