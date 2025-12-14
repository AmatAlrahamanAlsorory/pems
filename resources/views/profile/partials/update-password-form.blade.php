<form method="post" action="{{ route('password.update') }}" class="space-y-6">
    @csrf
    @method('put')

    <div>
        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الحالية</label>
        <input id="current_password" name="current_password" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        @error('current_password', 'updatePassword')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الجديدة</label>
        <input id="password" name="password" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        @error('password', 'updatePassword')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        @error('password_confirmation', 'updatePassword')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-3">
        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-medium transition shadow-md">
            تغيير كلمة المرور
        </button>
        
        @if (session('status') === 'password-updated')
            <p class="text-sm text-green-600 py-2">تم تغيير كلمة المرور بنجاح</p>
        @endif
    </div>
</form>
