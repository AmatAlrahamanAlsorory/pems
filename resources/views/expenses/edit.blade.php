<x-app-layout>
    <x-slot name="header">
        <div class="bg-white border-b border-gray-200 -mx-6 -mt-6 px-6 py-4 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        تعديل المصروف
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">تحديث بيانات المصروف</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                        <span class="text-green-700 font-medium text-sm">رقم المصروف: {{ $expense->expense_number }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
                <div class="p-8">
                    <form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-8">
                        @csrf
                        @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">المشروع</label>
                            <select name="project_id" id="project_id" class="form-select" required>
                                <option value="">اختر المشروع</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ $expense->project_id == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expense_category_id" class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                            <select name="expense_category_id" id="expense_category_id" class="form-select" required>
                                <option value="">اختر الفئة</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $expense->expense_category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expense_item_id" class="block text-sm font-medium text-gray-700 mb-2">البند</label>
                            <select name="expense_item_id" id="expense_item_id" class="form-select" required>
                                <option value="">اختر البند</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ $expense->expense_item_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_item_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">المبلغ</label>
                            <input type="number" name="amount" id="amount" value="{{ old('amount', $expense->amount) }}" 
                                   class="form-input" step="0.01" min="0" required>
                            @error('amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">العملة</label>
                            <select name="currency" id="currency" class="form-select" required>
                                <option value="YER" {{ $expense->currency == 'YER' ? 'selected' : '' }}>ريال يمني</option>
                                <option value="SAR" {{ $expense->currency == 'SAR' ? 'selected' : '' }}>ريال سعودي</option>
                                <option value="USD" {{ $expense->currency == 'USD' ? 'selected' : '' }}>دولار أمريكي</option>
                            </select>
                            @error('currency')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ المصروف</label>
                            <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $expense->expense_date) }}" 
                                   class="form-input" required>
                            @error('expense_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="custody_id" class="block text-sm font-medium text-gray-700 mb-2">العهدة (اختياري)</label>
                            <select name="custody_id" id="custody_id" class="form-select">
                                <option value="">بدون عهدة</option>
                                @foreach($custodies as $custody)
                                    <option value="{{ $custody->id }}" {{ $expense->custody_id == $custody->id ? 'selected' : '' }}>
                                        {{ $custody->custody_number ?? 'C-' . $custody->id }} - {{ $custody->project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('custody_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="person_id" class="block text-sm font-medium text-gray-700 mb-2">الشخص (اختياري)</label>
                            <select name="person_id" id="person_id" class="form-select">
                                <option value="">اختر الشخص</option>
                                @foreach($people as $person)
                                    <option value="{{ $person->id }}" {{ $expense->person_id == $person->id ? 'selected' : '' }}>
                                        {{ $person->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('person_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">رقم الفاتورة</label>
                            <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number', $expense->invoice_number) }}" 
                                   class="form-input">
                            @error('invoice_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                            <textarea name="description" id="description" rows="3" class="form-textarea" required>{{ old('description', $expense->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <a href="{{ route('expenses.index') }}" class="btn-secondary">إلغاء</a>
                        <button type="submit" class="btn-primary">تحديث المصروف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>