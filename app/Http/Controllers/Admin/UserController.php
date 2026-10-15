<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Daftar customer (akun admin tidak ditampilkan) + pencarian.
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->where('role', '!=', 'admin')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('username', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            // Langganan aktif dimuat sekalian supaya status premium tidak memicu query per baris.
            ->with(['subscriptions' => fn ($s) => $s
                ->where('status', 'paid')
                ->where('ends_at', '>', now())
                ->orderByDesc('ends_at')])
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if(
            $user->role === 'admin' || $user->is($request->user()),
            403,
            'Akun admin tidak bisa dihapus dari halaman ini.'
        );

        $name = $user->name;

        $user->delete(); // langganan, ulasan, dan riwayat chat ikut terhapus (cascade)

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$name}\" dihapus beserta ulasan dan riwayat chat-nya.");
    }
}
