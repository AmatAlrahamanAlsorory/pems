# نظام إدارة مصروفات الإنتاج الفني (PEMS)

## وصف المشروع
نظام شامل لإدارة مصروفات الإنتاج الفني مبني بـ Laravel مع واجهة عربية حديثة.

## المتطلبات
- PHP 8.1+
- Composer
- Node.js & NPM
- قاعدة بيانات (MySQL/PostgreSQL/SQLite)

## التثبيت
1. `composer install`
2. `npm install`
3. `cp .env.example .env`
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `npm run build`

## تشغيل المشروع
```bash
php artisan serve
```

## الميزات الرئيسية
- إدارة المشاريع والمصروفات
- نظام صلاحيات متقدم
- تقارير مالية شاملة
- واجهة عربية متجاوبة
- تصدير البيانات (PDF/Excel)

## حساب المدير
- **البريد الإلكتروني:** admin@pems.com
- **كلمة المرور:** admin123
- **الصلاحيات:** مدير مالي (كامل الصلاحيات)