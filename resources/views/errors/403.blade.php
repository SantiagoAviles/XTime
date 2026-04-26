<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso no autorizado — {{ config('app.name') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at top left, rgba(15, 76, 92, 0.10), transparent 28rem),
                linear-gradient(160deg, #0f4c5c 0%, #123848 50%, #0d2e3a 100%);
            color: #173042;
            padding: 1rem;
        }
        .card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 24px 64px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 460px;
            overflow: hidden;
        }
        .card-top {
            background: linear-gradient(135deg, #c0392b 0%, #922b21 100%);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .code {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
        }
        .label {
            font-size: 0.9rem;
            opacity: 0.85;
            margin-top: 0.4rem;
        }
        .card-body {
            padding: 2rem;
            text-align: center;
        }
        .card-body h2 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #0f4c5c;
        }
        .card-body p {
            font-size: 0.875rem;
            color: #6c7a89;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.55rem 1.4rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.88; }
        .btn-primary {
            background: linear-gradient(135deg, #0f4c5c, #123848);
            color: #fff;
        }
        .btn-outline {
            background: transparent;
            border: 1.5px solid #0f4c5c;
            color: #0f4c5c;
        }
        .actions { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-top">
            <div class="code">403</div>
            <div class="label">Acceso no autorizado</div>
        </div>
        <div class="card-body">
            <h2>No tienes permiso para acceder a esta página</h2>
            <p>
                Si crees que esto es un error, contacta al administrador del sistema
                o verifica que tu cuenta tenga los permisos necesarios.
            </p>
            <div class="actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        Ir al dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        Iniciar sesión
                    </a>
                @endauth
                <a href="javascript:history.back()" class="btn btn-outline">
                    Volver atrás
                </a>
            </div>
        </div>
    </div>
</body>
</html>
