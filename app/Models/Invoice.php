<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    // ... existing code ...

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount') ?? 0;
    }

    public function getPendingAmountAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getPaymentProgressAttribute()
    {
        if ($this->total_amount == 0) return 0;
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    public function isFullyPaid()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function isPartiallyPaid()
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->total_amount;
    }
}
