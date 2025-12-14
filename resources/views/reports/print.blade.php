<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'تقرير PEMS')</title>
    <style>
        * {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            direction: rtl;
            text-align: right;
        }
        
        body {
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 20px;
        }
        
        .print-header h1 {
            color: #3B82F6;
            font-size: 28px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        
        .print-header .subtitle {
            color: #666;
            font-size: 16px;
            margin: 5px 0;
        }
        
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .print-table th {
            background: linear-gradient(135deg, #3B82F6, #1E40AF);
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        
        .print-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #E5E7EB;
            text-align: center;
            font-size: 13px;
        }
        
        .print-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        .print-table tr:hover {
            background-color: #EFF6FF;
        }
        
        .total-row {
            font-weight: bold;
            background: linear-gradient(135deg, #F3F4F6, #E5E7EB) !important;
            border-top: 2px solid #3B82F6;
        }
        
        .print-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #E5E7EB;
            padding-top: 15px;
        }
        
        .no-print {
            margin: 20px 0;
            text-align: center;
        }
        
        .print-btn {
            background: #3B82F6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 10px;
        }
        
        .print-btn:hover {
            background: #1E40AF;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                margin: 0;
                padding: 15px;
            }
            
            .print-table {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">🖨️ طباعة</button>
        <button onclick="window.close()" class="print-btn" style="background: #6B7280;">إغلاق</button>
    </div>

    <div class="print-header">
        <h1>نظام إدارة مصروفات الإنتاج الفني - PEMS</h1>
        <div class="subtitle">@yield('report-title', 'تقرير')</div>
        <div class="subtitle">تاريخ الإنشاء: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    @yield('content')

    <div class="print-footer">
        <p>تم إنشاء هذا التقرير بواسطة نظام PEMS - {{ now()->format('Y-m-d') }}</p>
    </div>

    <script>
        // طباعة تلقائية عند فتح الصفحة (اختياري)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>