@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">المواقع</h1>
        <div class="flex gap-2">
            <a href="{{ route('locations.map') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                <svg class="w-5 h-5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                عرض الخريطة
            </a>
            @can('manage_locations')
            <a href="{{ route('locations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">+ موقع جديد</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($locations as $location)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold">{{ $location->name }}</h3>
                        <p class="text-gray-600 text-sm">{{ $location->city }}</p>
                    </div>
                    <span class="px-3 py-1 rounded text-sm {{ $location->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $location->status === 'active' ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>

                <p class="text-sm text-gray-700 mb-4">{{ $location->project->name }}</p>

                @if($location->budget_allocated > 0)
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span>الميزانية</span>
                        <span class="font-bold">{{ number_format($location->budget_percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $location->budget_percentage >= 90 ? 'bg-red-600' : ($location->budget_percentage >= 70 ? 'bg-yellow-600' : 'bg-green-600') }}" 
                             style="width: {{ min($location->budget_percentage, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-600 mt-1">
                        <span>{{ number_format($location->spent_amount) }} ر.س</span>
                        <span>{{ number_format($location->budget_allocated) }} ر.س</span>
                    </div>
                </div>
                @endif

                <div class="flex gap-2">
                    <a href="{{ route('locations.show', $location) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded text-sm">
                        التفاصيل
                    </a>
                    @if($location->latitude && $location->longitude)
                    <a href="{{ $location->map_url }}" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
