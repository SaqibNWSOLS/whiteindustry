<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id', 'source', 'company_name', 'contact_person', 'email',
        'phone', 'status', 'estimated_value', 'notes', 'converted_customer_id', 'converted_at',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'converted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($lead) {
            if (empty($lead->lead_id)) {
                // Generate a unique lead_id based on existing lead_id values (including soft-deleted)
                $all = static::withTrashed()->whereNotNull('lead_id')->pluck('lead_id')->toArray();
                $max = 0;
                foreach ($all as $lid) {
                    if (preg_match('/(\d+)$/', $lid, $m)) {
                        $num = (int) $m[1];
                        if ($num > $max) $max = $num;
                    }
                }
                $next = $max + 1;
                do {
                    $candidate = 'LEAD-' . str_pad($next, 3, '0', STR_PAD_LEFT);
                    $exists = static::withTrashed()->where('lead_id', $candidate)->exists();
                    if ($exists) $next++;
                } while ($exists);

                $lead->lead_id = $candidate;
            }
        });
    }

    public function convertedCustomer()
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }
}