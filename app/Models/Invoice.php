<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    protected $fillable = ['invoice_number', 'production_id', 'customer_id', 'invoice_date', 'due_date', 'subtotal', 'tax_amount', 'total_amount', 'status', 'notes'];
    protected $dates = ['invoice_date', 'due_date'];

    public function production() {
        return $this->belongsTo(Production::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }
}