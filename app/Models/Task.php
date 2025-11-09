<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    // Do NOT allow task_id to be mass-assigned from user input; it is generated server-side
    protected $fillable = [
        'title', 'description', 'assigned_to', 'due_date',
        'priority', 'status', 'related_type', 'related_id',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate a readable task_id after the model has been created so we can use the
        // auto-incremented numeric id and avoid collisions that happen when multiple
        // records are created concurrently.
        static::created(function ($task) {
            if (empty($task->task_id)) {
                $task->task_id = 'TSK-' . str_pad($task->id, 3, '0', STR_PAD_LEFT);
                // Save without firing model events to avoid recursion
                $task->saveQuietly();
            }
        });
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function related()
    {
        return $this->morphTo();
    }
}