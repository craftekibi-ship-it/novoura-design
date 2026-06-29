# Novoura Design — üretim (Coolify)
FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql mbstring gd zip bcmath exif \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
            storage/app/public bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# SQLite kalıcı dizini hazırla (mount: /app/database-data). Boş dosya = geçerli boş DB.
# migrate her açılışta (idempotent); ilk açılışta (boş DB) otomatik seed; sonra serve
CMD mkdir -p /app/database-data \
 && touch /app/database-data/database.sqlite \
 && php artisan migrate --force \
 && php artisan storage:link 2>/dev/null || true; \
 php artisan tinker --execute="if(\App\Models\User::count()===0){\Illuminate\Support\Facades\Artisan::call('db:seed',['--force'=>true]);echo 'seeded';}" 2>/dev/null || true; \
 php artisan config:cache 2>/dev/null || true; \
 php artisan serve --host=0.0.0.0 --port=8000
