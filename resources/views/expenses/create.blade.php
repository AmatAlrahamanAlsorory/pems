<x-app-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h3 class="text-red-800 font-medium mb-2">يوجد أخطاء:</h3>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">تسجيل مصروف جديد</h2>
                <p class="text-gray-600 text-sm">إضافة مصروف جديد للمشروع</p>
            </div>
            
            <div class="card">
                <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المشروع *</label>
                                <select name="project_id" required class="form-select">
                                    <option value="">اختر المشروع</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">العهدة</label>
                                <select name="custody_id" class="form-select">
                                    <option value="">بدون عهدة</option>
                                    @foreach($custodies as $custody)
                                        <option value="{{ $custody->id }}">
                                            {{ $custody->custody_number ?? 'C-' . $custody->id }} - {{ $custody->project->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">فئة المصروف *</label>
                                <select name="expense_category_id" required class="form-select">
                                    <option value="">اختر الفئة</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->code }} - {{ $category->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">عنصر المصروف *</label>
                                <select name="expense_item_id" required class="form-select">
                                    <option value="">اختر العنصر</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ *</label>
                                <input type="number" name="amount" step="0.01" required class="form-input">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">العملة *</label>
                                <select name="currency" required class="form-select">
                                    <option value="YER">ريال يمني (YER)</option>
                                    <option value="SAR">ريال سعودي (SAR)</option>
                                    <option value="USD">دولار أمريكي (USD)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الوصف *</label>
                            <input type="text" name="description" required class="form-input">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ المصروف *</label>
                                <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="form-input">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">رقم الفاتورة</label>
                                <input type="text" name="invoice_number" class="form-input">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الشخص المرتبط</label>
                            <select name="person_id" class="form-select">
                                <option value="">غير محدد</option>
                                @foreach($people as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }} - {{ $person->type_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">صورة الفاتورة</label>
                            <input type="file" name="invoice_file" accept="image/*" class="form-input">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" rows="3" class="form-input"></textarea>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="btn-primary flex-1">حفظ المصروف</button>
                        <a href="{{ route('expenses.index') }}" class="btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>