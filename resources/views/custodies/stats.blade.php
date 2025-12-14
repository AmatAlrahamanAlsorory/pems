<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">إحصائيات العهد والقواعد</h2>
                <p class="text-gray-600">نظرة شاملة على حالة العهد والقواعد المطبقة</p>
            </div>

            <!-- إحصائيات سريعة -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">إجمالي العهد</p>
                            <p class="text-2xl font-bold text-gray-900" id="total-custodies">-</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">العهد المفتوحة</p>
                            <p class="text-2xl font-bold" id="open-custodies-count">-</p>
                            <p class="text-xs text-gray-500">الحد الأقصى: 2</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">العهد المتأخرة</p>
                            <p class="text-2xl font-bold" id="overdue-custodies-count">-</p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">إجمالي المبلغ</p>
                            <p class="text-2xl font-bold text-gray-900" id="total-amount">-</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قواعد العهد -->
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">قواعد العهد المطبقة</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                            <div class="bg-blue-500 p-2 rounded-full">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">الحد الأقصى للعهد</div>
                                <div class="text-sm text-gray-600">عهدتان مفتوحتان كحد أقصى</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                            <div class="bg-green-500 p-2 rounded-full">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">نسبة التصفية</div>
                                <div class="text-sm text-gray-600">80% تصفية مطلوبة قبل عهدة جديدة</div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-lg">
                            <div class="bg-yellow-500 p-2 rounded-full">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">التصفية الأسبوعية</div>
                                <div class="text-sm text-gray-600">تصفية إجبارية كل 7 أيام</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg">
                            <div class="bg-red-500 p-2 rounded-full">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">العهد المتأخرة</div>
                                <div class="text-sm text-gray-600">تنبيهات تلقائية بعد 7 و 14 يوم</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- حالة المستخدم -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">حالة العهد الخاصة بك</h3>
                
                <div class="p-4 rounded-lg border-2" id="user-status">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="text-gray-600 mt-2">جاري التحميل...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // تحميل الإحصائيات
        fetch('/api/custody/stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-custodies').textContent = data.total_custodies;
                document.getElementById('open-custodies-count').textContent = data.open_custodies;
                document.getElementById('overdue-custodies-count').textContent = data.overdue_custodies;
                document.getElementById('total-amount').textContent = new Intl.NumberFormat('ar-SA').format(data.total_amount) + ' ر.س';
                
                // تحديث الألوان
                const openCount = document.getElementById('open-custodies-count');
                openCount.className = data.open_custodies >= 2 ? 'text-2xl font-bold text-red-600' : 'text-2xl font-bold text-green-600';
                
                const overdueCount = document.getElementById('overdue-custodies-count');
                overdueCount.className = data.overdue_custodies > 0 ? 'text-2xl font-bold text-red-600' : 'text-2xl font-bold text-green-600';
                
                // حالة المستخدم
                const userStatus = document.getElementById('user-status');
                if (data.can_request_new) {
                    userStatus.className = 'p-4 rounded-lg border-2 border-green-300 bg-green-50';
                    userStatus.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="bg-green-500 p-2 rounded-full">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-green-800">يمكنك طلب عهدة جديدة</div>
                                <div class="text-sm text-green-700">جميع الشروط مستوفاة</div>
                            </div>
                        </div>
                    `;
                } else {
                    userStatus.className = 'p-4 rounded-lg border-2 border-red-300 bg-red-50';
                    userStatus.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="bg-red-500 p-2 rounded-full">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-red-800">لا يمكن طلب عهدة جديدة</div>
                                <div class="text-sm text-red-700">يرجى تصفية العهد الحالية أولاً</div>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل الإحصائيات:', error);
            });
    </script>
</x-app-layout>