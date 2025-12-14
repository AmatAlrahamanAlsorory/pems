<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            توزيع الميزانية - {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-bold text-lg mb-2">معلومات المشروع</h3>
                        <p><strong>الميزانية الإجمالية:</strong> {{ number_format($project->total_budget) }} ر.س</p>
                        <p><strong>المصروف حتى الآن:</strong> {{ number_format($project->spent_amount) }} ر.س</p>
                    </div>

                    <form method="POST" action="{{ route('budget-allocations.store', $project) }}">
                        @csrf
                        
                        <div class="space-y-4" id="allocations-container">
                            @foreach($categories as $category)
                                @php
                                    $existing = $allocations->where('expense_category_id', $category->id)->first();
                                @endphp
                                <div class="flex items-center space-x-4 p-4 border rounded-lg" style="border-color: {{ $category->color }}">
                                    <div class="w-4 h-4 rounded" style="background-color: {{ $category->color }}"></div>
                                    <div class="flex-1">
                                        <label class="font-medium">{{ $category->name }}</label>
                                        <input type="hidden" name="allocations[{{ $loop->index }}][category_id]" value="{{ $category->id }}">
                                    </div>
                                    <div class="w-32">
                                        <input type="number" 
                                               name="allocations[{{ $loop->index }}][percentage]" 
                                               value="{{ $existing ? $existing->percentage : 0 }}"
                                               min="0" max="100" step="0.01"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                               placeholder="النسبة %">
                                    </div>
                                    <div class="w-40 text-sm text-gray-600" id="amount-{{ $loop->index }}">
                                        0 ر.س
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-bold">إجمالي النسب:</span>
                                <span id="total-percentage" class="font-bold text-lg">0%</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                حفظ التوزيع
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const totalBudget = {{ $project->total_budget }};
        
        function updateCalculations() {
            let totalPercentage = 0;
            
            document.querySelectorAll('input[name*="[percentage]"]').forEach((input, index) => {
                const percentage = parseFloat(input.value) || 0;
                totalPercentage += percentage;
                
                const amount = (totalBudget * percentage) / 100;
                document.getElementById(`amount-${index}`).textContent = 
                    new Intl.NumberFormat('ar-SA').format(amount) + ' ر.س';
            });
            
            const totalElement = document.getElementById('total-percentage');
            totalElement.textContent = totalPercentage.toFixed(2) + '%';
            totalElement.className = totalPercentage > 100 ? 'font-bold text-lg text-red-600' : 'font-bold text-lg';
        }
        
        document.querySelectorAll('input[name*="[percentage]"]').forEach(input => {
            input.addEventListener('input', updateCalculations);
        });
        
        updateCalculations();
    </script>
</x-app-layout>