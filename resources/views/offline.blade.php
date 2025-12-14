<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>العمل أوفلاين - PEMS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .offline-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        .offline-icon {
            font-size: 80px;
            color: #ff6b6b;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .offline-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        .offline-features {
            margin-top: 30px;
            text-align: right;
        }
        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .feature-icon {
            margin-left: 10px;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">📡</div>
        <h1>العمل أوفلاين</h1>
        <p>لا يوجد اتصال بالإنترنت حالياً، لكن يمكنك الاستمرار في العمل. سيتم حفظ بياناتك محلياً ومزامنتها عند عودة الاتصال.</p>
        
        <div class="offline-actions">
            <button class="btn btn-primary" onclick="location.reload()">إعادة المحاولة</button>
            <a href="/dashboard" class="btn btn-secondary">العودة للوحة التحكم</a>
        </div>
        
        <div class="offline-features">
            <h3>الميزات المتاحة أوفلاين:</h3>
            
            <div class="feature">
                <span class="feature-icon">✅</span>
                <span>تسجيل المصروفات (سيتم رفعها لاحقاً)</span>
            </div>
            
            <div class="feature">
                <span class="feature-icon">✅</span>
                <span>عرض البيانات المحفوظة مسبقاً</span>
            </div>
            
            <div class="feature">
                <span class="feature-icon">✅</span>
                <span>تصفح الفئات والبنود</span>
            </div>
            
            <div class="feature">
                <span class="feature-icon">✅</span>
                <span>عرض العهد النشطة</span>
            </div>
        </div>
    </div>

    <script>
        // فحص الاتصال كل 5 ثوانِ
        setInterval(() => {
            if (navigator.onLine) {
                location.reload();
            }
        }, 5000);
    </script>
</body>
</html>