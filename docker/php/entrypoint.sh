#!/bin/sh
set -e

# 1. Setup .env
if [ ! -f .env ]; then
    cp .envproduction .env
    echo "Environment file created."
fi

# 2. Install dependencies jika vendor belum ada
if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

# 3. Pastikan folder framework ada
# mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs bootstrap/cache
# chown -R www-data:www-data storage bootstrap/cache
mkdir -p storage/framework/{views,cache,sessions} storage/logs bootstrap/cache
chown -R 82:82 storage bootstrap/cache

# 4. PENANGANAN STORAGE LINK (Solusi Error Anda)
# Hapus link lama yang mungkin corrupt atau bawaan dari Windows
rm -rf public/storage
# Buat link baru secara paksa
# php artisan storage:link --force

# 5. Generate Key
php artisan key:generate --force

# Jalankan perintah utama
exec "$@"