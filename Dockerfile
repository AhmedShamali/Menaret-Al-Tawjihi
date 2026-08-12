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

# تفعيل خاصية الـ rewrite في أباتشي (ضروري لروابط لاراڤيل)
RUN a2enmod rewrite

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ضبط مسار العمل داخل السيرفر
WORKDIR /var/www/html

# نسخ كل ملفات مشروعك من جهازك إلى السيرفر
COPY . .

# تثبيت حزم لاراڤيل المطلوبة (بدون حزم التطوير لتقليل الحجم)
RUN composer install --no-dev --optimize-autoloader

# ضبط مجلد public ليكون الواجهة الأساسية للموقع
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# إنشاء مجلد قاعدة البيانات SQLite إذا لم يكن موجوداً
RUN mkdir -p /var/www/html/database && \
    touch /var/www/html/database/database.sqlite

# منح صلاحيات الكتابة الكاملة لمجلدات التخزين وقاعدة البيانات والكاش
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 80

# أوامر التشغيل الآمنة (مسح الكاش القديم، توليد المفتاح إن لم يكن موجوداً، تنفيذ الهجرة، ثم تشغيل أباتشي)
CMD sh -c "php artisan key:generate --no-interaction --force && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan migrate --force && apache2-foreground"
