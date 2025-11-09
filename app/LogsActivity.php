<?php

namespace App;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created', 'Created ' . class_basename($model));
        });

        static::updated(function ($model) {
            $model->logActivity('updated', 'Updated ' . class_basename($model), [
                'old' => $model->getOriginal(),
                'new' => $model->getAttributes(),
            ]);
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', 'Deleted ' . class_basename($model));
        });
    }

    public function logActivity($action, $description, $properties = null)
    {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }
}