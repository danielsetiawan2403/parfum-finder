<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Jumlah percakapan yang pesan terakhirnya dari user (belum dibalas admin).
    public static function pendingCount(): int
    {
        $latestIds = ChatMessage::query()
            ->selectRaw('MAX(id)')
            ->groupBy('user_id');

        return ChatMessage::query()
            ->whereIn('id', $latestIds)
            ->where('sender', 'user')
            ->count();
    }

    // Satu kartu per user: percakapan yang perlu dibalas tampil paling atas.
    public function index()
    {
        $users = User::query()
            ->whereHas('chatMessages')
            ->with(['chatMessages' => fn ($q) => $q->orderBy('id')])
            ->get()
            ->sort(function ($a, $b) {
                $lastA = $a->chatMessages->last();
                $lastB = $b->chatMessages->last();

                $pendingA = $lastA->sender === 'user';
                $pendingB = $lastB->sender === 'user';

                if ($pendingA !== $pendingB) {
                    return $pendingA ? -1 : 1;
                }

                return $lastB->id <=> $lastA->id;
            })
            ->values();

        return view('admin.chats.index', compact('users'));
    }

    public function reply(Request $request, User $user): RedirectResponse
    {
        // Error disimpan per user, supaya hanya kartu yang bersangkutan menampilkannya.
        $data = $request->validateWithBag("reply-{$user->id}", [
            'body' => ['required', 'string', 'max:1000'],
        ], [
            'body.required' => 'Balasan tidak boleh kosong.',
            'body.max'      => 'Balasan maksimal 1000 karakter.',
        ]);

        $user->chatMessages()->create([
            'sender' => 'admin',
            'body'   => trim($data['body']),
        ]);

        return redirect(route('admin.chats.index') . '#chat-' . $user->id)
            ->with('success', "Balasan terkirim ke {$user->name}.");
    }
}
