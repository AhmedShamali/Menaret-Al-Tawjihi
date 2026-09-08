#!/bin/sh
set -e

# 1. ضبط منفذ أباتشي ليتوافق ديناميكياً مع منفذ Render ($PORT) أو الافتراضي 80
PORT_TO_USE="${PORT:-80}"
echo "==> Configuring Apache to listen on port ${PORT_TO_USE}..."
sed -i "s/Listen 80/Listen ${PORT_TO_USE}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf

# 2. إنشاء رابط التخزين الرمزي إذا لم يكن موجوداً
echo "==> Checking storage symlink..."
php artisan storage:link || true

# 3. تشغيل الـ Migrations بأمان في بيئة السيرفر
echo "==> Running database migrations..."
php artisan migrate --force

# 4. تشغيل خيار Seeding فقط إذا تم تفعيل المتغير RUN_SEEDER_ON_BOOT=true
if [ "$RUN_SEEDER_ON_BOOT" = "true" ]; then
    echo "==> Seeding database..."
    php artisan db:seed --force || true
fi

# 5. تنظيف وتحسين الكاش لبيئة الإنتاج
if [ "$APP_ENV" = "production" ]; then
    echo "==> Optimizing configuration and routes for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# 6. إطلاق خادم الويب Apache
echo "==> Starting Apache web server on port ${PORT_TO_USE}..."
exec apache2-foreground
