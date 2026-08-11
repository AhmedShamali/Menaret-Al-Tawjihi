FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 1. إنشاء ملف .env من .env.example إذا لم يكن موجوداً
RUN cp .env.example .env || touch .env

# 2. إنشاء ملف قاعدة بيانات sqlite وصلاحياتها الكاملة
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 777 /var/www/html/database \
    && chmod 777 /var/www/html/database/database.sqlite

# 3. ضبط إعدادات الاتصال بـ SQLite داخل ملف .env
RUN echo "APP_NAME=Laravel" >> .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_DEBUG=false" >> .env \
    && echo "DB_CONNECTION=sqlite" >> .env \
    && echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env

# 4. توليد مفتاح التطبيق
RUN php artisan key:generate

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

# 5. مسح الكاش، تطبيق الميجريشنز، ثم تشغيل أباتشي
CMD php artisan config:clear && php artisan cache:clear && php artisan migrate --force && apache2-foreground
