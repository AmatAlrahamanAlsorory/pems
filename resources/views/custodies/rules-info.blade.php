<!-- معلومات قواعد العهد -->
<div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
    <div class="flex items-start gap-3">
        <div class="bg-blue-500 p-2 rounded-full mt-1">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-900 mb-2">قواعد العهد الصارمة</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-white p-3 rounded-lg border">
                    <div class="text-sm text-gray-600">العهد المفتوحة حالياً</div>
                    <div class="text-xl font-bold {{ $stats['open_custodies'] >= 2 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $stats['open_custodies'] }} / 2
                    </div>
                </div>
                
                <div class="bg-white p-3 rounded-lg border">
                    <div class="text-sm text-gray-600">العهد المتأخرة</div>
                    <div class="text-xl font-bold {{ $stats['overdue_custodies'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $stats['overdue_custodies'] }}
                    </div>
                </div>
            </div>
            
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2">
                    <span class="{{ $stats['open_custodies'] < 2 ? 'text-green-600' : 'text-red-600' }}">●</span>
                    <span>الحد الأقصى: عهدتان مفتوحتان فقط</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="{{ $stats['overdue_custodies'] == 0 ? 'text-green-600' : 'text-red-600' }}">●</span>
                    <span>يجب تصفية 80% من العهدة قبل طلب جديدة</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="{{ $stats['overdue_custodies'] == 0 ? 'text-green-600' : 'text-red-600' }}">●</span>
                    <span>تصفية إجبارية كل 7 أيام</span>
                </div>
            </div>
            
            @if(!$stats['can_request_new'])
                <div class="mt-3 p-3 bg-red-100 border border-red-300 rounded-lg">
                    <div class="text-red-800 font-medium">⚠️ لا يمكن طلب عهدة جديدة</div>
                    <div class="text-red-700 text-sm mt-1">يرجى تصفية العهد الحالية أولاً</div>
                </div>
            @endif
        </div>
    </div>
</div>