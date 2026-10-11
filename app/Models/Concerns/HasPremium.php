<?php

namespace App\Models\Concerns;

use App\Models\ChatMessage;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tambahkan ke model User: `use HasPremium;`
 * Premium = admin, atau punya langganan berstatus paid yang belum kedaluwarsa.
 */
trait HasPremium
{
    protected ?Subscription $activeSubscriptionCache = null;
    protected bool $activeSubscriptionLoaded = false;

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function activeSubscription(): ?Subscription
    {
        if (! $this->activeSubscriptionLoaded) {
            $this->activeSubscriptionCache = $this->subscriptions()
                ->where('status', 'paid')
                ->where('ends_at', '>', now())
                ->latest('ends_at')
                ->first();

            $this->activeSubscriptionLoaded = true;
        }

        return $this->activeSubscriptionCache;
    }

    public function isPremium(): bool
    {
        return $this->role === 'admin' || $this->activeSubscription() !== null;
    }
}