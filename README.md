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

## 10. Autenticación — Sprint 1 Etapa 2

### 10.1. Rutas de autenticación

| Método | URI | Nombre | Middleware | Controlador |
|--------|-----|---------|-----------|-------------|
| GET | `/login` | `login` | `guest` | `AuthController@showLoginForm` |
| POST | `/login` | `login.store` | `guest` | `AuthController@login` |
| POST | `/logout` | `logout` | `auth` | `AuthController@logout` |
| GET | `/forgot-password` | `password.request` | `guest` | `PasswordResetController@showForgotPasswordForm` |
| POST | `/forgot-password` | `password.email` | `guest` | `PasswordResetController@sendResetLink` |
| GET | `/reset-password/{token}` | `password.reset` | `guest` | `PasswordResetController@showResetPasswordForm` |
| POST | `/reset-password` | `password.update` | `guest` | `PasswordResetController@resetPassword` |

### 10.2. Flujo de login

```
POST /login
  ├─ RateLimiter: ¿superó 5 intentos en 15 min? (clave = email + IP)
  │     └─ SÍ → registra `login_blocked` en activity_log → error 422
  │
  ├─ Auth::attempt(email, password)
  │     └─ FALLA → RateLimiter::hit() → registra `login_failed` → error credenciales
  │
  ├─ ¿Usuario is_active = false?
  │     └─ SÍ → registra `inactive_user_login_attempt` → Auth::logout() → error
  │
  ├─ ¿Usuario sin roles?
  │     └─ SÍ → registra `user_without_role_login_attempt` → Auth::logout() → error
  │
  ├─ RateLimiter::clear()
  ├─ session()->regenerate()
  ├─ Registra `login_success` en activity_log
  └─ Redirige → /dashboard
```

### 10.3. RateLimiter

| Parámetro | Valor |
|-----------|-------|
| Clave | `login-attempts:{email_normalizado}:{ip}` |
| Intentos | 5 |
| Bloqueo | 15 minutos (900 segundos) |
| Clase | `Illuminate\Support\Facades\RateLimiter` |

### 10.4. Eventos auditados en `activity_log`

Todos con `log_name = 'auth'`:

| Evento | Descripción | Propiedades |
|--------|-------------|------------|
| `login_success` | Login exitoso | email, user_id, ip, user_agent |
| `login_failed` | Credenciales incorrectas | email, ip, user_agent |
| `login_blocked` | Bloqueado por RateLimiter | email, ip, user_agent |
| `inactive_user_login_attempt` | Usuario inactivo | email, user_id, ip, user_agent |
| `user_without_role_login_attempt` | Usuario sin rol | email, user_id, ip, user_agent |
| `logout` | Cierre de sesión | email, user_id, ip, user_agent |

**No se registran contraseñas ni datos sensibles.**

### 10.5. Flujo de logout

```
POST /logout (requiere CSRF)
  ├─ Captura Auth::user()
  ├─ Registra `logout` en activity_log (con user_id conservado)
  ├─ Auth::logout()
  ├─ session()->invalidate()
  ├─ session()->regenerateToken()
  └─ Redirige → /login
```

### 10.6. Recuperación de contraseña

```
GET /forgot-password
  └─ Muestra formulario de email

POST /forgot-password
  ├─ Valida email (required, email)
  └─ Password::sendResetLink() → enlaces expiran en 60 min

GET /reset-password/{token}
  └─ Muestra formulario (token, email, password, password_confirmation)

POST /reset-password
  ├─ Valida token, email, password (confirmed, min:8)
  └─ Password::reset() → redirige a /login si éxito
```

## 11. Control de Acceso — Sprint 1 Etapa 2

### 11.1. Rutas protegidas

| URI | Middleware | Acceso |
|-----|-----------|--------|
| `/dashboard` | `auth`, `permission:access_dashboard` | Usuarios con permiso |
| `/empleados` | `auth`, `role_or_permission:Administrador\|RRHH\|view_employees` | Admin, RRHH o permiso |
| `/areas` | `auth`, `role_or_permission:Administrador\|RRHH\|manage_areas` | Admin, RRHH o permiso |
| `/seguridad` | `auth`, `role_or_permission:Administrador\|assign_roles` | Admin o permiso |

Acceso denegado → `resources/views/errors/403.blade.php`

### 11.2. Middleware Spatie en `bootstrap/app.php`

```php
$middleware->alias([
    'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
]);
```

### 11.3. Tests implementados

Ejecutar:
```bash
# Todos
docker compose exec app php artisan test

# Solo Auth/RBAC
docker compose exec app php artisan test --filter "Auth|Rbac"

# Verbose
docker compose exec app php artisan test --verbose
```

**Casos cubiertos:**
- `tests/Feature/Auth/LoginTest.php` — 9 tests (login exitoso, credenciales incorrectas, usuario inactivo, sin rol, RateLimiter, auditoría)
- `tests/Feature/Auth/LogoutTest.php` — 5 tests (logout, sesión invalidada, auditoría)
- `tests/Feature/Auth/PasswordResetTest.php` — 7 tests (recuperación, reset, validaciones)
- `tests/Feature/Rbac/RouteAccessTest.php` — 12 tests (acceso a rutas protegidas, 403 para usuarios sin permisos)

## 12. Que quedo listo — Sprint 1

✅ **Base técnica:** Docker, Laravel 13, MySQL 8.4, Bootstrap 5, Vite  
✅ **Base de datos:** Migraciones, seeders, relaciones (User ↔ Empleado ↔ Area)  
✅ **Modelos:** User, Area, Empleado con LogsActivity de Spatie  
✅ **Autenticación:** Login, logout, recuperación de contraseña con RateLimiter  
✅ **Validaciones:** Email/password en LoginRequest  
✅ **RBAC:** 5 roles (Admin, RRHH, Supervisor, Jefe Ops, Empleado) con 8 permisos  
✅ **Auditoría:** 6 eventos de auth registrados en activity_log  
✅ **Dashboard:** Protegido con permiso, muestra user/rol  
✅ **Layout autenticado:** Navbar con user info, logout, flash messages  
✅ **Página 403:** Custom error page para acceso denegado  
✅ **Tests:** 33 tests feature cubriendo Auth, logout, password reset y RBAC  

## 13. Que falta desarrollar

- CRUDs completos (empleados, areas, usuarios/roles)
- Lógica de asistencia, horarios y turnos
- Reportes y exportaciones (Excel/PDF)
- Integraciones con QR, RFID, DNI o biometría
- API REST
- Módulos de justificaciones

## 14. Próximos pasos

1. **Sprint 1 — Bloque 4 (TODO):** CRUD de empleados, áreas y gestión de roles
2. **Sprint 1 — Bloque 5 (TODO):** Validaciones avanzadas, soft delete, archivos
3. **Sprint 2:** Marcaciones (QR/DNI), turnos y horarios
4. **Sprint 3:** Reportes, Excel/PDF, API REST
5. **Sprint 4:** Justificaciones, integraciones biométricas

