// نظام التنبيهات المباشر
class NotificationSystem {
    constructor() {
        this.checkInterval = 30000; // 30 ثانية
        this.init();
    }

    init() {
        this.startPolling();
        this.bindEvents();
    }

    startPolling() {
        setInterval(() => {
            this.checkForNewNotifications();
        }, this.checkInterval);
    }

    async checkForNewNotifications() {
        try {
            const response = await fetch('/api/notifications/unread');
            const data = await response.json();
            
            if (data.count > 0) {
                this.updateNotificationBadge(data.count);
                
                // إذا كان هناك إشعار جديد، أعد تحميل منطقة التنبيهات
                if (data.latest && this.isNewNotification(data.latest)) {
                    this.refreshNotificationArea();
                }
            }
        } catch (error) {
            console.log('خطأ في جلب التنبيهات:', error);
        }
    }

    updateNotificationBadge(count) {
        const badges = document.querySelectorAll('.notification-badge');
        badges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    isNewNotification(notification) {
        const lastCheck = localStorage.getItem('lastNotificationCheck');
        const notificationTime = new Date(notification.created_at).getTime();
        const lastCheckTime = lastCheck ? parseInt(lastCheck) : 0;
        
        if (notificationTime > lastCheckTime) {
            localStorage.setItem('lastNotificationCheck', Date.now().toString());
            return true;
        }
        return false;
    }

    refreshNotificationArea() {
        // إعادة تحميل الصفحة إذا كان المستخدم في صفحة التنبيهات
        if (window.location.pathname.includes('notifications')) {
            window.location.reload();
        } else {
            // أو إظهار تنبيه بسيط
            this.showNewNotificationAlert();
        }
    }

    showNewNotificationAlert() {
        // إنشاء تنبيه منبثق
        const alert = document.createElement('div');
        alert.className = 'fixed top-4 right-4 bg-blue-600 text-white px-4 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
        alert.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                <span class="text-sm font-medium">لديك إشعار جديد</span>
                <a href="/notifications" class="text-xs bg-white/20 px-2 py-1 rounded hover:bg-white/30">
                    عرض
                </a>
                <button onclick="this.parentElement.parentElement.remove()" class="text-white/80 hover:text-white">
                    ×
                </button>
            </div>
        `;
        
        document.body.appendChild(alert);
        
        // إزالة التنبيه بعد 5 ثوان
        setTimeout(() => {
            if (alert.parentElement) {
                alert.remove();
            }
        }, 5000);
    }

    bindEvents() {
        // ربط أحداث قراءة التنبيهات
        document.addEventListener('click', async (e) => {
            if (e.target.matches('.mark-notification-read')) {
                e.preventDefault();
                const notificationId = e.target.dataset.notificationId;
                await this.markAsRead(notificationId);
            }
        });
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                // تحديث العداد
                this.checkForNewNotifications();
            }
        } catch (error) {
            console.log('خطأ في تحديد الإشعار كمقروء:', error);
        }
    }
}

// تشغيل النظام عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    new NotificationSystem();
});

// تحديث الشريط المتحرك
function updateTickerSpeed() {
    const ticker = document.querySelector('.ticker-content');
    if (ticker) {
        const itemCount = ticker.children.length;
        const duration = Math.max(20, itemCount * 3); // حد أدنى 20 ثانية
        ticker.style.animationDuration = `${duration}s`;
    }
}

// تشغيل تحديث السرعة عند التحميل
document.addEventListener('DOMContentLoaded', updateTickerSpeed);