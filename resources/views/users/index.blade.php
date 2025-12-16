@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة المستخدمين</h1>
            <p class="text-gray-600 mt-1">إدارة حسابات المستخدمين والصلاحيات</p>
        </div>
        <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            إضافة مستخدم
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الصلاحية</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموقع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الإنشاء</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="mr-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($user->role === 'financial_manager') bg-purple-100 text-purple-800
                            @elseif($user->role === 'admin_accountant') bg-blue-100 text-blue-800
                            @elseif($user->role === 'production_manager') bg-green-100 text-green-800
                            @elseif($user->role === 'field_accountant') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @switch($user->role)
                                @case('financial_manager') مدير مالي @break
                                @case('admin_accountant') محاسب إدارة @break
                                @case('production_manager') مدير إنتاج @break
                                @case('field_accountant') محاسب ميداني @break
                                @case('financial_assistant') مساعد مالي @break
                                @default {{ $user->role }}
                            @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->location ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('Y-m-d') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex gap-1">
                            <a href="{{ route('users.show', $user) }}" class="text-blue-700 hover:text-blue-900 text-xs font-bold border border-blue-300 hover:border-blue-500 px-2 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                عرض
                            </a>
                            
                            @if(\App\Helpers\PermissionHelper::canEditUser(auth()->user()))
                                <a href="{{ route('users.edit', $user) }}" class="text-yellow-700 hover:text-yellow-900 text-xs font-bold border border-yellow-300 hover:border-yellow-500 px-2 py-1 rounded bg-yellow-50 hover:bg-yellow-100">
                                    تعديل
                                </a>
                            @endif
                            
                            @if(\App\Helpers\PermissionHelper::canDeleteUser(auth()->user()) && $user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 hover:text-red-900 text-xs font-bold border border-red-300 hover:border-red-500 px-2 py-1 rounded bg-red-50 hover:bg-red-100">
                                        حذف
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-lg">لا يوجد مستخدمين</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
