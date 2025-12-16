<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-blue-900">تفاصيل الشخص</h2>
                    <p class="text-gray-600 mt-1">{{ $person->name }}</p>
                </div>
                <a href="{{ route('people.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold transition">
                    رجوع
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- معلومات الشخص -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white">
                            <h3 class="text-xl font-bold">المعلومات الأساسية</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600">الاسم</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $person->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">المنصب</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $person->position ?? 'غير محدد' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">رقم الهاتف</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $person->phone ?? 'غير محدد' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $person->email ?? 'غير محدد' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">النوع</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    @if($person->type == 'actor')
                                        ممثل
                                    @elseif($person->type == 'director')
                                        مخرج
                                    @elseif($person->type == 'producer')
                                        منتج
                                    @elseif($person->type == 'crew')
                                        طاقم فني
                                    @else
                                        {{ $person->type }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">تاريخ الإضافة</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $person->created_at->format('Y/m/d') }}</p>
                            </div>
                        </div>
                        @if($person->notes)
                        <div class="px-6 pb-6">
                            <p class="text-sm text-gray-600 mb-2">ملاحظات</p>
                            <p class="text-gray-700">{{ $person->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- المشاريع المشارك فيها -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-green-600 to-green-700 p-6 text-white">
                            <h3 class="text-xl font-bold">المشاريع المشارك فيها</h3>
                        </div>
                        <div class="p-6">
                            @php
                                $projects = \App\Models\Project::whereHas('expenses', function($query) use ($person) {
                                    $query->where('person_id', $person->id);
                                })->get();
                            @endphp
                            
                            @forelse($projects as $project)
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $project->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $project->type }}</p>
                                        <p class="text-xs text-gray-500">{{ $project->start_date ? $project->start_date->format('Y/m/d') : 'غير محدد' }}</p>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-lg font-bold text-blue-600">{{ number_format($project->total_budget) }} ر.ي</p>
                                        <p class="text-sm text-gray-500">الميزانية الإجمالية</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8">لا يوجد مشاريع مشارك فيها</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- الشريط الجانبي -->
                <div class="space-y-6">
                    <!-- إحصائيات -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-purple-600 to-purple-700 p-6 text-white">
                            <h3 class="text-xl font-bold">الإحصائيات</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @php
                                $totalExpenses = \App\Models\Expense::where('person_id', $person->id)->sum('amount');
                                $expensesCount = \App\Models\Expense::where('person_id', $person->id)->count();
                                $projectsCount = \App\Models\Project::whereHas('expenses', function($query) use ($person) {
                                    $query->where('person_id', $person->id);
                                })->count();
                            @endphp
                            
                            <div>
                                <p class="text-sm text-gray-600">إجمالي المصروفات</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalExpenses) }} ر.ي</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">عدد المصروفات</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $expensesCount }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">عدد المشاريع</p>
                                <p class="text-2xl font-bold text-green-600">{{ $projectsCount }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- الإجراءات -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-orange-600 to-orange-700 p-6 text-white">
                            <h3 class="text-xl font-bold">الإجراءات</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @permission('manage_people')
                            <a href="{{ route('people.edit', $person) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-lg font-semibold transition">
                                تعديل البيانات
                            </a>
                            @endpermission
                            
                            <a href="{{ route('expenses.create') }}?person_id={{ $person->id }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-lg font-semibold transition">
                                إضافة مصروف
                            </a>
                            
                            <a href="{{ route('reports.person') }}?person_id={{ $person->id }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-3 rounded-lg font-semibold transition">
                                تقرير مفصل
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>