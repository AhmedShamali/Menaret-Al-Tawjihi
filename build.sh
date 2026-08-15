#!/usr/bin/env bash
set -o errexit

# تحديث النظام وتثبيت درايفر الـ PostgreSQL للـ PHP
apt-get update && apt-get install -y php-pgsql

# تثبيت حزم المشروع
composer install --no-dev --optimize-autoloader

# مسح وتشغيل الكاش والـ Migrations
php artisan config:clear
php artisan cache:clear
php artisan migrate --force
