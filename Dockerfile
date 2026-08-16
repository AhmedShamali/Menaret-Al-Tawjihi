FROM php:8.2-apache

# تثبيت متطلبات النظام ودرايفر PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# تثبيت إضافات الـ PHP الضرورية لحل مشكلة الـ driver
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# تنظيف الكاش
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تفعيل خاصية الـ rewrite في أباتشي
RUN a2enmod rewrite

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ضبط مسار العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# تثبيت حزم لاراڤيل
RUN composer install --no-dev --optimize-autoloader

# ضبط مجلد public كواجهة أساسية لأباتشي وتفعيل AllowOverride
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "<Directory /var/www/html/public/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf

# ضبط الأذونات لمجلدات التخزين
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# إنشاء الرابط الرمزي للصور لتفعيل عرضها تلقائياً
RUN php artisan storage:link

# تجهيز ملف البيئة من النسخة الافتراضية
RUN cp .env.example .env

EXPOSE 80

# أوامر التشغيل الآمنة: توليد المفتاح، مسح الكاش، ثم إقلاع أباتشي
CMD sh -c "php artisan key:generate --no-interaction --force --ansi || true && php artisan config:clear && php artisan cache:clear && php artisan route:clear && apache2-foreground"