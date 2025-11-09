<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id', 'type', 'company_name', 'industry_type', 'tax_id',
        'contact_person', 'email', 'phone', 'address', 'city', 'postal_code', 'status',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($customer) {
            if (empty($customer->customer_id)) {
                // Generate a unique customer_id based on the current max numeric suffix found in existing customer_id values.
                // Includes soft-deleted records to avoid reusing identifiers.
                $all = static::withTrashed()->whereNotNull('customer_id')->pluck('customer_id')->toArray();
                $max = 0;
                foreach ($all as $cid) {
                    // Expecting format like CUST-001 or similar; extract trailing number
                    if (preg_match('/(\d+)$/', $cid, $m)) {
                        $num = (int) $m[1];
                        if ($num > $max) $max = $num;
                    }
                }
                $next = $max + 1;
                // Safeguard loop in case of race conditions
                do {
                    $candidate = 'CUST-' . str_pad($next, 3, '0', STR_PAD_LEFT);
                    $exists = static::withTrashed()->where('customer_id', $candidate)->exists();
                    if ($exists) $next++;
                } while ($exists);

                $customer->customer_id = $candidate;
            }
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}
