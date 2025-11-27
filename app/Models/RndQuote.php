<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndQuote extends Model
{
    protected $fillable = ['quote_id', 'sent_at', 'approved_at', 'rnd_notes', 'status'];
    protected $dates = ['sent_at', 'approved_at'];

    public function quote() {
        return $this->belongsTo(Quote::class);
    }

    public function documents() {
        return $this->hasMany(RndDocument::class,'rnd_quotes_id');
    }
}
