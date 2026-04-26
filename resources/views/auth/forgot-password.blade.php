@extends('layouts.guest')

@section('title', 'Recuperar contraseña')

@section('content')
    <h5 class="fw-semibold mb-1 text-center" style="color: #0f4c5c;">Recuperar contraseña</h5>
    <p class="text-muted text-center mb-4" style="font-size: 0.875rem;">
        Ingresa tu correo para recibir un enlace de recuperación
    </p>

    @if (session('status'))
        <div class="alert alert-success py-2 mb-4" role="alert" style="font-size: 0.875rem;">
            Se ha enviado un enlace de recuperación a tu correo.
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-4">
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

        {{-- Botón --}}
        <button
            type="submit"
            class="btn w-100 fw-semibold mb-3"
            style="background: linear-gradient(135deg, #0f4c5c, #123848); color: #fff; padding: 0.6rem;"
        >
            Enviar enlace
        </button>

        {{-- Enlace de vuelta a login --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-decoration-none" style="font-size: 0.875rem; color: #0f4c5c;">
                Volver a iniciar sesión
            </a>
        </div>
    </form>
@endsection
