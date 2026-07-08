<div class="sidebar-nav-wrap">
    <div class="mb-3">
        <p class="sidebar-section-title">Inicio</p>
        @can('access_dashboard')
            <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <span>Dashboard</span>
            </a>
        @endcan
    </div>

    @canany(['view_employees', 'manage_areas'])
        <div class="mb-3">
            <p class="sidebar-section-title">Personal</p>
            @can('view_employees')
                <a class="sidebar-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}"
                   href="{{ route('empleados.index') }}">
                    <span>Empleados</span>
                </a>
            @endcan
            @can('manage_areas')
                <a class="sidebar-link {{ request()->routeIs('areas.*') ? 'active' : '' }}"
                   href="{{ route('areas.index') }}">
                    <span>Áreas</span>
                </a>
            @endcan
        </div>
    @endcanany

    @canany(['manage_turnos', 'view_turnos'])
        <div class="mb-3">
            <p class="sidebar-section-title">Horarios</p>
            <a class="sidebar-link {{ request()->routeIs('turnos.*') ? 'active' : '' }}"
               href="{{ route('turnos.index') }}">
                <span>Turnos</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('feriados.*') ? 'active' : '' }}"
               href="{{ route('feriados.index') }}">
                <span>Feriados</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('reglas-horas-extra.*') ? 'active' : '' }}"
               href="{{ route('reglas-horas-extra.index') }}">
                <span>Reglas de horas extra</span>
            </a>
        </div>
    @endcanany

    @canany(['view_attendance_panel', 'view_attendance_own_area', 'mark_attendance_manual'])
        <div class="mb-3">
            <p class="sidebar-section-title">Asistencias</p>
            @canany(['view_attendance_panel', 'view_attendance_own_area'])
                <a class="sidebar-link {{ request()->routeIs('asistencias.panel') ? 'active' : '' }}"
                   href="{{ route('asistencias.panel') }}">
                    <span>Panel en tiempo real</span>
                </a>
            @endcanany
            @can('mark_attendance_manual')
                <a class="sidebar-link {{ request()->routeIs('asistencias.manual.*') ? 'active' : '' }}"
                   href="{{ route('asistencias.manual.create') }}">
                    <span>Marcación manual</span>
                </a>
            @endcan
            <a class="sidebar-link {{ request()->routeIs('asistencias.kiosko') ? 'active' : '' }}"
               href="{{ route('asistencias.kiosko') }}" target="_blank">
                <span>Modo quiosco (QR/DNI)</span>
            </a>
        </div>
    @endcanany

    @can('view_self_portal')
        <div class="mb-3">
            <p class="sidebar-section-title">Mi cuenta</p>
            <a class="sidebar-link {{ request()->routeIs('autogestion.dashboard') ? 'active' : '' }}"
               href="{{ route('autogestion.dashboard') }}">
                <span>Mi dashboard</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('autogestion.historial') ? 'active' : '' }}"
               href="{{ route('autogestion.historial') }}">
                <span>Mi historial</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('autogestion.justificaciones.*') ? 'active' : '' }}"
               href="{{ route('autogestion.justificaciones.index') }}">
                <span>Mis justificaciones</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('asistencias.kiosko') ? 'active' : '' }}"
               href="{{ route('asistencias.kiosko') }}" target="_blank">
                <span>Marcar mi asistencia (QR/DNI)</span>
            </a>
        </div>
    @endcan

    @canany(['approve_justifications', 'manage_justifications'])
        <div class="mb-3">
            <p class="sidebar-section-title">Justificaciones</p>
            <a class="sidebar-link {{ request()->routeIs('justificaciones.index') ? 'active' : '' }}"
               href="{{ route('justificaciones.index') }}">
                <span>Bandeja de revisión</span>
            </a>
        </div>
    @endcanany

    @can('view_reports')
        <div class="mb-3">
            <p class="sidebar-section-title">Reportes</p>
            <a class="sidebar-link {{ request()->routeIs('reportes.asistencia') ? 'active' : '' }}"
               href="{{ route('reportes.asistencia') }}">
                <span>Asistencia mensual</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('reportes.horas-extra') ? 'active' : '' }}"
               href="{{ route('reportes.horas-extra') }}">
                <span>Horas extra</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('reportes.incidencias') ? 'active' : '' }}"
               href="{{ route('reportes.incidencias') }}">
                <span>Incidencias y tardanzas</span>
            </a>
        </div>
    @endcan

    @canany(['assign_roles', 'view_activity_log'])
        <div class="mb-3">
            <p class="sidebar-section-title">Seguridad</p>
            @can('assign_roles')
                <a class="sidebar-link {{ request()->routeIs('seguridad.usuarios.*') ? 'active' : '' }}"
                   href="{{ route('seguridad.usuarios.index') }}">
                    <span>Usuarios y roles</span>
                </a>
            @endcan
            @can('view_activity_log')
                <a class="sidebar-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}"
                   href="{{ route('auditoria.index') }}">
                    <span>Auditoría</span>
                </a>
            @endcan
        </div>
    @endcanany
</div>
