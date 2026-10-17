<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountDeletedMail;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Daftar customer (akun admin tidak ditampilkan) + pencarian.
    // Akun yang sudah dihapus tidak muncul (soft delete otomatis menyembunyikannya).
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

    // Hapus akun customer: wajib menyertakan alasan, lalu alasan itu dikirim ke email user.
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if(
            $user->role === 'admin' || $user->is($request->user()),
            403,
            'Akun admin tidak bisa dihapus dari halaman ini.'
        );

        $validator = Validator::make($request->all(), [
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'reason.required' => 'Alasan penghapusan wajib diisi.',
            'reason.min'      => 'Alasan penghapusan minimal 5 karakter.',
            'reason.max'      => 'Alasan penghapusan maksimal 500 karakter.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $reason = trim($validator->validated()['reason']);
        $name   = $user->name;
        $email  = $user->email;

        // Ulasan dan riwayat chat dihapus. Akunnya sendiri hanya ditandai terhapus (soft delete),
        // jadi riwayat pembayaran tetap utuh dan total pendapatan tidak berubah.
        DB::transaction(function () use ($user, $reason) {
            Review::where('user_id', $user->id)->delete();
            $user->chatMessages()->delete();

            $user->deletion_reason = $reason;
            $user->save();
            $user->delete();
        });

        // Email dikirim SETELAH akun terhapus. Kalau pengiriman gagal, penghapusan tidak dibatalkan,
        // tetapi admin diberi tahu.
        $mailer = config('mail.default');
        $sent   = true;

        try {
            Mail::to($email)->send(new AccountDeletedMail($name, $reason));
        } catch (\Throwable $e) {
            report($e);
            $sent = false;
        }

        if (! $sent) {
            return redirect()->route('admin.users.index')->with(
                'warning',
                "Akun \"{$name}\" sudah dihapus, tetapi email ke {$email} gagal dikirim. Periksa pengaturan MAIL_* di file .env."
            );
        }

        // Mode "log" hanya menulis email ke storage/logs/laravel.log, belum benar-benar terkirim.
        if (in_array($mailer, ['log', 'array'], true)) {
            return redirect()->route('admin.users.index')->with(
                'warning',
                "Akun \"{$name}\" sudah dihapus. Emailnya belum terkirim sungguhan karena MAIL_MAILER masih \"{$mailer}\" (isinya hanya dicatat di storage/logs/laravel.log)."
            );
        }

        return redirect()->route('admin.users.index')->with(
            'success',
            "Akun \"{$name}\" dihapus dan email alasan penghapusan dikirim ke {$email}."
        );
    }
}
