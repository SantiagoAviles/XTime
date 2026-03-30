# Altermec Asistencia

Base empresarial en Laravel para un sistema web de control de asistencia, gestion de horarios, portal de autogestion y futuras integraciones con QR, DNI, RFID o biometria.

## 1. Resumen de lo que hace esta base

- Crea un proyecto Laravel 13 listo para desarrollo local.
- Usa MySQL en Docker con la base `db_asistencia_altermec`.
- Integra Bootstrap 5 con Blade y Vite.
- Instala soporte para Excel, PDF, roles/permisos y auditoria.
- Deja una estructura modular preparada para crecer sin programar aun la logica del negocio.

## 2. Paso 1. Crear el proyecto Laravel

Nombre profesional sugerido:

- `altermec/control-asistencia`

Comando recomendado:

```bash
composer create-project laravel/laravel nombre-del-proyecto
```

Por que Composer directamente:

- Es la opcion mas simple y estable.
- Evita depender del instalador global de Laravel.
- Es mas facil de documentar para un entorno empresarial y CI/CD.

## 3. Paso 2. Configuracion inicial

Archivo `.env` preparado con:

- `APP_NAME="Altermec Asistencia"`
- `APP_URL=http://localhost:8000`
- `APP_TIMEZONE=America/Lima`
- `APP_LOCALE=es`
- `DB_CONNECTION=mysql`
- `DB_DATABASE=db_asistencia_altermec`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3307`

Tambien se incluye `.env.example` con la misma estructura base para clonar el proyecto rapidamente.

## 4. Paso 3. Docker para MySQL

Archivo creado:

- `compose.yaml`

Levantar solo la base de datos:

```bash
docker compose up -d mysql
```

Detener el contenedor:

```bash
docker compose down
```

Por que esta opcion:

- Mantiene simple el entorno.
- Dockeriza solo la BD, que era el requerimiento principal.
- Permite usar PHP, Composer y Node desde la maquina local sin agregar complejidad innecesaria.

## 5. Paso 4. Instalar dependencias

Dependencias PHP instaladas:

```bash
composer require maatwebsite/excel barryvdh/laravel-dompdf spatie/laravel-permission spatie/laravel-activitylog
```

Por que se instalaron:

- `maatwebsite/excel`: base para exportaciones e importaciones Excel.
- `barryvdh/laravel-dompdf`: generacion de PDF desde Blade o HTML.
- `spatie/laravel-permission`: manejo profesional de roles y permisos.
- `spatie/laravel-activitylog`: auditoria y trazabilidad de cambios.

Dependencias frontend instaladas:

```bash
npm install
```

Bootstrap 5 se integra por Vite y Blade con `bootstrap` y `@popperjs/core`.

## 6. Paso 5. Estructura del proyecto

Estructura sugerida y aplicada:

```text
app/
├── Domain/
│   ├── Asistencias/
│   │   ├── Actions/
│   │   ├── Models/
│   │   └── Services/
│   ├── Auditoria/
│   ├── Autogestion/
│   ├── Empleados/
│   ├── Horarios/
│   ├── Integraciones/
│   │   ├── Actions/
│   │   ├── Clients/
│   │   ├── Models/
│   │   └── Services/
│   ├── Reportes/
│   │   ├── Actions/
│   │   ├── Exports/
│   │   ├── Models/
│   │   └── Services/
│   ├── Turnos/
│   └── Usuarios/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   └── Web/
│   └── Requests/
└── Models/
    └── User.php

resources/views/
├── dashboard/
├── layouts/
│   └── partials/
├── asistencias/
├── horarios/
├── turnos/
├── empleados/
├── reportes/
├── autogestion/
├── usuarios/
├── auditoria/
└── integraciones/

routes/
├── api/
├── web/
├── api.php
└── web.php
```

Convencion propuesta:

- Controllers web: `app/Http/Controllers/Web/...`
- Controllers API: `app/Http/Controllers/Api/...`
- Requests: `app/Http/Requests/...`
- Models de negocio: `app/Domain/<Modulo>/Models`
- Services: `app/Domain/<Modulo>/Services`
- Actions reutilizables: `app/Domain/<Modulo>/Actions`
- Exports Excel: `app/Domain/Reportes/Exports`
- Integraciones externas: `app/Domain/Integraciones/Clients`

## 7. Paso 6. Estructura visual base

Se dejo preparado:

- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/partials/navbar.blade.php`
- `resources/views/layouts/partials/sidebar.blade.php`
- `resources/views/dashboard/index.blade.php`
- `resources/css/app.css`
- `resources/js/app.js`

Esto deja:

- layout principal Blade
- navbar simple
- sidebar simple
- dashboard inicial de validacion
- Bootstrap 5 como base visual

## 8. Paso 7. Base de datos inicial

Migraciones base actualmente activas:

- `users`
- `roles`, `permissions` y tablas pivote por Spatie
- `activity_log` para auditoria

Migraciones de negocio revertidas por el momento:

- `empleados`
- `horarios`
- `turnos`
- `empleado_horario`
- `asistencias`

## 9. Paso 8. Levantar el proyecto

Orden recomendado de comandos:

```bash
cp .env.example .env
composer install
php artisan key:generate
docker compose up -d mysql
npm install
php artisan migrate
npm run build
php artisan serve
```

Explicacion breve:

- `cp .env.example .env`: crea el archivo local de entorno.
- `composer install`: instala dependencias PHP.
- `php artisan key:generate`: genera la clave de la app.
- `docker compose up -d mysql`: levanta MySQL en Docker.
- `npm install`: instala dependencias frontend.
- `php artisan migrate`: crea las tablas base.
- `npm run build`: compila assets para produccion local.
- `php artisan serve`: inicia el servidor Laravel.

Tambien quedaron scripts utiles:

```bash
composer setup
composer setup:db
composer build
```

## 10. Paquetes instalados

PHP:

- `laravel/framework`
- `maatwebsite/excel`
- `barryvdh/laravel-dompdf`
- `spatie/laravel-permission`
- `spatie/laravel-activitylog`

Frontend:

- `bootstrap`
- `@popperjs/core`
- `vite`
- `axios`

## 11. Buenas practicas recomendadas

- Mantener la logica de negocio fuera de los controllers.
- Usar `Requests` para validacion de entrada.
- Usar `Actions` para casos de uso puntuales.
- Usar `Services` para coordinacion de procesos del dominio.
- Centralizar exportaciones en `Reportes/Exports`.
- Separar claramente rutas web y API desde el inicio.
- No mezclar vistas finales con prototipos en esta etapa.
- Mantener migraciones pequenas, expresivas y reversibles.

## 12. Que quedo listo

- Proyecto Laravel base creado.
- Docker MySQL configurado.
- `.env` y `.env.example` listos.
- Bootstrap 5 integrado.
- Layout principal Blade listo.
- Estructura modular inicial aplicada.
- Paquetes de Excel, PDF, permisos y auditoria instalados.
- Migraciones base creadas.
- Rutas web y API separadas y organizadas.

## 13. Que NO se ha desarrollado todavia

- Logica de asistencia.
- Reglas de horarios o turnos.
- CRUDs completos.
- Vistas funcionales por modulo.
- Reportes finales.
- Integraciones con QR, RFID, DNI o biometria.
- API REST funcional.
- Seeders de negocio.

## 14. Proximos pasos recomendados

1. Definir autenticacion y estrategia de acceso.
2. Modelar catalogos base y seeders de roles.
3. Construir CRUDs administrativos por modulo.
4. Definir flujo real de marcacion y regularizacion.
5. Diseñar contratos de integracion para QR, RFID y API.
