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

    // Daftar user yang pernah chat (tanpa isi pesan). Yang perlu dibalas tampil paling atas.
    public function index()
    {
        $users = User::query()
            ->whereHas('chatMessages')
            ->with(['chatMessages' => fn ($q) => $q->orderBy('id')])
            ->get()
            ->each(function ($user) {
                $messages = $user->chatMessages;
                $lastAdminId = $messages->where('sender', 'admin')->max('id') ?? 0;

                // Pesan user yang belum dibalas = pesan user setelah balasan admin terakhir.
                $user->unread_count = $messages
                    ->where('sender', 'user')
                    ->where('id', '>', $lastAdminId)
                    ->count();
                $user->last_message_at = $messages->last()->created_at;
            })
            ->sort(function ($a, $b) {
                $pendingA = $a->unread_count > 0;
                $pendingB = $b->unread_count > 0;

                if ($pendingA !== $pendingB) {
                    return $pendingA ? -1 : 1;
                }

                return $b->last_message_at <=> $a->last_message_at;
            })
            ->values();

        return view('admin.chats.index', compact('users'));
    }

    // Room chat khusus satu user: di sinilah isi pesan ditampilkan.
    public function show(User $user)
    {
        $messages = $user->chatMessages()->orderBy('id')->get();

        abort_if($messages->isEmpty(), 404);

        return view('admin.chats.show', compact('user', 'messages'));
    }

    public function reply(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ], [
            'body.required' => 'Balasan tidak boleh kosong.',
            'body.max'      => 'Balasan maksimal 1000 karakter.',
        ]);

        $user->chatMessages()->create([
            'sender' => 'admin',
            'body'   => trim($data['body']),
        ]);

        return redirect()->route('admin.chats.show', $user)
            ->with('success', "Balasan terkirim ke {$user->name}.");
    }
}
