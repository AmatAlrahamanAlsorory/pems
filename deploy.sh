#!/bin/bash

echo "🚀 بدء عملية النشر لنظام PEMS..."

# تثبيت التبعيات
echo "📦 تثبيت التبعيات..."
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build

# إنشاء مفتاح التطبيق إذا لم يكن موجوداً
if [ ! -f .env ]; then
    echo "📝 إنشاء ملف .env..."
    cp .env.example .env
fi

echo "🔑 إنشاء مفتاح التطبيق..."
php artisan key:generate --force

# تحسين التطبيق للإنتاج
echo "⚡ تحسين التطبيق..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تشغيل الهجرات
echo "🗄️ تشغيل هجرات قاعدة البيانات..."
php artisan migrate --force

# تشغيل البذور
echo "🌱 إضافة البيانات الأساسية..."
php artisan db:seed --force --class=AdminWithTestDataSeeder

# إنشاء رابط التخزين
echo "🔗 إنشاء رابط التخزين..."
php artisan storage:link

echo "✅ تم النشر بنجاح!"
echo "🌐 يمكنك الآن الوصول للموقع"
echo "👤 حساب المدير: admin@pems.com / admin123"