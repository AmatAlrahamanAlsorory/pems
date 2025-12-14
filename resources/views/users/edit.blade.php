@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">تعديل المستخدم</h1>
        <p class="text-gray-600 mt-1">تحديث بيانات المستخدم: {{ $user->name }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- الاسم -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        الاسم الكامل *
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
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
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- كلمة المرور -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        كلمة المرور الجديدة (اختياري)
                    </label>
                    <input type="password" name="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور</p>
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
                        <option value="financial_manager" {{ old('role', $user->role) === 'financial_manager' ? 'selected' : '' }}>
                            مدير مالي - كامل الصلاحيات
                        </option>
                        <option value="admin_accountant" {{ old('role', $user->role) === 'admin_accountant' ? 'selected' : '' }}>
                            محاسب إدارة - المراقبة والتقارير
                        </option>
                        <option value="production_manager" {{ old('role', $user->role) === 'production_manager' ? 'selected' : '' }}>
                            مدير إنتاج - الموافقة على المصروفات
                        </option>
                        <option value="field_accountant" {{ old('role', $user->role) === 'field_accountant' ? 'selected' : '' }}>
                            محاسب ميداني - إدخال المصروفات والتصفيات
                        </option>
                        <option value="financial_assistant" {{ old('role', $user->role) === 'financial_assistant' ? 'selected' : '' }}>
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
                    <input type="text" name="location" value="{{ old('location', $user->location) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="مثال: موقع صنعاء">
                </div>

                <!-- معلومات إضافية -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">تاريخ الإنشاء:</span>
                            <span class="font-semibold">{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">آخر تحديث:</span>
                            <span class="font-semibold">{{ $user->updated_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">
                    حفظ التعديلات
                </button>
                <a href="{{ route('users.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
