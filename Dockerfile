# ============================================================
# CultureRaya — produksi (Laravel 13 + PHP-FPM + nginx)
# ============================================================
# Build:
#     docker build -t cultureraya .
#
# Jalankan (dengan SQLite, paling sederhana):
#     docker compose up -d
#
# Baca .dockerignore agar tahu file apa saja yang tidak dikirim.

# ------------------------------------------------------------
# 1. FRONTEND — build aset CSS/JS dengan Node
# ------------------------------------------------------------
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json .npmrc ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js ./
# Tailwind v4 memindai file ini (dideklarasikan di app.css).
# Dibuat kosong agar build tidak gagal bila file aslinya tidak ikut.
RUN mkdir -p storage/framework/views vendor/laravel/framework/src/Illuminate/Pagination/resources/views

COPY public ./public
RUN npm run build

# ------------------------------------------------------------
# 2. DEPENDENSI PHP — composer install (tanpa paket dev)
#    PHP 8.5 dipakai agar sama dengan `composer.lock` proyek.
# ------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --no-scripts \
        --no-autoloader

COPY app app
COPY bootstrap/app.php bootstrap/app.php
COPY bootstrap/providers.php bootstrap/providers.php
COPY config config
COPY database database
COPY public public
COPY routes routes
COPY artisan ./

# Folder cache bootstrap harus ada & writable sebelum package:discover
RUN mkdir -p bootstrap/cache && chmod ug+rwX bootstrap/cache

RUN composer dump-autoload --optimize --classmap-authoritative

# ------------------------------------------------------------
# 3. RUNTIME — PHP-FPM + nginx (satu kontainer, diatur supervisord)
# ------------------------------------------------------------
FROM php:8.5-fpm-alpine AS app

# Label
LABEL org.opencontainers.image.title="CultureRaya"
LABEL org.opencontainers.image.description="Jelajahi Budaya Nusantara"

# Dependensi sistem untuk ekstensi PHP & nginx
RUN apk add --no-cache \
        nginx \
        supervisor \
        icu-libs \
        libzip \
        libpng \
        libjpeg-turbo \
        freetype \
        oniguruma \
        sqlite-libs \
        git \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        sqlite-dev \
        bzip2-dev

# Ekstensi yang AMAN dibangun sebagai modul shared
RUN docker-php-ext-install pdo_mysql gd intl bcmath zip

# pcntl — dibangun terpisah (dibutuhkan queue worker)
RUN docker-php-ext-install pcntl \
    && docker-php-ext-enable pcntl

# opcache & mbstring SUDAH termasuk bawaan image resmi PHP 8.5 —
# tidak perlu di-install ulang (malah bikin build gagal karena tidak
# menghasilkan modul). Konfigurasi opcache tetap bisa lewat
# docker/opcache.ini.

# Konfigurasi PHP, PHP-FPM, opcache, nginx, supervisor
COPY docker/php.ini "$PHP_INI_DIR/conf.d/99-cultureraya.ini"
COPY docker/opcache.ini "$PHP_INI_DIR/conf.d/99-opcache.ini"
COPY docker/fpm.conf /usr/local/etc/php-fpm.d/zz-cultureraya.conf
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Composer autoload + kode aplikasi
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY --from=vendor /app/composer.lock ./composer.lock
COPY --chown=www-data:www-data . /var/www/html

# Aset frontend hasil build (menimpa public bawaan)
RUN rm -rf public/build
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

# ---------- Izin ----------
RUN mkdir -p storage/framework/{cache/data,sessions,testing,views} \
              storage/logs storage/app/public bootstrap/cache database \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R ug+rwX storage bootstrap/cache database \
    && rm -f database/database.sqlite \
       && rm -f bootstrap/cache/*.php

ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_URL=http://localhost:8000 \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite \
    SESSION_DRIVER=database \
    CACHE_STORE=database \
    QUEUE_CONNECTION=database

EXPOSE 80

HEALTHCHECK --interval=15s --timeout=5s --start-period=40s --retries=3 \
    CMD php -r 'exit((int) !@file_get_contents("http://127.0.0.1/up"));'

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["supervisord", "-n", "-c", "/etc/supervisord.conf"]
