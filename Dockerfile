FROM php:8.2-apache

# تثبيت متطلبات النظام والأدوات الأساسية
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# تنظيف الكاش الخاص بنظام التشغيل
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تفعيل خاصية الـ rewrite في أباتشي
RUN a2enmod rewrite

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ضبط مسار العمل داخل السيرفر
WORKDIR /var/www/html

# نسخ كل ملفات مشروعك
COPY . .

# تثبيت حزم لاراڤيل
RUN composer install --no-dev --optimize-autoloader

# ضبط مجلد public كواجهة أساسية لأباتشي وتفعيل AllowOverride لملفات .htaccess
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "<Directory /var/www/html/public/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf

# تهيئة قاعدة البيانات والأذونات الكاملة لمجلدات التخزين وقاعدة البيانات
RUN mkdir -p /var/www/html/database && \
    touch /var/www/html/database/database.sqlite

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# تجهيز ملف البيئة من النسخة الافتراضية
RUN cp .env.example .env

EXPOSE 80

# أوامر التشغيل: توليد المفتاح، الهجرات، السيدرز، مسح الكاش، ثم إقلاع أباتشي
CMD sh -c "php artisan key:generate --no-interaction --force && php artisan migrate --force && php artisan config:clear && php artisan cache:clear && php artisan route:clear && apache2-foreground"
