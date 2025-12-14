# دليل النشر المجاني - PEMS

## 🚀 النشر على Railway (الأسرع والأسهل)

### الخطوات:

#### 1. إنشاء حساب
- اذهب إلى [railway.app](https://railway.app)
- سجل دخول بـ GitHub

#### 2. رفع الكود إلى GitHub
```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/username/pems.git
git push -u origin main
```

#### 3. النشر على Railway
- اضغط "New Project"
- اختر "Deploy from GitHub repo"
- اختر مستودع pems
- Railway سيكتشف Laravel تلقائياً

#### 4. إضافة متغيرات البيئة
في لوحة Railway، اذهب إلى Variables وأضف:
```
APP_NAME=نظام إدارة مصروفات الإنتاج الفني
APP_ENV=production
APP_KEY=base64:oa1iAO5nLuw10KKYMwHvqZc2WNS3TUa0GPTgoEbYXXw=
APP_DEBUG=false
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-2.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.yhaonlrrndhimhrrmgsd
DB_PASSWORD=p7wM8FnnNuOZcURM
DB_SSLMODE=require
```

#### 5. تشغيل Migration
في Railway Terminal:
```bash
php artisan migrate --seed
```

---

## 🔄 البدائل الأخرى:

### Render.com
- نفس الخطوات تقريباً
- يحتاج ملف `render.yaml`

### Fly.io
- يحتاج `fly.toml`
- أكثر تعقيداً قليلاً

---

## ✅ بعد النشر:

1. **اختبر الموقع:** تأكد من عمل جميع الوظائف
2. **أرسل الرابط للعميل:** `https://your-app-name.up.railway.app`
3. **معلومات الدخول:**
   - البريد: admin@pems.com
   - كلمة المرور: admin123

---

## 🔧 استكشاف الأخطاء:

### إذا فشل النشر:
1. تحقق من logs في Railway
2. تأكد من صحة متغيرات البيئة
3. تأكد من اتصال Supabase

### إذا لم تعمل قاعدة البيانات:
1. تحقق من بيانات Supabase
2. شغل `php artisan migrate --seed` يدوياً

---

## 💡 نصائح:

- **Railway** الأسهل والأسرع
- **Render** أكثر استقراراً
- **Fly.io** أكثر مرونة
- جميعها تدعم Supabase بشكل مثالي

**الوقت المتوقع للنشر: 10-15 دقيقة**