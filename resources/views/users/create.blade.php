@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">إضافة مستخدم جديد</h1>
        <p class="text-gray-600 mt-1">إنشاء حساب مستخدم جديد مع تحديد الصلاحيات</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- الاسم -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        الاسم الكامل *
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- البريد الإلكتروني -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        البريد الإلكتروني *
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- كلمة المرور -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        كلمة المرور *
                    </label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">يجب أن تكون 8 أحرف على الأقل</p>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الصلاحية -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        الصلاحية *
                    </label>
                    <select name="role" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                        <option value="">اختر الصلاحية</option>
                        <option value="financial_manager" {{ old('role') === 'financial_manager' ? 'selected' : '' }}>
                            مدير مالي - كامل الصلاحيات
                        </option>
                        <option value="admin_accountant" {{ old('role') === 'admin_accountant' ? 'selected' : '' }}>
                            محاسب إدارة - المراقبة والتقارير
                        </option>
                        <option value="production_manager" {{ old('role') === 'production_manager' ? 'selected' : '' }}>
                            مدير إنتاج - الموافقة على المصروفات
                        </option>
                        <option value="field_accountant" {{ old('role') === 'field_accountant' ? 'selected' : '' }}>
                            محاسب ميداني - إدخال المصروفات والتصفيات
                        </option>
                        <option value="financial_assistant" {{ old('role') === 'financial_assistant' ? 'selected' : '' }}>
                            مساعد مالي - إدخال المصروفات فقط
                        </option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الموقع -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        الموقع (اختياري)
                    </label>
                    <input type="text" name="location" value="{{ old('location') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="مثال: موقع صنعاء">
                    <p class="text-xs text-gray-500 mt-1">موقع عمل المستخدم (للمحاسبين الميدانيين)</p>
                </div>

                <!-- معلومات الصلاحيات -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-bold text-blue-900 mb-2">معلومات الصلاحيات:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><strong>مدير مالي:</strong> جميع الصلاحيات + إعدادات النظام</li>
                        <li><strong>محاسب إدارة:</strong> المراقبة + التقارير + تحويل العهد</li>
                        <li><strong>مدير إنتاج:</strong> الموافقة على المصروفات + طلب العهد</li>
                        <li><strong>محاسب ميداني:</strong> إدخال المصروفات + رفع الفواتير + التصفيات</li>
                        <li><strong>مساعد مالي:</strong> إدخال المصروفات + رفع الفواتير فقط</li>
                    </ul>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">
                    إضافة المستخدم
                </button>
                <a href="{{ route('users.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
