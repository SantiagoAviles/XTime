@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <h5 class="fw-semibold mb-1 text-center" style="color: #0f4c5c;">Bienvenido</h5>
    <p class="text-muted text-center mb-4" style="font-size: 0.875rem;">
        Ingresa tus credenciales para continuar
    </p>

    <form method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-medium" style="font-size: 0.875rem;">
                Correo electrónico
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                autofocus
                class="form-control @error('email') is-invalid @enderror"
                placeholder="usuario@altermec.com"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-medium mb-0" style="font-size: 0.875rem;">
                    Contraseña
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size: 0.8rem; color: #e36414;">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="••••••••"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Recuérdame --}}
        <div class="mb-4">
            <div class="form-check">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="form-check-input"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label for="remember" class="form-check-label" style="font-size: 0.875rem;">
                    Mantener sesión iniciada
                </label>
            </div>
        </div>

        {{-- Error general (credenciales incorrectas) --}}
        @if ($errors->has('credentials'))
            <div class="alert alert-danger py-2 mb-3" style="font-size: 0.875rem;">
                {{ $errors->first('credentials') }}
            </div>
        @endif

        {{-- Botón --}}
        <button
            type="submit"
            class="btn w-100 fw-semibold"
            style="background: linear-gradient(135deg, #0f4c5c, #123848); color: #fff; padding: 0.6rem;"
        >
            Iniciar sesión
        </button>
    </form>
@endsection
