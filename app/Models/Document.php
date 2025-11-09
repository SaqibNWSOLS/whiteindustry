<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'type', 'file_path', 'file_name', 'mime_type', 'file_size',
        'version', 'expiry_date', 'related_type', 'related_id', 'description', 'uploaded_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function related()
    {
        return $this->morphTo();
    }
}

