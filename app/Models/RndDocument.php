<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndDocument extends Model
{
     protected $fillable = ['rnd_quotes_id', 'document_name', 'file_path', 'file_type', 'file_size', 'uploaded_by'];

    public function rndQuote() {
        return $this->belongsTo(RndQuote::class,'rnd_quotes_id');
    }

    public function uploadedBy() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
