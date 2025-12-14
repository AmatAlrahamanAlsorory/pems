<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-blue-900">الملف الشخصي</h2>
                <p class="text-gray-600 mt-1">إدارة معلومات حسابك الشخصي</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- معلومات المستخدم -->
                <div class="lg:col-span-2">
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="text-xl font-bold text-gray-900">معلومات الحساب</h3>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-xl font-bold text-gray-900">تغيير كلمة المرور</h3>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <!-- الشريط الجانبي -->
                <div>
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="text-xl font-bold text-gray-900">معلومات المستخدم</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h4>
                                <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                    {{ Auth::user()->role_name }}
                                </span>
                            </div>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">تاريخ التسجيل:</span>
                                    <span class="font-medium">{{ Auth::user()->created_at->format('Y/m/d') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">آخر تسجيل دخول:</span>
                                    <span class="font-medium">{{ Auth::user()->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-red-600">
                            <h3 class="text-xl font-bold text-gray-900">منطقة الخطر</h3>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
