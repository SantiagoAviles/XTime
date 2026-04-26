# Altermec Asistencia

Base empresarial en Laravel para un sistema web de control de asistencia, gestion de horarios, portal de autogestion y futuras integraciones con QR, DNI, RFID o biometria.

## 1. Resumen

- Laravel 13 con PHP 8.4.
- MySQL en Docker con la base `db_asistencia_altermec`.
- Bootstrap 5 con Vite y Blade.
- Soporte para Excel, PDF, roles/permisos y auditoria.
- Estructura modular preparada para crecer.

## 2. Requisitos

| Opcion | Requisitos |
|--------|-----------|
| Docker (recomendada) | Docker y Docker Compose |
| Local | PHP >= 8.4, Composer, Node/NPM, Docker |

## 3. Opcion A — Todo en Docker

Un solo comando levanta la app y la base de datos:

```bash
cp .env.example .env
docker compose up -d --build
```

Luego inicializar la base de datos:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

App disponible en `http://localhost:8000`.

El contenedor instala Composer, Node, compila assets y genera `APP_KEY` automaticamente.

## 4. Opcion B — Laravel local + MySQL en Docker

```bash
cp .env.example .env
# Editar en .env: DB_HOST=127.0.0.1 y DB_PORT=3307

composer install
php artisan key:generate
docker compose up -d mysql
npm install
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

App disponible en `http://localhost:8000`.

## 5. Reiniciar desde cero

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

## 6. Comandos utiles

```bash
# Ver estado
docker compose ps

# Ver logs
docker compose logs -f app

# Shell del contenedor
docker compose exec app bash

# Artisan en Docker
docker compose exec app php artisan <comando>

# Detener
docker compose down        # solo detiene
docker compose down -v     # detiene y borra volumenes (BD incluida)
```

## 7. Dependencias instaladas

PHP: `laravel/framework`, `maatwebsite/excel`, `barryvdh/laravel-dompdf`, `spatie/laravel-permission`, `spatie/laravel-activitylog`

Frontend: `bootstrap`, `@popperjs/core`, `vite`, `axios`

## 8. Estructura del proyecto

```text
app/
├── Domain/
│   ├── Asistencias/
│   ├── Auditoria/
│   ├── Empleados/
│   ├── Horarios/
│   ├── Integraciones/
│   ├── Reportes/
│   ├── Turnos/
│   └── Usuarios/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   └── Web/
│   └── Requests/
└── Models/

resources/views/
├── layouts/
│   └── partials/
├── dashboard/
└── [modulos]/

routes/
├── api.php
└── web.php
```

## 9. Base de datos

### Tablas Laravel base

| Tabla | Descripcion |
|-------|-------------|
| `users` | Autenticacion (email, password, is_active) |
| `password_reset_tokens` | Tokens de recuperacion de contrasena |
| `sessions` | Sesiones activas en BD |
| `cache` / `cache_locks` | Cache de Laravel |
| `jobs` / `job_batches` / `failed_jobs` | Cola de trabajos asincronos |

### Tablas Spatie Permission

| Tabla | Descripcion |
|-------|-------------|
| `permissions` | Permisos del sistema |
| `roles` | Roles del sistema |
| `model_has_permissions` | Permisos asignados directamente a modelos |
| `model_has_roles` | Roles asignados a usuarios |
| `role_has_permissions` | Permisos que tiene cada rol |

### Tabla Spatie Activity Log

| Tabla | Descripcion |
|-------|-------------|
| `activity_log` | Registro de auditoría de cambios en modelos |

### Tablas de negocio — Sprint 1

| Tabla | Descripcion |
|-------|-------------|
| `areas` | Departamentos/areas de la empresa |
| `empleados` | Empleados consolidados con SoftDelete |
| `area_supervisor` | Pivot: supervisor asignado a un area |

### Relaciones principales

```
User         1 ──── 0..1  Empleado
Empleado     N ────── 1   Area           (area principal)
Empleado     N ──────N    Area           (como supervisor) via area_supervisor
User         N ──────N    Role           (Spatie)
Role         N ──────N    Permission     (Spatie)
```

### Decision de diseno

Los tipos de usuario del sistema anterior (Funcionario, Supervisor, Administrador, JefeOperaciones, Trabajador) se consolidan en una unica tabla `empleados`, diferenciandose por roles de Spatie. Esto evita duplicacion y simplifica las consultas.

### Roles del sistema

| Rol | Permisos clave |
|-----|---------------|
| Administrador | Todos |
| RRHH | Gestion completa de empleados y areas |
| Supervisor | Dashboard + ver empleados |
| Jefe de Operaciones | Dashboard + ver empleados |
| Empleado | Solo dashboard |

### Permisos definidos

`access_dashboard` · `view_employees` · `create_employees` · `edit_employees` · `delete_employees` · `manage_areas` · `assign_roles` · `view_activity_log`

### Usuario administrador inicial

| Campo | Valor |
|-------|-------|
| Email | admin@altermec.com |
| Password | Admin1234! |
| Rol | Administrador |
| Area | Administracion |

## 10. Que quedo listo

- Proyecto Laravel base con Docker completo.
- MySQL dockerizado, `.env` y `.env.example` listos.
- Bootstrap 5 integrado, layout Blade con navbar y sidebar.
- Paquetes de Excel, PDF, permisos y auditoria instalados.
- Migraciones base creadas (areas, empleados, area_supervisor).
- Seeders de roles, permisos, areas y usuario administrador.
- Rutas web y API separadas.

## 11. Que falta desarrollar

- Autenticacion con login/logout y recuperacion de contrasena.
- CRUDs por modulo (empleados, areas, usuarios).
- Vistas funcionales con Bootstrap.
- Logica de asistencia, horarios y turnos.
- Reportes y exportaciones.
- Integraciones con QR, RFID, DNI o biometria.
- API REST.

## 12. Proximos pasos

1. Login, logout y recuperacion de contrasena.
2. CRUD de empleados con validaciones.
3. CRUD de areas.
4. Dashboard por rol.
5. Flujo de marcacion y regularizacion.
5. Reportes y exportaciones Excel/PDF.

