<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white">
                    <h2 class="text-2xl font-bold">مشروع جديد</h2>
                    <p class="text-blue-100 mt-1">إضافة مشروع إنتاج فني جديد</p>
                </div>

                <!-- Form -->
                <form action="{{ route('projects.store') }}" method="POST" class="p-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">اسم المشروع *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">نوع المشروع *</label>
                            <select name="type" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">اختر النوع</option>
                                <option value="series" {{ old('type') == 'series' ? 'selected' : '' }}>مسلسل</option>
                                <option value="movie" {{ old('type') == 'movie' ? 'selected' : '' }}>فيلم</option>
                                <option value="program" {{ old('type') == 'program' ? 'selected' : '' }}>برنامج</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Budget -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">الميزانية الكلية *</label>
                            <input type="number" name="total_budget" value="{{ old('total_budget') }}" step="0.01" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('total_budget')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Reserve -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">احتياطي الطوارئ</label>
                            <input type="number" name="emergency_reserve" value="{{ old('emergency_reserve') }}" step="0.01"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('emergency_reserve')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Planned Days -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">عدد أيام التصوير</label>
                            <input type="number" name="planned_days" value="{{ old('planned_days') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('planned_days')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Episodes Count -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">عدد الحلقات</label>
                            <input type="number" name="episodes_count" value="{{ old('episodes_count') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('episodes_count')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">تاريخ البدء *</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">تاريخ الانتهاء</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">الوصف</label>
                            <textarea name="description" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-4 mt-8">
                        <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold shadow-lg transition duration-200">
                            حفظ المشروع
                        </button>
                        <a href="{{ route('projects.index') }}"
                           class="px-8 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold transition duration-200">
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
