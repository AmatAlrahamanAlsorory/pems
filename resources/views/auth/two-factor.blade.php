<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h2 class="text-lg font-semibold mb-2">التحقق بخطوتين</h2>
        <p>تم إرسال رمز التحقق إلى بريدك الإلكتروني ورقم هاتفك</p>
    </div>

    <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="code" value="رمز التحقق" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-2xl tracking-widest" 
                         type="text" name="code" maxlength="6" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <form method="POST" action="{{ route('2fa.send') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                    إعادة إرسال الرمز
                </button>
            </form>

            <x-primary-button>
                تحقق
            </x-primary-button>
        </div>
    </form>

    <script>
        // Auto-submit when 6 digits entered
        document.getElementById('code').addEventListener('input', function(e) {
            if (e.target.value.length === 6) {
                e.target.form.submit();
            }
        });
    </script>
</x-guest-layout>