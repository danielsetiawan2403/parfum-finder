<?php

namespace App\Models;

use App\Models\Concerns\HasPremium;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // HasPremium berisi logika premium (method isPremium())
    // SoftDeletes: akun yang dihapus admin hanya ditandai (kolom deleted_at), tidak benar-benar dihapus,
    // jadi riwayat pembayarannya tetap ada untuk laporan dan user tidak bisa login lagi.
    use HasFactory, Notifiable, HasPremium, SoftDeletes;

    /**
     * Cek apakah user ini adalah admin.
     * Dipakai di PerfumeController@show dan ReviewController@store.
     * SESUAIKAN: kalau nilai role admin kamu bukan 'admin', ganti di sini.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}