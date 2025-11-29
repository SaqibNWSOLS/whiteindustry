@extends('layouts.app')

@section('title', 'Administration')

@section('content')
    <div class="content">
        <div id="admin" class="module active">
            <div class="tabs">
                <div class="tab-nav">
                    <button class="tab-button active" onclick="showTab('admin', 'profile', this)">My Profile</button>
                    <button class="tab-button" onclick="showTab('admin', 'password', this)">Change Password</button>
                    @if(Auth::user()->hasRole('Administrator'))
                    <button class="tab-button" onclick="showTab('admin', 'settings', this)">Settings</button>
                    @endif
                </div>
            </div>

            <div id="admin-profile" class="tab-content active">
                <div class="card">
                    <form id="admin-profile-form" action="{{ route('admin.profile.update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <h3 style="margin-bottom: 20px;"><i class="ti ti-user-circle"></i> Administrator Profile</h3>
                        
                        @if(session('profile_success'))
                            <div class="alert alert-success">
                                {{ session('profile_success') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul style="margin: 0; padding-left: 16px;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-input @error('first_name') error @enderror" 
                                       value="{{ old('first_name', Auth::user()->first_name) }}" required>
                                @error('first_name')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-input @error('last_name') error @enderror" 
                                       value="{{ old('last_name', Auth::user()->last_name) }}" required>
                                @error('last_name')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-input @error('email') error @enderror" 
                                       value="{{ old('email', Auth::user()->email) }}" required>
                                @error('email')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-input @error('phone') error @enderror" 
                                       value="{{ old('phone', Auth::user()->phone) }}">
                                @error('phone')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-input @error('address') error @enderror" 
                                      rows="3" placeholder="Enter your address">{{ old('address', Auth::user()->address) }}</textarea>
                            @error('address')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <button id="admin-profile-save" class="btn btn-primary" type="submit">
                            <i class="ti ti-device-floppy"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <div id="admin-password" class="tab-content">
                <div class="card">
                    <form id="admin-password-form" action="{{ route('admin.password.update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <h3 style="margin-bottom: 20px;"><i class="ti ti-lock"></i> Change Password</h3>
                        
                        @if(session('password_success'))
                            <div class="alert alert-success">
                                {{ session('password_success') }}
                            </div>
                        @endif
                        
                        @if(session('password_error'))
                            <div class="alert alert-danger">
                                {{ session('password_error') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">Current Password *</label>
                            <input type="password" name="current_password" class="form-input @error('current_password') error @enderror" 
                                   placeholder="Enter current password" required>
                            @error('current_password')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">New Password *</label>
                                <input type="password" name="new_password" class="form-input @error('new_password') error @enderror" 
                                       placeholder="Enter new password" required>
                                @error('new_password')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm New Password *</label>
                                <input type="password" name="new_password_confirmation" class="form-input" 
                                       placeholder="Confirm new password" required>
                            </div>
                        </div>

                        <div class="password-requirements">
                            <small>Password must contain at least:</small>
                            <ul style="margin: 8px 0 0 16px; font-size: 12px; color: #666;">
                                <li>8 characters</li>
                                <li>1 uppercase letter</li>
                                <li>1 lowercase letter</li>
                                <li>1 number</li>
                                <li>1 special character</li>
                            </ul>
                        </div>

                        <button id="admin-password-save" class="btn btn-primary" type="submit">
                            <i class="ti ti-key"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>

            <div id="admin-settings" class="tab-content">
                <div class="card">
                    <form id="admin-settings-form" action="{{ route('admin.settings.update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <h3 style="margin-bottom: 20px;"><i class="ti ti-settings"></i> System Settings</h3>
                        
                        @if(session('settings_success'))
                            <div class="alert alert-success">
                                {{ session('settings_success') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">Company Name *</label>
                            <input type="text" name="company_name" class="form-input @error('company_name') error @enderror" 
                                   value="{{ old('company_name', $settings->company_name ?? 'White Industry') }}" required>
                            @error('company_name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Tax ID</label>
                                <input type="text" name="tax_id" class="form-input @error('tax_id') error @enderror" 
                                       value="{{ old('tax_id', $settings->tax_id ?? 'DZ-123456789') }}">
                                @error('tax_id')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-input @error('phone') error @enderror" 
                                       value="{{ old('phone', $settings->phone ?? '+213 21 123 456') }}">
                                @error('phone')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Default Currency *</label>
                                <select class="form-select @error('default_currency') error @enderror" name="default_currency" required>
                                    <option value="DZD" {{ (old('default_currency', $settings->default_currency ?? 'DZD') == 'DZD') ? 'selected' : '' }}>DZD - Algerian Dinar</option>
                                    <option value="EUR" {{ (old('default_currency', $settings->default_currency ?? 'DZD') == 'EUR') ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="USD" {{ (old('default_currency', $settings->default_currency ?? 'DZD') == 'USD') ? 'selected' : '' }}>USD - US Dollar</option>
                                </select>
                                @error('default_currency')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Timezone *</label>
                                <select class="form-select @error('timezone') error @enderror" name="timezone" required>
                                    <option value="Africa/Algiers" {{ (old('timezone', $settings->timezone ?? 'Africa/Algiers') == 'Africa/Algiers') ? 'selected' : '' }}>Algiers (GMT+1)</option>
                                    <option value="UTC" {{ (old('timezone', $settings->timezone ?? 'Africa/Algiers') == 'UTC') ? 'selected' : '' }}>UTC</option>
                                    <option value="Europe/Paris" {{ (old('timezone', $settings->timezone ?? 'Africa/Algiers') == 'Europe/Paris') ? 'selected' : '' }}>Paris (GMT+1)</option>
                                </select>
                                @error('timezone')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Company Address</label>
                            <textarea name="company_address" class="form-input @error('company_address') error @enderror" 
                                      rows="3" placeholder="Enter company address">{{ old('company_address', $settings->company_address ?? '') }}</textarea>
                            @error('company_address')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Signature</label>
                            <textarea name="email_signature" class="form-input @error('email_signature') error @enderror" 
                                      rows="3" placeholder="Enter email signature">{{ old('email_signature', $settings->email_signature ?? '') }}</textarea>
                            @error('email_signature')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <button id="admin-settings-save" type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Tab navigation function
    function showTab(module, tab, element) {
        // Hide all tab contents
        document.querySelectorAll(`#${module} .tab-content`).forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll(`#${module} .tab-button`).forEach(button => {
            button.classList.remove('active');
        });
        
        // Show selected tab content
        document.getElementById(`${module}-${tab}`).classList.add('active');
        
        // Add active class to clicked tab button
        element.classList.add('active');
    }

    // Form submission handlers
    document.addEventListener('DOMContentLoaded', function() {
        // Profile form
        const profileForm = document.getElementById('admin-profile-form');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                const saveButton = document.getElementById('admin-profile-save');
                saveButton.innerHTML = '<i class="ti ti-loader"></i> Saving...';
                saveButton.disabled = true;
            });
        }

        // Password form
        const passwordForm = document.getElementById('admin-password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                const newPassword = document.querySelector('input[name="new_password"]').value;
                const confirmPassword = document.querySelector('input[name="new_password_confirmation"]').value;
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New password and confirmation do not match!');
                    return;
                }

                const saveButton = document.getElementById('admin-password-save');
                saveButton.innerHTML = '<i class="ti ti-loader"></i> Updating...';
                saveButton.disabled = true;
            });
        }

        // Settings form
        const settingsForm = document.getElementById('admin-settings-form');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function(e) {
                const saveButton = document.getElementById('admin-settings-save');
                saveButton.innerHTML = '<i class="ti ti-loader"></i> Saving...';
                saveButton.disabled = true;
            });
        }
    });
</script>
@endpush

<style>
.alert {
    padding: 12px 16px;
    margin-bottom: 20px;
    border-radius: 6px;
    border-left: 4px solid;
}

.alert-success {
    background-color: #f0f9f4;
    border-color: #10b981;
    color: #065f46;
}

.alert-danger {
    background-color: #fef2f2;
    border-color: #ef4444;
    color: #7f1d1d;
}

.form-error {
    color: #ef4444;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

.form-input.error {
    border-color: #ef4444;
}

.password-requirements {
    background-color: #f8fafc;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 16px;
    border-left: 4px solid #3b82f6;
}

.tab-button {
    transition: all 0.3s ease;
}

.tab-button:hover {
    background-color: #f1f5f9;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>