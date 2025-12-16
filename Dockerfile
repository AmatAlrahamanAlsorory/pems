# (1) Base Image: استخدم صورة PHP-FPM مع Alpine Linux
FROM php:8.2-fpm-alpine

# (2) تثبيت متطلبات النظام
RUN apk update && apk add \
    git \
    curl \
    libxml2-dev \
    libzip-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql zip

# (3) إعداد مجلد العمل ونسخ الكود
WORKDIR /var/www/html
COPY . .

# (4) تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# (5) تثبيت تبعيات PHP و NPM
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# (6) إعداد صلاحيات الملفات
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

# (7) أمر التشغيل
CMD php artisan serve --host 0.0.0.0 --port 8080