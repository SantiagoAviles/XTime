@extends('layouts.guest')

@section('title', 'Restablecer contraseña')

@section('content')
    <h5 class="fw-semibold mb-1 text-center" style="color: #0f4c5c;">Restablecer contraseña</h5>
    <p class="text-muted text-center mb-4" style="font-size: 0.875rem;">
        Ingresa tu nueva contraseña
    </p>

    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

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

        {{-- Nueva contraseña --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-medium" style="font-size: 0.875rem;">
                Nueva contraseña
            </label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="••••••••"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-medium" style="font-size: 0.875rem;">
                Confirmar contraseña
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="••••••••"
            >
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botón --}}
        <button
            type="submit"
            class="btn w-100 fw-semibold mb-3"
            style="background: linear-gradient(135deg, #0f4c5c, #123848); color: #fff; padding: 0.6rem;"
        >
            Restablecer contraseña
        </button>

        {{-- Enlace de vuelta a login --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-decoration-none" style="font-size: 0.875rem; color: #0f4c5c;">
                Volver a iniciar sesión
            </a>
        </div>
    </form>
@endsection
