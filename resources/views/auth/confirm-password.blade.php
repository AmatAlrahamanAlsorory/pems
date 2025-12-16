<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-blue-600 rounded-3xl flex items-center justify-center shadow-lg mb-6">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold text-blue-900 mb-2">تأكيد كلمة المرور</h2>
                <p class="text-blue-700 text-lg font-medium">نظام إدارة المصروفات</p>
                <p class="text-blue-600 text-sm mt-2">هذه منطقة آمنة. يرجى تأكيد كلمة المرور قبل المتابعة</p>
            </div>

            <!-- Confirm Form -->
            <div class="bg-white rounded-3xl p-8 border border-blue-200 shadow-2xl">
                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-blue-900 mb-2">
                            كلمة المرور
                        </label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-2xl text-blue-900 placeholder-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 transition-all duration-300"
                               placeholder="أدخل كلمة المرور">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-4 px-6 rounded-2xl font-bold text-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transform hover:scale-105 transition-all duration-300 shadow-lg">
                            تأكيد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
