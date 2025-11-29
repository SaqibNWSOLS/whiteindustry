<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\Setting;

if (! function_exists('priceFormat')) {
   function priceFormat($price)
    {
        

        return  round((float)$price,2).' DZD';
    }
}


if (! function_exists('dateFormat')) {

    
    function dateFormat($date){


        $date=date('d-M-Y',strtotime($date));

        return $date;

    }

}

if (! function_exists('dateTimeFormat')) {

    
    function dateTimeFormat($date){


        $date=date('Y-m-d h:i A',strtotime($date));

        return $date;

    }

}
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

if (!function_exists('setting')) {
    /**
     * Get / set the specified setting value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param string|array $key
     * @param mixed $default
     * @return mixed|Setting
     */
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('settings');
        }

        if (is_array($key)) {
            return Setting::setValue($key[0], $key[1]);
        }

        return Setting::getValue($key, $default);
    }
}