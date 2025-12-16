@props(['items', 'title' => '', 'type' => 'notifications'])

@if($items->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        @if($title)
            <div class="bg-red-500 text-white px-4 py-2 rounded-t-lg">
                <h3 class="font-semibold">{{ $title }}</h3>
            </div>
        @endif
        
        <div class="p-4">
            <!-- عرض أول 3 عناصر -->
            @foreach($items->take(3) as $item)
                <div class="flex items-start gap-3 p-3 border-r-4 
                    @if($type === 'critical') border-red-500 bg-red-50
                    @elseif($type === 'approvals') border-orange-500 bg-orange-50
                    @else border-blue-500 bg-blue-50 @endif
                    rounded mb-3 last:mb-0">
                    
                    @if($type === 'notifications')
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse mt-2"></div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $item->title }}</h4>
                            <p class="text-sm text-gray-600">{{ Str::limit($item->message, 80) }}</p>
                            <span class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                        </div>
                    @elseif($type === 'critical')
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse mt-2"></div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $item->name }}</h4>
                            <p class="text-sm text-gray-600">تجاوز {{ number_format($item->budget_percentage, 1) }}% من الميزانية</p>
                            <span class="text-xs text-gray-500">الميزانية: {{ number_format($item->total_budget) }} ريال</span>
                        </div>
                    @elseif($type === 'approvals')
                        <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse mt-2"></div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $item->approvable_type === 'App\\Models\\Custody' ? 'طلب عهدة' : 'طلب موافقة' }}</h4>
                            <p class="text-sm text-gray-600">{{ $item->approvable->description ?? 'في انتظار الموافقة' }}</p>
                            <span class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        <!-- الشريط المتحرك للعناصر الإضافية -->
        @if($items->count() > 3)
            <div class="
                @if($type === 'critical') bg-red-600
                @elseif($type === 'approvals') bg-orange-600
                @else bg-blue-600 @endif
                text-white py-2 overflow-hidden relative">
                <div class="ticker-wrapper">
                    <div class="ticker-content">
                        @foreach($items->skip(3) as $item)
                            <span class="ticker-item">
                                @if($type === 'notifications')
                                    📢 {{ $item->title }} - {{ Str::limit($item->message, 40) }}
                                @elseif($type === 'critical')
                                    ⚠️ {{ $item->name }} - تجاوز {{ number_format($item->budget_percentage, 1) }}%
                                @elseif($type === 'approvals')
                                    📋 {{ $item->approvable_type === 'App\\Models\\Custody' ? 'عهدة' : 'موافقة' }} - في الانتظار
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-xs bg-white/20 px-3 py-1 rounded-full">
                    {{ $items->count() - 3 }} إضافي
                </div>
            </div>
        @endif
    </div>
@endif