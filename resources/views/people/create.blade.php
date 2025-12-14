<x-app-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold">إضافة شخص جديد</h2>
                </div>

                <form action="{{ route('people.store') }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم *</label>
                            <input type="text" name="name" required class="form-input">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">النوع *</label>
                            <select name="type" required class="form-select">
                                <option value="">اختر النوع</option>
                                <option value="actor">ممثل</option>
                                <option value="technician">فني</option>
                                <option value="crew">طاقم</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-input">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهوية</label>
                            <input type="text" name="id_number" class="form-input">
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="btn-primary">حفظ</button>
                        <a href="{{ route('people.index') }}" class="btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>