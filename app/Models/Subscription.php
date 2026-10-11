<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    /**
     * Daftar paket Premium. Ubah harga atau durasi di sini.
     */
    public const PLANS = [
        '1-bulan' => ['label' => '1 Bulan', 'months' => 1,  'price' => 29000],
        '6-bulan' => ['label' => '6 Bulan', 'months' => 6,  'price' => 129000],
        '1-tahun' => ['label' => '1 Tahun', 'months' => 12, 'price' => 199000],
    ];

    protected $fillable = [
        'user_id',
        'invoice',
        'plan',
        'label',
        'months',
        'price',
        'status',
        'paid_at',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'paid_at'   => 'datetime',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    // URL pembayaran memakai nomor invoice, bukan id.
    public function getRouteKeyName(): string
    {
        return 'invoice';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}