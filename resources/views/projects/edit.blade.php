<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white">
                    <h2 class="text-2xl font-bold">تعديل المشروع</h2>
                    <p class="text-blue-100 mt-1">{{ $project->name }}</p>
                </div>

                <form action="{{ route('projects.update', $project) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">اسم المشروع *</label>
                            <input type="text" name="name" value="{{ old('name', $project->name) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">نوع المشروع *</label>
                            <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="series" {{ $project->type == 'series' ? 'selected' : '' }}>مسلسل</option>
                                <option value="movie" {{ $project->type == 'movie' ? 'selected' : '' }}>فيلم</option>
                                <option value="program" {{ $project->type == 'program' ? 'selected' : '' }}>برنامج</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">الحالة *</label>
                            <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="planning" {{ $project->status == 'planning' ? 'selected' : '' }}>تخطيط</option>
                                <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="on_hold" {{ $project->status == 'on_hold' ? 'selected' : '' }}>متوقف</option>
                                <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ $project->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">الميزانية الكلية *</label>
                            <input type="number" name="total_budget" value="{{ old('total_budget', $project->total_budget) }}" step="0.01" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">احتياطي الطوارئ</label>
                            <input type="number" name="emergency_reserve" value="{{ old('emergency_reserve', $project->emergency_reserve) }}" step="0.01"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">عدد أيام التصوير</label>
                            <input type="number" name="planned_days" value="{{ old('planned_days', $project->planned_days) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">عدد الحلقات</label>
                            <input type="number" name="episodes_count" value="{{ old('episodes_count', $project->episodes_count) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">تاريخ البدء *</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">تاريخ الانتهاء</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">الوصف</label>
                            <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $project->description) }}</textarea>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-8">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold shadow-lg transition">
                            حفظ التعديلات
                        </button>
                        <a href="{{ route('projects.index') }}" class="px-8 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold transition">
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
