<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .guest-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at top left, rgba(15, 76, 92, 0.12), transparent 30rem),
                linear-gradient(160deg, #0f4c5c 0%, #123848 45%, #0d2e3a 100%);
        }
        .guest-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        .guest-header {
            background: linear-gradient(135deg, #0f4c5c 0%, #e36414 100%);
            padding: 2rem;
            text-align: center;
            color: #ffffff;
        }
        .guest-header .brand-name {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .guest-header .brand-sub {
            font-size: 0.8rem;
            opacity: 0.85;
            margin-top: 0.25rem;
        }
        .guest-body {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="guest-wrapper">
        <div class="guest-card">
            <div class="guest-header">
                <div class="brand-name">{{ config('app.name') }}</div>
                <div class="brand-sub">Sistema de Control de Asistencia</div>
            </div>
            <div class="guest-body">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
