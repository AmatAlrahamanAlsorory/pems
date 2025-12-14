@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">إضافة موقع جديد</h1>

    <form action="{{ route('locations.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">المشروع *</label>
            <select name="project_id" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
                <option value="">اختر المشروع</option>
                @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">اسم الموقع *</label>
            <input type="text" name="name" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold mb-2">المدينة</label>
                <input type="text" name="city" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">الحالة</label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
                    <option value="active">نشط</option>
                    <option value="inactive">غير نشط</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">العنوان</label>
            <textarea name="address" rows="2" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">الميزانية المخصصة *</label>
            <input type="number" name="budget_allocated" required step="0.01" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4 p-4 bg-blue-50 rounded">
            <label class="flex items-center mb-2">
                <input type="checkbox" id="add-gps" class="ml-2">
                <span class="text-sm font-semibold">إضافة الموقع الجغرافي (GPS)</span>
            </label>
            <div id="gps-fields" class="hidden mt-3 space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs mb-1">خط العرض (Latitude)</label>
                        <input type="number" name="latitude" step="0.00000001" class="w-full px-3 py-2 border rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs mb-1">خط الطول (Longitude)</label>
                        <input type="number" name="longitude" step="0.00000001" class="w-full px-3 py-2 border rounded text-sm">
                    </div>
                </div>
                <button type="button" onclick="getCurrentLocation()" class="text-sm text-blue-600 hover:text-blue-800">
                    📍 استخدام موقعي الحالي
                </button>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded font-semibold">
                حفظ الموقع
            </button>
            <a href="{{ route('locations.index') }}" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 rounded">
                إلغاء
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('add-gps').addEventListener('change', function() {
    document.getElementById('gps-fields').classList.toggle('hidden', !this.checked);
});

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="latitude"]').value = position.coords.latitude;
            document.querySelector('input[name="longitude"]').value = position.coords.longitude;
            alert('تم الحصول على الموقع بنجاح!');
        }, function() {
            alert('لم نتمكن من الحصول على موقعك');
        });
    } else {
        alert('المتصفح لا يدعم تحديد الموقع');
    }
}
</script>
@endsection
