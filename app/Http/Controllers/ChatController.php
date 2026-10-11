<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Halaman chat konsultasi (khusus premium).
    public function index(Request $request)
    {
        if ($redirect = $this->guardPremium($request)) {
            return $redirect;
        }

        $messages = $request->user()
            ->chatMessages()
            ->orderBy('id')
            ->get();

        return view('chat.index', compact('messages'));
    }

    // Kirim pesan.
    public function store(Request $request)
    {
        if ($redirect = $this->guardPremium($request)) {
            return $redirect;
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ], [
            'body.required' => 'Pesan tidak boleh kosong.',
            'body.max'      => 'Pesan maksimal 1000 karakter.',
        ]);

        $request->user()->chatMessages()->create([
            'sender' => 'user',
            'body'   => trim($data['body']),
        ]);

        return redirect()->route('chat.index');
    }

    // Non-premium diarahkan ke halaman upgrade.
    private function guardPremium(Request $request): ?RedirectResponse
    {
        if (! $request->user()->isPremium()) {
            return redirect()->route('premium')
                ->with('warning', 'Chat konsultasi hanya untuk member Premium.');
        }

        return null;
    }
}