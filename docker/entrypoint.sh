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

echo "[entrypoint] Ajustando permisos..."
chmod -R 775 storage bootstrap/cache

echo "[entrypoint] Iniciando servidor Laravel en 0.0.0.0:8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
