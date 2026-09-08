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

# تثبيت إضافات الـ PHP الضرورية (PostgreSQL و MySQL والدوال الأساسية)
RUN docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql mbstring exif pcntl bcmath gd

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

# تصحيح مسار الأباتشي الأساسي لمجلد public بشكل دقيق وصحيح
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -i -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN echo "<Directory /var/www/html/public/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf

# ضبط الأذونات لمجلدات التخزين والمرفوعات وسكريبت الإقلاع
RUN mkdir -p /var/www/html/public/uploads/logos && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads && \
    chmod +x /var/www/html/docker-entrypoint.sh

EXPOSE 80 10000

# تشغيل خادم الويب عبر سكريبت الإقلاع الذكي المتوافق مع Render و Railway
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]