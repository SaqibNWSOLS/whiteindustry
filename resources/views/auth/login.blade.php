@extends('layouts.guest')

@section('content')
    <!-- Simple Language Switcher -->
    <div class="logo" style="background: #163f2a; padding: 10px; text-align: center;">
        <img src="{{ asset('logo.png') }}" alt="logo">
    </div>

    <h2 style="font-size:20px; margin-bottom:12px;">{{ __('auth.login.title') }}</h2>
    
    @if($errors->any())
        <div style="color:#b91c1c; margin-bottom:8px;">
            {{ $errors->first() }}
        </div>
    @endif
    
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div style="margin-bottom:10px;">
            <label style="display:block; margin-bottom:6px;">{{ __('auth.login.email') }}</label>
            <input name="email" type="email" required class="form-input" value="{{ old('email') }}" />
        </div>
        
        <div style="margin-bottom:10px;">
            <label style="display:block; margin-bottom:6px;">{{ __('auth.login.password') }}</label>
            <input name="password" type="password" required class="form-input" />
        </div>
        
        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
            <label style="font-size:13px;">
                <input type="checkbox" name="remember"> {{ __('auth.login.remember_me') }}
            </label>
            <button class="btn" type="submit">{{ __('auth.login.button') }}</button>
        </div>
    </form>
@endsection