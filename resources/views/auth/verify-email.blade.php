<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-blue-600 rounded-3xl flex items-center justify-center shadow-lg mb-6">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold text-blue-900 mb-2">تحقق من بريدك</h2>
                <p class="text-blue-700 text-lg font-medium">نظام إدارة المصروفات</p>
            </div>

            <!-- Verification Form -->
            <div class="bg-white rounded-3xl p-8 border border-blue-200 shadow-2xl">
                <div class="mb-6 text-sm text-gray-600 text-center">
                    شكراً للتسجيل! قبل البدء، يرجى تأكيد عنوان بريدك الإلكتروني بالنقر على الرابط الذي أرسلناه إليك. إذا لم تستلم البريد، فسنرسل لك بريداً آخر بكل سرور.
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 font-medium text-sm text-green-600 text-center bg-green-50 p-4 rounded-lg">
                        تم إرسال رابط تأكيد جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.
                    </div>
                @endif

                <div class="space-y-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-4 px-6 rounded-2xl font-bold text-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transform hover:scale-105 transition-all duration-300 shadow-lg">
                            إعادة إرسال بريد التأكيد
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-2xl font-medium hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300/30 transition-all duration-300">
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
