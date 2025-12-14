<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>نظام إدارة مصروفات الإنتاج الفني - PEMS</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body { font-family: 'Cairo', sans-serif; }
            </style>
        @endif
    </head>
    <body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full">
            <!-- Logo & Title -->
            <div class="text-center mb-10">
                <div class="mx-auto h-24 w-24 bg-blue-600 rounded-3xl flex items-center justify-center shadow-2xl mb-6">
                    <svg class="h-14 w-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-5xl font-bold text-blue-900 mb-3">PEMS</h1>
                <p class="text-blue-700 text-xl font-semibold">نظام إدارة المصروفات</p>
                <p class="text-blue-600 text-sm mt-1">للإنتاج الفني</p>
            </div>

            @auth
                <!-- Dashboard Button -->
                <div class="bg-white rounded-3xl p-8 shadow-2xl border border-blue-200">
                    <a href="{{ route('dashboard') }}" class="block w-full bg-blue-600 text-white py-4 px-6 rounded-2xl font-bold text-lg hover:bg-blue-700 transition-all duration-300 shadow-lg text-center">
                        الذهاب إلى لوحة التحكم
                    </a>
                </div>
            @else
                <!-- Login & Register Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Login Card -->
                    <a href="{{ route('login') }}" class="bg-white rounded-3xl p-8 shadow-2xl border border-blue-200 hover:shadow-3xl hover:scale-105 transition-all duration-300 group">
                        <div class="text-center">
                            <div class="mx-auto h-16 w-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 transition-colors duration-300">
                                <svg class="h-8 w-8 text-blue-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                            <h3 class="text-blue-900 font-bold text-lg mb-1">تسجيل الدخول</h3>
                            <p class="text-blue-600 text-sm">للمستخدمين الحاليين</p>
                        </div>
                    </a>

                    <!-- Register Card -->
                    <a href="{{ route('register') }}" class="bg-white rounded-3xl p-8 shadow-2xl border border-blue-200 hover:shadow-3xl hover:scale-105 transition-all duration-300 group">
                        <div class="text-center">
                            <div class="mx-auto h-16 w-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 transition-colors duration-300">
                                <svg class="h-8 w-8 text-blue-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <h3 class="text-blue-900 font-bold text-lg mb-1">حساب جديد</h3>
                            <p class="text-blue-600 text-sm">إنشاء حساب جديد</p>
                        </div>
                    </a>
                </div>
            @endauth
        </div>
    </body>
</html>
