<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Login - White Industry')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; color: #111827; }
        .center { min-height: 100vh; display:flex; align-items:center; justify-content:center; }
        .card { background:white; padding:24px; border-radius:8px; box-shadow:0 8px 30px rgba(0,0,0,0.08); width:100%; max-width:420px; }
        .form-input { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px; }
        .btn { background:#163f2a; color:white; border:none; padding:10px 14px; border-radius:6px; cursor:pointer; }
    </style>
</head>
<body>
    <div class="center">
        <div class="card">
            @yield('content')
        </div>
    </div>
</body>
</html>