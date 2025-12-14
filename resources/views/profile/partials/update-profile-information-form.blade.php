<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم</label>
        <input id="name" name="name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('name', $user->name) }}" required autofocus>
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
        <input id="email" name="email" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('email', $user->email) }}" required>
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-3">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
            حفظ التغييرات
        </button>
        
        @if (session('status') === 'profile-updated')
            <p class="text-sm text-green-600 py-2">تم الحفظ بنجاح</p>
        @endif
    </div>
</div>

<form method="post" action="{{ route('profile.update') }}" class="hidden" id="profile-form">
    @csrf
    @method('patch')
</form>

<script>
    document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.getElementById('profile-form');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        
        // إضافة القيم للنموذج
        const nameField = document.createElement('input');
        nameField.type = 'hidden';
        nameField.name = 'name';
        nameField.value = nameInput.value;
        form.appendChild(nameField);
        
        const emailField = document.createElement('input');
        emailField.type = 'hidden';
        emailField.name = 'email';
        emailField.value = emailInput.value;
        form.appendChild(emailField);
        
        form.submit();
    });
</script>
