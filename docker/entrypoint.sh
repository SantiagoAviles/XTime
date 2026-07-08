#!/bin/bash
set -e

echo "[entrypoint] Verificando .env..."
if [ ! -f .env ]; then
    echo "[entrypoint] No existe .env, copiando desde .env.example..."
    cp .env.example .env
fi

echo "[entrypoint] Instalando dependencias PHP..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "[entrypoint] Instalando dependencias Node..."
npm install

echo "[entrypoint] Compilando assets..."
npm run build

echo "[entrypoint] Verificando APP_KEY..."
if grep -qE "^APP_KEY=$|^APP_KEY=\"\"$" .env; then
    echo "[entrypoint] Generando APP_KEY..."
    php artisan key:generate --force
else
    echo "[entrypoint] APP_KEY ya configurado, omitiendo."
fi

echo "[entrypoint] Esperando a que Laravel pueda conectarse a la base de datos..."
tries=0
until php artisan db:show >/dev/null 2>&1; do
    tries=$((tries + 1))
    if [ "$tries" -ge 30 ]; then
        echo "[entrypoint] ERROR: Laravel no pudo conectar a la base de datos tras 30 intentos (60s). Detalle:"
        php artisan db:show || true
        exit 1
    fi
    echo "[entrypoint] BD no disponible aún (intento ${tries}/30), reintentando en 2s..."
    sleep 2
done
echo "[entrypoint] Base de datos disponible."

echo "[entrypoint] Ejecutando migraciones..."
php artisan migrate --force

echo "[entrypoint] Sembrando datos base (idempotente, no duplica si ya existen)..."
php artisan db:seed --force

echo "[entrypoint] Ajustando permisos (best-effort)..."
# En WSL+NTFS los archivos son root:root con 777; chmod fallaría con "Operation not
# permitted" y abortaría el entrypoint por culpa del 'set -e'. Como Laravel solo
# necesita poder escribir (y 777 ya lo permite), silenciamos el error.
chmod -R 775 storage bootstrap/cache 2>/dev/null || \
    echo "[entrypoint] chmod no aplicable (host NTFS o sin permisos) — continuando."

echo "[entrypoint] Iniciando servidor Laravel en 0.0.0.0:8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
