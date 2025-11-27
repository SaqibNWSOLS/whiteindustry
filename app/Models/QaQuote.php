<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QaQuote extends Model
{

    protected $fillable = ['orders_id', 'rnd_quotes_id', 'sent_at', 'approved_at', 'qa_notes', 'status'];
    protected $dates = ['sent_at', 'approved_at'];

    public function order() {
        return $this->belongsTo(Order::class,'orders_id');
    }

    public function rndQuote() {
        return $this->belongsTo(RndQuote::class,'rnd_quotes_id');
    }

    public function documents() {
        return $this->hasMany(QaDocument::class,'qa_quotes_id');
    }
    
}
