# دليل نشر PEMS على Railway

## الخطوات:

### 1. تحضير المشروع
```bash
composer install --no-dev --optimize-autoloader
npm run build
```

### 2. إنشاء حساب Railway
- اذهب إلى: https://railway.app
- سجل دخول بـ GitHub
- اربط مستودع المشروع

### 3. إعداد قاعدة البيانات
- أضف PostgreSQL من Railway Dashboard
- انسخ متغيرات قاعدة البيانات

### 4. ضبط متغيرات البيئة
انسخ من `.env.railway` وأضف:
```
APP_KEY=base64:your-generated-key
APP_URL=https://your-app-name.railway.app
```

### 5. تشغيل المايجريشن
```bash
php artisan migrate --seed --force
```

## الرابط النهائي:
`https://your-pems.railway.app`

## بيانات الدخول التجريبية:
- **البريد:** admin@pems.com  
- **كلمة المرور:** admin123

## ملاحظات:
- الخطة المجانية: 500MB قاعدة بيانات
- مدة التجربة: شهر واحد مجاني
- يمكن ترقية الخطة لاحقاً