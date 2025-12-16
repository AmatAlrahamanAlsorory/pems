<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PEMS') }} - نظام إدارة المصروفات</title>
        <meta name="description" content="نظام متكامل لإدارة ومراقبة مصروفات الإنتاج الفني">
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%233B82F6'%3E%3Cpath d='M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'/%3E%3C/svg%3E">
        
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/notifications.js') }}"></script>
        <script src="{{ asset('js/notifications-live.js') }}"></script>
        <script src="{{ asset('js/ai-assistant.js') }}"></script>
        <script src="{{ asset('js/responsive-charts.js') }}"></script>
        
        <!-- PWA Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#3b82f6">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="PEMS">
        
        <!-- PWA Icons -->
        <link rel="apple-touch-icon" href="/icon-192.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png">
        
        <script src="{{ asset('js/offline.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('css/mobile-fixes.css') }}">
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-pattern">
        <!-- Online Status Indicator -->
        <div id="online-status" class="fixed top-4 left-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50 hidden"></div>
        
        <div class="min-h-screen">
            @include('layouts.navigation')
            


            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-soft">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="alert alert-success animate-slide-up">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="alert alert-danger animate-slide-up">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="animate-fade-in">
                @yield('content', $slot ?? '')
            </main>
            

        </div>
        
        <!-- مؤشر حالة الاتصال -->
        <div id="connection-status" class="online">متصل</div>
        
        @stack('scripts')
    </body>
</html>
