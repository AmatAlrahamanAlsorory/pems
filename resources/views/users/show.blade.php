@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        تفاصيل المستخدم
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">معلومات المستخدم والصلاحيات</p>
                </div>
                <div class="flex gap-3">
                    @if(\App\Helpers\PermissionHelper::canEditUser(auth()->user()))
                        <a href="{{ route('users.edit', $user) }}" class="text-yellow-700 hover:text-yellow-900 text-sm font-bold border border-yellow-300 hover:border-yellow-500 px-4 py-2 rounded bg-yellow-50 hover:bg-yellow-100">
                            تعديل المستخدم
                        </a>
                    @endif
                    <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-gray-900 text-sm font-bold border border-gray-300 hover:border-gray-500 px-4 py-2 rounded bg-gray-50 hover:bg-gray-100">
                        رجوع للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-8">
                <div class="flex items-center mb-8">
                    <div class="flex-shrink-0 h-20 w-20 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="text-indigo-600 font-bold text-2xl">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div class="mr-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <div class="mt-2">
                            @php
                                $roleColors = [
                                    'financial_manager' => 'bg-purple-100 text-purple-800',
                                    'admin_accountant' => 'bg-blue-100 text-blue-800',
                                    'production_manager' => 'bg-green-100 text-green-800',
                                    'field_accountant' => 'bg-yellow-100 text-yellow-800',
                                    'financial_assistant' => 'bg-gray-100 text-gray-800'
                                ];
                                $roleNames = [
                                    'financial_manager' => 'مدير مالي',
                                    'admin_accountant' => 'محاسب إدارة',
                                    'production_manager' => 'مدير إنتاج',
                                    'field_accountant' => 'محاسب ميداني',
                                    'financial_assistant' => 'مساعد مالي'
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $roleNames[$user->role] ?? $user->role }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">الاسم الكامل</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">الموقع</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $user->location ?: 'غير محدد' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الحساب</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">الصلاحية</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $roleNames[$user->role] ?? $user->role }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">تاريخ الإنشاء</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">آخر تحديث</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">الصلاحيات المتاحة</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $permissions = [
                                'create_project' => 'إنشاء مشروع',
                                'edit_project' => 'تعديل مشروع',
                                'delete_project' => 'حذف مشروع',
                                'view_project' => 'عرض مشروع',
                                'create_custody' => 'إنشاء عهدة',
                                'edit_custody' => 'تعديل عهدة',
                                'delete_custody' => 'حذف عهدة',
                                'approve_custody' => 'موافقة عهدة',
                                'view_custody' => 'عرض عهدة',
                                'create_expense' => 'إنشاء مصروف',
                                'edit_expense' => 'تعديل مصروف',
                                'delete_expense' => 'حذف مصروف',
                                'approve_expense' => 'موافقة مصروف',
                                'view_expense' => 'عرض مصروف',
                                'view_reports' => 'عرض التقارير',
                                'export_reports' => 'تصدير التقارير',
                                'manage_users' => 'إدارة المستخدمين',
                                'edit_user' => 'تعديل مستخدم',
                                'delete_user' => 'حذف مستخدم',
                                'manage_locations' => 'إدارة المواقع',
                                'edit_location' => 'تعديل موقع',
                                'delete_location' => 'حذف موقع',
                                'manage_people' => 'إدارة الأشخاص',
                                'edit_person' => 'تعديل شخص',
                                'delete_person' => 'حذف شخص'
                            ];
                        @endphp
                        
                        @foreach($permissions as $permission => $name)
                            @if(\App\Helpers\PermissionHelper::hasPermission($user, $permission))
                                <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-green-800 font-medium">{{ $name }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection