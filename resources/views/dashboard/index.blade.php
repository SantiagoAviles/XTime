@extends('layouts.app')

@section('title', 'Base del proyecto')
@section('section_label', 'Arquitectura inicial')
@section('page_title', 'Base empresarial lista para desarrollar')
@section('page_description', 'El proyecto ya esta preparado con Docker MySQL, Bootstrap 5, paquetes clave y estructura modular.')

@section('content')
    <div class="page-section p-4 p-lg-5 mb-4">
        <div class="row g-4">
            <div class="col-12 col-xl-7">
                <h2 class="h4 mb-3">Estado del proyecto</h2>
                <p class="text-secondary mb-4">
                    Esta pantalla solo valida que la estructura arranque correctamente. No incluye todavia
                    logica de negocio, CRUDs ni reglas de asistencia.
                </p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="dashboard-card h-100 p-4">
                            <div class="card-icon mb-3">DB</div>
                            <h3 class="h5">MySQL en Docker</h3>
                            <p class="text-secondary mb-0">Configurado para trabajar con la base `db_asistencia_altermec`.</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="dashboard-card h-100 p-4">
                            <div class="card-icon mb-3">UI</div>
                            <h3 class="h5">Bootstrap 5 + Blade</h3>
                            <p class="text-secondary mb-0">Layout base listo para seguir con modulos, vistas y componentes.</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="dashboard-card h-100 p-4">
                            <div class="card-icon mb-3">ACL</div>
                            <h3 class="h5">Roles y permisos</h3>
                            <p class="text-secondary mb-0">Paquete instalado para seguridad y perfiles empresariales.</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="dashboard-card h-100 p-4">
                            <div class="card-icon mb-3">LOG</div>
                            <h3 class="h5">Auditoria y trazabilidad</h3>
                            <p class="text-secondary mb-0">Base preparada para registrar actividad y cambios relevantes.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="dashboard-card h-100 p-4">
                    <h2 class="h5 mb-3">Siguiente bloque de desarrollo</h2>
                    <ul class="mb-0 text-secondary">
                        <li>Autenticacion y acceso por perfiles.</li>
                        <li>Catalogos base de empleados, horarios y turnos.</li>
                        <li>Flujos de marcacion y regularizacion.</li>
                        <li>Exportaciones Excel y reportes PDF.</li>
                        <li>Integraciones API, QR, RFID o biometria.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
