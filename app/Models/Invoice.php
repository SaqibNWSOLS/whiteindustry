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
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    public function markAsIssued()
    {
        $this->update([
            'status' => 'issued',
            'invoice_date' => now()
        ]);
    }

    public function getTaxPercentage()
    {
        if ($this->subtotal == 0) {
            return 0;
        }
        return round(($this->tax_amount / $this->subtotal) * 100, 2);
    }

     public function payments()
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