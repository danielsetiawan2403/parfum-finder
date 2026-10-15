<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perfume extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'image',
        'top_notes',
        'middle_notes',
        'base_notes',
        'description',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}