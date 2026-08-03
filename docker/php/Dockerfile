# --- Étape 1 : Composer (juste pour copier le binaire) ---
FROM composer:2 AS composer

# --- Étape 2 : image PHP finale ---
FROM php:8.4-fpm-bookworm

# Dépendances système nécessaires à la compilation des extensions PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP natives
RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    opcache

# Extensions PECL (pas de compilation native disponible)
RUN pecl install redis apcu \
    && docker-php-ext-enable redis apcu

# Composer disponible dans l'image
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
