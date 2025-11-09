<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogger
{
    protected $user;
    protected $subject;
    protected $properties;

    public function causedBy($user)
    {
        $this->user = $user;
        return $this;
    }

    public function performedOn($model)
    {
        $this->subject = $model;
        return $this;
    }

    public function withProperties($properties)
    {
        $this->properties = $properties;
        return $this;
    }

    public function log($description, $action = 'custom')
    {
        return ActivityLog::create([
            'user_id' => $this->user?->id ?? auth()->id(),
            'action' => $action,
            'model_type' => $this->subject ? get_class($this->subject) : null,
            'model_id' => $this->subject?->id,
            'description' => $description,
            'properties' => $this->properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
