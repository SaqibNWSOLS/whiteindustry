<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QaDocument extends Model
{
     protected $fillable = ['qa_quotes_id', 'document_name', 'file_path', 'file_type', 'file_size', 'uploaded_by'];

    public function qaDepartment() {
        return $this->belongsTo(QaDepartment::class);
    }

    public function uploadedBy() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
