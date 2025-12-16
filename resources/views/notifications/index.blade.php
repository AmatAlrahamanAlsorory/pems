<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">الإشعارات</h2>
                    <p class="text-sm text-gray-600 mt-1">إدارة جميع الإشعارات والتنبيهات</p>
                </div>
                @if($notifications->where('is_read', false)->count() > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            تحديد الكل كمقروء ({{ $notifications->where('is_read', false)->count() }})
                        </button>
                    </form>
                @endif
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="stat-card">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-600">إجمالي الإشعارات</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $notifications->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-600">غير مقروءة</p>
                            <p class="text-2xl font-bold text-red-600">{{ $notifications->where('is_read', false)->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-600">مقروءة</p>
                            <p class="text-2xl font-bold text-green-600">{{ $notifications->where('is_read', true)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card">
                @forelse($notifications as $notification)
                    <div class="notification-item p-4 border-b border-gray-100 last:border-b-0 {{ !$notification->is_read ? 'bg-blue-50/50' : '' }}">
                        <div class="flex items-start gap-4">
                            @if(!$notification->is_read)
                                <div class="notification-dot mt-2"></div>
                            @else
                                <div class="w-2 h-2 bg-gray-300 rounded-full mt-2"></div>
                            @endif
                            
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 {{ !$notification->is_read ? 'text-blue-900' : '' }}">
                                            {{ $notification->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                            {{ $notification->message }}
                                        </p>
                                        <div class="flex items-center gap-4 mt-3">
                                            <span class="text-xs text-gray-500 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if(!$notification->is_read)
                                                <span class="badge badge-warning">
                                                    جديد
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if(!$notification->is_read)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}" class="mr-4">
                                            @csrf
                                            <button type="submit" class="btn-secondary text-xs py-1 px-3">
                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                تحديد كمقروء
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد إشعارات</h3>
                        <p class="text-gray-500">ستظهر الإشعارات الجديدة هنا عند توفرها</p>
                    </div>
                @endforelse
            </div>
            

            
            @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>