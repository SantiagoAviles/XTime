FROM php:8.4-fpm

# Argumentos de build
ARG user=altermec
ARG uid=1000

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP necesarias para Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Usuario no-root
RUN useradd -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www

# Crear directorios con permisos correctos ANTES de cambiar de usuario
RUN mkdir -p /var/www/vendor /var/www/node_modules /var/www/storage /var/www/bootstrap/cache \
    && chown -R $user:$user /var/www

EXPOSE 8000

# Correr como usuario no-root
USER $user

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
