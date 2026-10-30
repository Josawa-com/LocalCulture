#!/bin/sh
set -e

cd /var/www/html

ROLE="${CONTAINER_ROLE:-app}"

# ------------------------------------------------------------
# Persiapan yang berlaku untuk semua role
# ------------------------------------------------------------

# Database SQLite dibuat bila belum ada (dipakai secara default).
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] && [ ! -f "${DB_DATABASE:-database/database.sqlite}" ]; then
    echo "Membuat database SQLite di ${DB_DATABASE:-database/database.sqlite}..."
    mkdir -p "$(dirname "${DB_DATABASE:-database/database.sqlite}")"
    touch "${DB_DATABASE:-database/database.sqlite}"
    chown www-data:www-data "${DB_DATABASE:-database/database.sqlite}"
fi

# ------------------------------------------------------------
# APP_KEY: dibuat sekali dan disimpan permanen di volume
# storage/app (supaya data terenkripsi tidak rusak saat kontainer
# di-restart atau di-rebuild).
# ------------------------------------------------------------
if [ -z "${APP_KEY:-}" ]; then
    KEY_FILE="storage/app/app_key"

    if [ -f "$KEY_FILE" ]; then
        echo "APP_KEY ditemukan di $KEY_FILE"
        export APP_KEY="$(cat "$KEY_FILE")"
    else
        echo "APP_KEY belum ada -> membuat baru..."
        export APP_KEY="$(php artisan key:generate --show --no-interaction)"
        echo "$APP_KEY" > "$KEY_FILE"
        chown www-data:www-data "$KEY_FILE"
        chmod 600 "$KEY_FILE"
        echo "APP_KEY disimpan di $KEY_FILE"
    fi
fi

# ------------------------------------------------------------
# Package manifest dibangun ulang dari paket yang ADA di produksi.
# Cache bootstrap dari mesin lokal tidak dikirim, tapi dibuat ulang di
# sini agar aman bila ada yang tersisa.
# ------------------------------------------------------------
echo "Menemukan paket (package:discover)..."
php artisan package:discover --no-interaction

# Symlink storage/app/public ke public/storage (untuk avatar).
if [ ! -L public/storage ] && [ ! -e public/storage ]; then
    echo "Membuat tautan storage..."
    php artisan storage:link --no-interaction >/dev/null
fi

# Izin tulis untuk direktori yang berubah saat runtime.
# Dibuat ulang di sini karena volume bisa menutupi folder bawaan image.
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         storage/app/public \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# ------------------------------------------------------------
# Migrasi (default: jalan; matikan dengan DB_MIGRATE=false)
# ------------------------------------------------------------
if [ "${DB_MIGRATE:-true}" = "true" ]; then
    echo "Menjalankan migrasi..."
    php artisan migrate --force --no-interaction
fi

# ------------------------------------------------------------
# Optimisasi produksi (matikan dengan APP_OPTIMIZE=false)
# ------------------------------------------------------------
if [ "${APP_OPTIMIZE:-true}" = "true" ] && [ "${APP_ENV:-production}" = "production" ]; then
    echo "Meng-cache konfigurasi, route, dan view..."
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan event:cache --no-interaction
fi

# ------------------------------------------------------------
# Peran kontainer
# ------------------------------------------------------------
case "$ROLE" in
    app|web)
        echo "Menjalankan nginx + PHP-FPM..."
        exec supervisord -n -c /etc/supervisord.conf
        ;;
    queue)
        echo "Menjalankan queue worker..."
        # Supervisord memakai autostart=false, jadi queue dijalankan langsung.
        exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600
        ;;
    scheduler)
        echo "Menjalankan scheduler..."
        while true; do
            php artisan schedule:run --no-interaction >/dev/null 2>&1
            sleep 60
        done
        ;;
    *)
        echo "CONTAINER_ROLE tidak dikenal: $ROLE"
        echo "Pilihan: app (default), queue, scheduler"
        exit 1
        ;;
esac
