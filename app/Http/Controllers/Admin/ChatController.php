<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
    // Dipanggil juga oleh JavaScript tiap beberapa detik (AJAX), jadi hanya bagian daftarnya yang dikirim.
    public function index(Request $request)
    {
        $users = $this->conversations();

        if ($request->ajax()) {
            return view('admin.chats._list', compact('users'));
        }

        return view('admin.chats.index', compact('users'));
    }

    // Room chat khusus satu user: di sinilah isi pesan ditampilkan.
    public function show(User $user)
    {
        $messages = $user->chatMessages()->orderBy('id')->get();

        abort_if($messages->isEmpty(), 404);

        return view('admin.chats.show', compact('user', 'messages'));
    }

    // Dipanggil JavaScript tiap beberapa detik: kirim pesan yang id-nya lebih besar dari ?after=.
    public function poll(Request $request, User $user): JsonResponse
    {
        $after = max(0, (int) $request->query('after', 0));

        $messages = $user->chatMessages()
            ->where('id', '>', $after)
            ->orderBy('id')
            ->limit(100)
            ->get();

        return response()->json([
            'messages' => $messages->map(fn (ChatMessage $m) => $this->format($m))->values(),
        ]);
    }

    // Balas user. Dari JavaScript (AJAX) jawabannya JSON, dari form biasa tetap redirect.
    public function reply(Request $request, User $user)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ], [
            'body.required' => 'Balasan tidak boleh kosong.',
            'body.max'      => 'Balasan maksimal 1000 karakter.',
        ]);

        $message = $user->chatMessages()->create([
            'sender' => 'admin',
            'body'   => trim($data['body']),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => $this->format($message)], 201);
        }

        return redirect()->route('admin.chats.show', $user)
            ->with('success', "Balasan terkirim ke {$user->name}.");
    }

    // Satu baris per user yang pernah chat, sudah terurut: yang perlu dibalas paling atas.
    // Memakai dua query ringkas (bukan memuat semua pesan) karena daftar ini diambil berulang oleh JavaScript.
    private function conversations()
    {
        // Pesan terakhir tiap user.
        $last = ChatMessage::query()
            ->selectRaw('user_id, MAX(created_at) AS last_at')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // Jumlah pesan user yang belum dibalas = pesan user setelah balasan admin terakhir.
        $unread = ChatMessage::query()
            ->where('sender', 'user')
            ->whereRaw("id > COALESCE((SELECT MAX(m.id) FROM chat_messages m WHERE m.user_id = chat_messages.user_id AND m.sender = 'admin'), 0)")
            ->selectRaw('user_id, COUNT(*) AS total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return User::query()
            ->whereIn('id', $last->keys())
            ->get()
            ->each(function (User $user) use ($last, $unread) {
                $user->unread_count    = (int) ($unread[$user->id] ?? 0);
                $user->last_message_at = Carbon::parse($last[$user->id]->last_at);
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
}
