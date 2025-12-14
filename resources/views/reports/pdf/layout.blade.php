<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title', 'تقرير')</title>
    <style>
        * {
            font-family: 'Noto Sans Arabic', 'Tahoma', 'Arial Unicode MS', sans-serif;
            direction: rtl;
        }
        
        @font-face {
            font-family: 'Arabic';
            src: url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap');
        }
        
        body {
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #3B82F6;
            font-size: 24px;
            margin: 0;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        table th {
            background-color: #3B82F6;
            color: white;
            padding: 10px;
            text-align: right;
            font-weight: bold;
        }
        
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: right;
        }
        
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .total {
            font-weight: bold;
            background-color: #E5E7EB;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام إدارة المصروفات - PEMS</h1>
        <p>@yield('report-title', 'تقرير')</p>
        <p>التاريخ: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    @yield('content')

    <div class="footer">
        <p>تم الإنشاء بواسطة نظام PEMS - {{ now()->format('Y-m-d') }}</p>
    </div>
</body>
</html>
