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

# إنشاء ملف البيئة أولاً قبل توليد المفتاح
RUN cp .env.example .env || true

# توليد المفتاح بعد توفر ملف البيئة
RUN php artisan key:generate

# إنشاء قاعدة بيانات sqlite وصلاحياتها الكاملة
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 777 /var/www/html/database \
    && chmod 777 /var/www/html/database/database.sqlite

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

# مسح الكاش وتطبيق الميجريشن ثم تشغيل أباتشي
CMD php artisan config:clear && php artisan migrate --force && apache2-foreground
