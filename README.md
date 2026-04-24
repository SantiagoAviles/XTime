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

Migraciones activas:

- `users`
- `roles`, `permissions` y tablas pivote (Spatie)
- `activity_log`

Migraciones de negocio pendientes de activar:

- `empleados`, `horarios`, `turnos`, `empleado_horario`, `asistencias`

## 10. Que quedo listo

- Proyecto Laravel base con Docker completo.
- MySQL dockerizado, `.env` y `.env.example` listos.
- Bootstrap 5 integrado, layout Blade con navbar y sidebar.
- Paquetes de Excel, PDF, permisos y auditoria instalados.
- Migraciones base creadas y probadas.
- Rutas web y API separadas.

## 11. Que falta desarrollar

- Logica de asistencia, horarios y turnos.
- CRUDs por modulo.
- Vistas funcionales.
- Reportes y exportaciones.
- Integraciones con QR, RFID, DNI o biometria.
- API REST.
- Seeders de negocio.

## 12. Proximos pasos

1. Autenticacion y roles de acceso.
2. Seeders de roles (Administrador, Supervisor, JefeOperaciones, Trabajador).
3. CRUD de empleados.
4. Flujo de marcacion y regularizacion.
5. Reportes y exportaciones Excel/PDF.

