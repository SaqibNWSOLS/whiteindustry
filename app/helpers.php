<?php

if (!function_exists('activity')) {
    function activity()
    {
        return app(\App\Services\ActivityLogger::class);
    }
}

if (!function_exists('route_if_exists')) {
    /**
     * Return the URL for a named route if it exists, otherwise return a safe fallback ('#').
     *
     * @param  string  $name
     * @param  mixed   $parameters
     * @param  bool    $absolute
     * @return string
     */
    function route_if_exists(string $name, $parameters = [], bool $absolute = true)
    {
        try {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name, $parameters, $absolute);
            }
        } catch (\Throwable $e) {
            // If anything goes wrong (e.g., Route facade not available), fall through to fallback
        }

        return '#';
    }
}