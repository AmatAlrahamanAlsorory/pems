@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">{{ $location->name }}</h1>
        <div class="flex gap-2">
            @if($location->latitude && $location->longitude)
            <a href="{{ $location->map_url }}" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                عرض على الخريطة
            </a>
            @endif
            <a href="{{ route('locations.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                العودة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4">معلومات الموقع</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">المشروع</p>
                        <p class="font-semibold">{{ $location->project->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المدينة</p>
                        <p class="font-semibold">{{ $location->city ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">الحالة</p>
                        <span class="px-3 py-1 rounded text-sm {{ $location->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $location->status === 'active' ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">العنوان</p>
                        <p class="font-semibold">{{ $location->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            @if($location->latitude && $location->longitude)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div id="location-map" style="height: 400px;"></div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4">العهد النشطة</h3>
                @if($location->custodies->count() > 0)
                <div class="space-y-2">
                    @foreach($location->custodies as $custody)
                    <div class="border rounded p-3 flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $custody->custody_number }}</p>
                            <p class="text-sm text-gray-600">{{ $custody->purpose }}</p>
                        </div>
                        <div class="text-left">
                            <p class="font-bold">{{ number_format($custody->amount) }} ر.س</p>
                            <span class="text-xs px-2 py-1 rounded {{ $custody->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100' }}">
                                {{ $custody->status }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">لا توجد عهد</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">الميزانية</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">المخصص</p>
                        <p class="text-2xl font-bold">{{ number_format($location->budget_allocated) }} ر.س</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">المصروف</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($location->spent_amount) }} ر.س</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">المتبقي</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($location->remaining_budget) }} ر.س</p>
                    </div>
                    <div class="pt-4 border-t">
                        <div class="flex justify-between text-sm mb-2">
                            <span>نسبة الصرف</span>
                            <span class="font-bold">{{ number_format($location->budget_percentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $location->budget_percentage >= 90 ? 'bg-red-600' : ($location->budget_percentage >= 70 ? 'bg-yellow-600' : 'bg-green-600') }}" 
                                 style="width: {{ min($location->budget_percentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">إحصائيات</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">عدد العهد</span>
                        <span class="font-bold">{{ $location->custodies->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">عدد المصروفات</span>
                        <span class="font-bold">{{ $location->expenses->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($location->latitude && $location->longitude)
<script>
function initMap() {
    const position = { lat: {{ $location->latitude }}, lng: {{ $location->longitude }} };
    
    const map = new google.maps.Map(document.getElementById('location-map'), {
        zoom: 14,
        center: position
    });
    
    new google.maps.Marker({
        position: position,
        map: map,
        title: '{{ $location->name }}'
    });
}

window.initMap = initMap;
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initMap&language=ar"></script>
@endif
@endsection
