// نظام الإشعارات الفورية
class NotificationSystem {
    constructor() {
        this.init();
        this.startPolling();
    }

    init() {
        // طلب إذن الإشعارات
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // إضافة مستمع للنقر على الإشعارات
        document.addEventListener('click', (e) => {
            if (e.target.matches('.notification-item')) {
                this.markAsRead(e.target.dataset.id);
            }
        });
    }

    startPolling() {
        // فحص الإشعارات كل 30 ثانية
        setInterval(() => {
            this.checkNotifications();
        }, 30000);

        // فحص فوري عند التحميل
        this.checkNotifications();
    }

    async checkNotifications() {
        try {
            const response = await fetch('/api/notifications/unread');
            const data = await response.json();
            
            this.updateNotificationBadge(data.count);
            
            if (data.latest && this.shouldShowNotification(data.latest)) {
                this.showBrowserNotification(data.latest);
            }
            
        } catch (error) {
            console.error('خطأ في جلب الإشعارات:', error);
        }
    }

    updateNotificationBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'block' : 'none';
        }
    }

    shouldShowNotification(notification) {
        // تجنب إظهار نفس الإشعار مرتين
        const lastShown = localStorage.getItem('last_notification_id');
        return notification.id != lastShown;
    }

    showBrowserNotification(notification) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const browserNotification = new Notification(notification.title, {
                body: notification.message,
                icon: '/icon-192.png',
                badge: '/icon-192.png',
                tag: `notification-${notification.id}`,
                requireInteraction: notification.level === 'critical'
            });

            browserNotification.onclick = () => {
                window.focus();
                this.markAsRead(notification.id);
                browserNotification.close();
            };

            // إغلاق تلقائي بعد 5 ثوان (إلا إذا كان حرج)
            if (notification.level !== 'critical') {
                setTimeout(() => browserNotification.close(), 5000);
            }

            localStorage.setItem('last_notification_id', notification.id);
        }
    }

    async markAsRead(notificationId) {
        try {
            await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
            
            // تحديث العداد
            this.checkNotifications();
            
        } catch (error) {
            console.error('خطأ في تحديث الإشعار:', error);
        }
    }

    // إشعار فوري للتنبيهات الحرجة
    showCriticalAlert(message, type = 'danger') {
        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        ${this.getAlertIcon(type)}
                    </div>
                    <div class="flex-grow-1">
                        <strong>تنبيه هام!</strong><br>
                        <small>${message}</small>
                    </div>
                    <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        if (type === 'danger') {
            this.playNotificationSound();
        }
        
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) alert.remove();
        }, 10000);
    }
    
    getAlertIcon(type) {
        const icons = {
            'danger': '<i class="fas fa-exclamation-triangle text-red-500"></i>',
            'warning': '<i class="fas fa-exclamation-circle text-yellow-500"></i>',
            'success': '<i class="fas fa-check-circle text-green-500"></i>',
            'info': '<i class="fas fa-info-circle text-blue-500"></i>'
        };
        return icons[type] || icons['info'];
    }
    
    playNotificationSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.2);
        } catch (e) {
            console.log('Audio not supported');
        }
    }
}

// تشغيل النظام عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    window.notificationSystem = new NotificationSystem();
});

// إضافة إشعارات للأحداث المهمة
window.addEventListener('budget-exceeded', (event) => {
    window.notificationSystem.showCriticalAlert(
        `تم تجاوز ميزانية المشروع: ${event.detail.project}`,
        'danger'
    );
});

window.addEventListener('custody-approved', (event) => {
    window.notificationSystem.showCriticalAlert(
        `تم اعتماد العهدة رقم: ${event.detail.custody}`,
        'success'
    );
});