<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $user->first_name = $validated['first_name'] ?? $user->first_name;
        $user->last_name = $validated['last_name'] ?? $user->last_name;
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->save();

        return back()->with('success','Profile Updated successfully!');
    }

    /**
     * Save system settings.
     * We'll persist to storage/app/settings.json for simplicity.
     */
    public function updateSettings(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'company_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'default_currency' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Keep minimal persistent storage in storage/app/settings.json
        $existing = [];
        if (Storage::exists('settings.json')) {
            try {
                $existing = json_decode(Storage::get('settings.json'), true) ?: [];
            } catch (\Exception $e) {
                $existing = [];
            }
        }

        $settings = array_merge($existing, $validated);
        Storage::put('settings.json', json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return back()->with('success','Settings Updated successfully!');
    }
}
