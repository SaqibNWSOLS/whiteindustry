@extends('layouts.guest')

@section('content')
    <h2 style="font-size:20px; margin-bottom:12px;">Sign in to White Industry</h2>
    @if($errors->any())
        <div style="color:#b91c1c; margin-bottom:8px;">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div style="margin-bottom:10px;">
            <label style="display:block; margin-bottom:6px;">Email</label>
            <input name="email" type="email" required class="form-input" />
        </div>
        <div style="margin-bottom:10px;">
            <label style="display:block; margin-bottom:6px;">Password</label>
            <input name="password" type="password" required class="form-input" />
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
            <label style="font-size:13px;"><input type="checkbox" name="remember"> Remember me</label>
            <button class="btn" type="submit">Login</button>
        </div>
    </form>
@endsection