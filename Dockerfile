FROM php:8.2-apache

# تثبيت الحزم المطلوبة
RUN apt-get update && apt-get install -y \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# تثبيت ملحقات PHP اللازمة
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع إلى السيرفر
COPY . /var/www/html

# تثبيت حزم لارافيل وتوليد الـ autoload
RUN composer install --no-dev --optimize-autoloader

# ضبط الصلاحيات للمجلدات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# إنشاء ملف قاعدة بيانات sqlite فارغ وضبط صلاحياته
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/database

# إنشاء ملف البيئة وتوليد المفتاح
RUN cp .env.example .env || true
RUN php artisan key:generate

# تشغيل الـ Migrations لإنشاء الجداول في قاعدة البيانات
RUN php artisan migrate --force

# تعديل مسار أباتشي ليشير إلى مجلد public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite