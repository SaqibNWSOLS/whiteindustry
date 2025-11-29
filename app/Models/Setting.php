<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return $setting->getCastValue();
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string|null $description
     * @param bool $isPublic
     * @return Setting
     */
    public static function setValue(
        string $key, 
        $value, 
        string $type = 'string', 
        string $group = 'general', 
        string $description = null, 
        bool $isPublic = false
    ): Setting {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
                'is_public' => $isPublic,
            ]
        );
    }

    /**
     * Get the casted value based on type
     *
     * @return mixed
     */
    public function getCastValue()
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            'float', 'double' => (float) $this->value,
            'json', 'array' => json_decode($this->value, true) ?? $this->value,
            default => $this->value,
        };
    }

    /**
     * Set the value attribute with proper casting
     *
     * @param mixed $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if ($this->type === 'json' || $this->type === 'array') {
            $this->attributes['value'] = json_encode($value);
        } elseif ($this->type === 'boolean') {
            $this->attributes['value'] = $value ? '1' : '0';
        } else {
            $this->attributes['value'] = (string) $value;
        }
    }

    /**
     * Scope a query to only include public settings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query by group.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get all settings grouped by group
     *
     * @return array
     */
    public static function getAllGrouped(): array
    {
        return static::all()
            ->groupBy('group')
            ->map(function ($settings) {
                return $settings->mapWithKeys(function ($setting) {
                    return [$setting->key => $setting->getCastValue()];
                });
            })
            ->toArray();
    }

    /**
     * Get company settings
     *
     * @return array
     */
    public static function getCompanySettings(): array
    {
        return static::group('company')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getCastValue()];
            })
            ->toArray();
    }

    /**
     * Get system settings
     *
     * @return array
     */
    public static function getSystemSettings(): array
    {
        return static::group('system')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getCastValue()];
            })
            ->toArray();
    }

    /**
     * Get email settings
     *
     * @return array
     */
    public static function getEmailSettings(): array
    {
        return static::group('email')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getCastValue()];
            })
            ->toArray();
    }
}