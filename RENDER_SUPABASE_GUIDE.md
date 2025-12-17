# 🚀 دليل النشر على Render مع Supabase

## 1️⃣ إعداد Supabase:
1. اذهب إلى: https://supabase.com
2. اضغط "New Project"
3. املأ البيانات:
   - Name: pems-database
   - Password: PemsDemo2024!
4. انتظر إنشاء القاعدة (2-3 دقائق)
5. اذهب إلى Settings > Database
6. انسخ بيانات الاتصال

## 2️⃣ النشر على Render:
1. اذهب إلى: https://render.com
2. سجل دخول بـ GitHub
3. اضغط "New +" > "Web Service"
4. اختر مستودع "pems"

## 3️⃣ إعدادات Render:
```
Name: pems
Language: Docker
Build Command: ./build.sh
Start Command: php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

## 4️⃣ متغيرات البيئة:
```
APP_NAME=PEMS - نظام إدارة مصروفات الإنتاج
APP_ENV=production
APP_KEY=base64:E4RxCe2GSycdoxi3mp6fUa5QF2SHNvAlFAb2+Hdoisk=
APP_DEBUG=false
APP_LOCALE=ar
DB_CONNECTION=pgsql
DB_HOST=[من Supabase]
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[من Supabase]
DEMO_MODE=true
SEED_DEMO_DATA=true
```

## 5️⃣ النشر:
اضغط "Create Web Service"

## 🎯 النتيجة:
- رابط: https://pems-xxxx.onrender.com
- بيانات الدخول: admin@pems.com / admin123
- قاعدة بيانات سحابية مع Supabase