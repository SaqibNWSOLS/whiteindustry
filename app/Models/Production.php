<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    protected $fillable = ['production_number', 'order_id', 'start_date', 'end_date', 'production_notes', 'status'];
    protected $dates = ['start_date', 'end_date'];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }
}
