<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\Setting;
use App\Models\User;
use App\Models\Notification;


if (!function_exists('sendNotificationToRole')) {
    /**
     * Send notification to all users with specific role
     *
     * @param string|array $roles Single role or array of roles
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string $priority
     * @param array $additionalData
     * @return int Number of notifications sent
     */
    function sendNotificationToRole($roles, $type, $title, $message, $priority = 'normal', $additionalData = [])
    {
        // Convert single role to array
        $roles = (array) $roles;
        
        // Get all users with the specified roles
        $users = User::role($roles)->get();
        
        $notifications = [];
        $now = now();
        
        foreach ($users as $user) {
            $notificationData = [
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'priority' => $priority,
                'user_id' => $user->id,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            // Add additional data if provided
            if (!empty($additionalData)) {
                $notificationData = array_merge($notificationData, $additionalData);
            }
            
            $notifications[] = $notificationData;
        }
        
        // Bulk insert for better performance
        if (!empty($notifications)) {
            Notification::insert($notifications);
        }
        
        return count($notifications);
    }
}

if (!function_exists('sendNotificationToUser')) {
    /**
     * Send notification to specific user
     *
     * @param User $user
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string $priority
     * @param array $additionalData
     * @return Notification
     */
    function sendNotificationToUser($user, $type, $title, $message, $priority = 'normal', $additionalData = [])
    {
        $notificationData = [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'priority' => $priority,
            'user_id' => $user->id,
            'is_read' => false,
        ];
        
        // Add additional data if provided
        if (!empty($additionalData)) {
            $notificationData = array_merge($notificationData, $additionalData);
        }
        
        return Notification::create($notificationData);
    }
}

if (! function_exists('handleResponse')) {
    function handleResponse($request, string $message, string $redirectRoute, $statusCode = 200, array $extra = []): JsonResponse|RedirectResponse
    {
        if ($request->ajax()) {
            return response()->json(array_merge([
                'status' => $statusCode,
                'message' => $message,
                'redirect_url' => route($redirectRoute),
            ], $extra))->setEncodingOptions(JSON_NUMERIC_CHECK)->setStatusCode($statusCode);
        }

        if ($statusCode !== 200) {
            // Send errors via session for redirect
            return redirect($redirectRoute)->with('error', $message)->withErrors($extra['errors'] ?? []);
        }

        return redirect($redirectRoute)->with('success', $message);
    }
}


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

if (!function_exists('notify')) {
    function notify()
    {
        return app(\App\Services\NotificationService::class);
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