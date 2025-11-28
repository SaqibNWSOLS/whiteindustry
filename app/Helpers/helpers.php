<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

if (! function_exists('priceFormat')) {
   function priceFormat($price)
    {
        

        return  round((float)$price,2).' DA';
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