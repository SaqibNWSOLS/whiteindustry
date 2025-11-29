<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;

class AdminController extends Controller
{

    public function index()
{
    $settings = Setting::getCompanySettings();
    return view('settings.index', compact('settings'));
}

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $user->update($validator->validated());

        return redirect()->back()->with('profile_success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->current_password, Auth::user()->password)) {
                $validator->errors()->add('current_password', 'The current password is incorrect.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('password_success', 'Password updated successfully!');
    }

    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'default_currency' => 'required|string|in:DZD,EUR,USD',
            'timezone' => 'required|string',
            'company_address' => 'nullable|string|max:500',
            'email_signature' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($validator->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('settings_success', 'Settings updated successfully!');
    }
}