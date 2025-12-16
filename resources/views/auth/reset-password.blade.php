<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-blue-600 rounded-3xl flex items-center justify-center shadow-lg mb-6">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold text-blue-900 mb-2">إعادة تعيين كلمة المرور</h2>
                <p class="text-blue-700 text-lg font-medium">نظام إدارة المصروفات</p>
                <p class="text-blue-600 text-sm mt-2">أدخل كلمة مرور جديدة لحسابك</p>
            </div>

            <!-- Reset Form -->
            <div class="bg-white rounded-3xl p-8 border border-blue-200 shadow-2xl">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-blue-900 mb-2">
                            البريد الإلكتروني
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                               class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300"
                               placeholder="أدخل بريدك الإلكتروني">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-blue-900 mb-2">
                            كلمة المرور الجديدة
                        </label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300"
                               placeholder="أدخل كلمة مرور جديدة">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-blue-900 mb-2">
                            تأكيد كلمة المرور
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300"
                               placeholder="أعد إدخال كلمة المرور">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-4 px-6 rounded-2xl font-bold text-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transform hover:scale-105 transition-all duration-300 shadow-lg">
                            إعادة تعيين كلمة المرور
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-300">
                            العودة إلى تسجيل الدخول
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
