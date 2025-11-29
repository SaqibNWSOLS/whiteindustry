<?php

return [
    'administration' => 'Administration',
    
    // Tabs
    'tabs' => [
        'profile' => 'My Profile',
        'password' => 'Change Password',
        'settings' => 'Settings',
    ],
    
    // Profile Tab
    'profile' => [
        'title' => 'Administrator Profile',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'address' => 'Address',
        'save_changes' => 'Save Changes',
    ],
    
    // Password Tab
    'password' => [
        'title' => 'Change Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm New Password',
        'update_password' => 'Update Password',
        'password_requirements' => 'Password must contain at least:',
        'requirements' => [
            'min_chars' => '8 characters',
            'uppercase' => '1 uppercase letter',
            'lowercase' => '1 lowercase letter',
            'number' => '1 number',
            'special_char' => '1 special character',
        ],
    ],
    
    // Settings Tab
    'settings' => [
        'title' => 'System Settings',
        'company_name' => 'Company Name',
        'tax_id' => 'Tax ID',
        'phone' => 'Phone',
        'default_currency' => 'Default Currency',
        'timezone' => 'Timezone',
        'company_address' => 'Company Address',
        'email_signature' => 'Email Signature',
        'save_settings' => 'Save Settings',
    ],
    
    // Form Placeholders
    'placeholders' => [
        'enter_address' => 'Enter your address',
        'enter_current_password' => 'Enter current password',
        'enter_new_password' => 'Enter new password',
        'confirm_new_password' => 'Confirm new password',
        'enter_company_address' => 'Enter company address',
        'enter_email_signature' => 'Enter email signature',
    ],
    
    // Success Messages
    'success' => [
        'profile_updated' => 'Profile updated successfully',
        'password_updated' => 'Password updated successfully',
        'settings_updated' => 'Settings updated successfully',
    ],
    
    // Error Messages
    'error' => [
        'password_mismatch' => 'New password and confirmation do not match!',
        'password_update_failed' => 'Failed to update password',
    ],
    
    // Currencies
    'currencies' => [
        'DZD' => 'DZD - Algerian Dinar',
        'EUR' => 'EUR - Euro',
        'USD' => 'USD - US Dollar',
    ],
    
    // Timezones
    'timezones' => [
        'Africa/Algiers' => 'Algiers (GMT+1)',
        'UTC' => 'UTC',
        'Europe/Paris' => 'Paris (GMT+1)',
    ],
    
    // Buttons & States
    'buttons' => [
        'saving' => 'Saving...',
        'updating' => 'Updating...',
    ],
    
    // Validation
    'validation' => [
        'required' => 'This field is required',
    ],
];