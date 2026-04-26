@extends('layouts.app')

@section('title', 'Dashboard')
@section('section_label', 'Sprint 1')
@section('page_title', 'Bienvenido, {{ $user->name }}')
@section('page_description', 'Panel principal del sistema de control de asistencia ALTERMEC.')

@section('content')
    {{-- Bienvenida y rol --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="dashboard-card p-4 d-flex align-items-center gap-4 flex-wrap">
                <div>
                    <h2 class="h5 mb-1">Hola, {{ $user->name }}</h2>
                    <p class="text-secondary mb-0" style="font-size: 0.875rem;">
                        Rol:
                        <span class="badge text-bg-light border fw-semibold" style="color: #0f4c5c;">
                            {{ $role?->name ?? 'Sin rol asignado' }}
                        </span>
                    </p>
                </div>
                <div class="ms-auto">
                    <span class="badge rounded-pill border" style="background: rgba(15,76,92,0.07); color: #0f4c5c;">
                        Sprint 1 — Base técnica activa
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas de estado --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-card h-100 p-4">
                <div class="card-icon mb-3">DB</div>
                <h3 class="h6 fw-semibold">Base de datos</h3>
                <p class="text-secondary mb-0" style="font-size: 0.875rem;">
                    MySQL activo con tablas de negocio, roles y auditoría.
                </p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dashboard-card h-100 p-4">
                <div class="card-icon mb-3">🔐</div>
                <h3 class="h6 fw-semibold">Autenticación</h3>
                <p class="text-secondary mb-0" style="font-size: 0.875rem;">
                    Login, logout y recuperación de contraseña operativos.
                </p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dashboard-card h-100 p-4">
                <div class="card-icon mb-3">ACL</div>
                <h3 class="h6 fw-semibold">Roles y permisos</h3>
                <p class="text-secondary mb-0" style="font-size: 0.875rem;">
                    RBAC con Spatie configurado. Accesos restringidos por rol.
                </p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dashboard-card h-100 p-4">
                <div class="card-icon mb-3">LOG</div>
                <h3 class="h6 fw-semibold">Auditoría</h3>
                <p class="text-secondary mb-0" style="font-size: 0.875rem;">
                    Activity log configurado y listo para registrar cambios.
                </p>
            </div>
        </div>
    </div>

    {{-- Próximos módulos --}}
    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="dashboard-card p-4">
                <h2 class="h6 fw-semibold mb-3">Accesos disponibles según rol</h2>
                <ul class="mb-0 text-secondary" style="font-size: 0.875rem;">
                    @can('view_employees')
                        <li>Gestión de empleados</li>
                    @endcan
                    @can('manage_areas')
                        <li>Gestión de áreas</li>
                    @endcan
                    @can('assign_roles')
                        <li>Gestión de usuarios y roles</li>
                    @endcan
                    @can('view_activity_log')
                        <li>Registro de auditoría</li>
                    @endcan
                    @if (! $user->can('view_employees') && ! $user->can('manage_areas') && ! $user->can('assign_roles'))
                        <li>Sin módulos adicionales asignados todavía.</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="dashboard-card p-4">
                <h2 class="h6 fw-semibold mb-3">Próximos módulos a desarrollar</h2>
                <ul class="mb-0 text-secondary" style="font-size: 0.875rem;">
                    <li>CRUD completo de empleados y áreas.</li>
                    <li>Gestión de horarios y turnos.</li>
                    <li>Registro de asistencia (QR, DNI, biometría).</li>
                    <li>Reportes y exportaciones PDF/Excel.</li>
                    <li>Portal de autogestión del empleado.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
