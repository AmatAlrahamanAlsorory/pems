<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-blue-600 rounded-3xl flex items-center justify-center shadow-lg mb-6">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold text-blue-900 mb-2">حساب جديد</h2>
                <p class="text-blue-700 text-lg font-medium">نظام إدارة المصروفات</p>
                <p class="text-blue-600 text-sm mt-2">إنشاء حساب جديد في نظام PEMS</p>
            </div>

            <!-- Register Form -->
            <div class="bg-white rounded-3xl p-8 border border-blue-200 shadow-2xl">
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-blue-900 mb-2">
                            الاسم الكامل
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300"
                               placeholder="أدخل اسمك الكامل">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-blue-900 mb-2">
                            البريد الإلكتروني
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300"
                               placeholder="أدخل بريدك الإلكتروني">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-blue-900 mb-2">
                            الدور الوظيفي
                        </label>
                        <select id="role" name="role" required
                                class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300">
                            <option value="" class="text-gray-800">اختر الدور</option>
                            <option value="financial_assistant" class="text-gray-800">مساعد مالي</option>
                            <option value="field_accountant" class="text-gray-800">محاسب ميداني</option>
                            <option value="production_manager" class="text-gray-800">مدير إنتاج</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-500" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-blue-900 mb-2">
                            كلمة المرور
                        </label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300"
                               placeholder="أدخل كلمة مرور قوية">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-blue-900 mb-2">
                            تأكيد كلمة المرور
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300"
                               placeholder="أعد إدخال كلمة المرور">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-4 px-6 rounded-2xl font-bold text-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transform hover:scale-105 transition-all duration-300 shadow-lg">
                            إنشاء الحساب
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-300">
                            هل لديك حساب بالفعل؟ سجل الدخول
                        </a>
                    </div>
                </form>
            </div>


        </div>
    </div>
</x-guest-layout>