@extends('layouts.app')

@section('title', 'Administration')

@section('content')
    <div class="content">
        <div id="admin" class="module active">
            <div class="tabs">
                <div class="tab-nav">
                    <button class="tab-button active" onclick="showTab('admin', 'profile', this)">My Profile</button>
                    {{-- <button class="tab-button" onclick="showTab('admin', 'users', this)">Users</button> --}}
                    <button class="tab-button" onclick="showTab('admin', 'settings', this)">Settings</button>
                </div>
            </div>

            <div id="admin-profile" class="tab-content active">
                <div class="card">
                    <form id="admin-profile-form" action="/admin/profile" method="post">
                        @csrf
                        @method('PUT')
                    <h3 style="margin-bottom: 20px;"><i class="ti ti-user-circle"></i> Administrator Profile</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-input" value="{{ Auth::user()->first_name }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-input" value="{{ Auth::user()->last_name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" value="{{ Auth::user()->email }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" value="{{ Auth::user()->phone }}">
                    </div>
                    {{-- <div class="form-group">
                        @if(Auth::user()->isAdmin())
                        <label class="form-label">Department</label>
                        <input type="text" class="form-input" value="{{ Auth::user()->department}}"
                            style="background: #f5f5f5;">
                        @else  
                        <label class="form-label">Department</label>
                        <input type="text" class="form-input" value="{{ Auth::user()->department}}" readonly
                            style="background: #f5f5f5;">  
                        @endif    
                    </div> --}}
                    <button id="admin-profile-save" class="btn btn-primary" type="submit">
                        <i class="ti ti-device-floppy"></i> Save Changes
                    </button>
                    </form>
                </div>
            </div>

            {{-- <div id="admin-users" class="tab-content">
                <div class="module-header">
                    <button class="btn btn-primary" onclick="createItem('user')"><i class="ti ti-user-plus"></i> Add
                        User</button>
                    <input type="search" class="search-input" placeholder="Search users...">
                </div>
                <div class="table-container">
                    <div class="table-header">
                        <h3>User Management</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>USR-001</td>
                                <td>Pierre Blanc</td>
                                <td>pierre.blanc@whiteindustry.com</td>
                                <td><span class="badge badge-info">Administrator</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>USR-002</td>
                                <td>Marie Dubois</td>
                                <td>marie.dubois@whiteindustry.com</td>
                                <td><span class="badge badge-info">Manager</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> --}}

            <div id="admin-settings" class="tab-content">
                <div class="card">
                    <form id="admin-settings-form" action="/admin/settings" method="post">
                        @csrf
                        @method('PUT')
                        <h3 style="margin-bottom: 20px;"><i class="ti ti-settings"></i> System Settings</h3>
                        <div class="form-group">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-input" value="White Industry">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Tax ID</label>
                                <input type="text" name="tax_id" class="form-input" value="DZ-123456789">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-input" value="+213 21 123 456">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Default Currency</label>
                            <select class="form-select" name="default_currency">
                                <option value="DZD" selected>DZD - Algerian Dinar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="USD">USD - US Dollar</option>
                            </select>
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
