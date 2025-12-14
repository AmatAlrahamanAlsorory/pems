class OfflineManager {
    constructor() {
        this.db = null;
        this.isOnline = navigator.onLine;
        this.init();
    }

    async init() {
        await this.initDB();
        this.registerServiceWorker();
        this.setupEventListeners();
        await this.cacheEssentialData();
    }

    async initDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('PEMS_DB', 1);
            
            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve();
            };
            
            request.onupgradeneeded = event => {
                const db = event.target.result;
                
                if (!db.objectStoreNames.contains('offline_expenses')) {
                    const store = db.createObjectStore('offline_expenses', { keyPath: 'id' });
                    store.createIndex('timestamp', 'timestamp');
                }
                
                if (!db.objectStoreNames.contains('categories')) {
                    db.createObjectStore('categories', { keyPath: 'id' });
                }
                
                if (!db.objectStoreNames.contains('custodies')) {
                    db.createObjectStore('custodies', { keyPath: 'id' });
                }
            };
        });
    }

    registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('Service Worker مسجل بنجاح');
                    
                    // تسجيل للمزامنة الخلفية
                    if ('sync' in window.ServiceWorkerRegistration.prototype) {
                        registration.sync.register('sync-expenses');
                    }
                })
                .catch(error => console.error('فشل تسجيل Service Worker:', error));
        }
    }

    setupEventListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.updateConnectionStatus();
            this.syncOfflineData();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.updateConnectionStatus();
        });
    }

    updateConnectionStatus() {
        const statusElement = document.getElementById('connection-status');
        if (statusElement) {
            statusElement.className = this.isOnline ? 'online' : 'offline';
            statusElement.textContent = this.isOnline ? 'متصل' : 'غير متصل';
        }
    }

    async cacheEssentialData() {
        if (this.isOnline) {
            try {
                // تخزين الفئات محلياً
                const categories = await fetch('/api/categories').then(r => r.json());
                await this.storeData('categories', categories);
                
                // تخزين العهد النشطة
                const custodies = await fetch('/api/custodies/active').then(r => r.json());
                await this.storeData('custodies', custodies);
            } catch (error) {
                console.error('فشل في تخزين البيانات الأساسية:', error);
            }
        }
    }

    async storeData(storeName, data) {
        const tx = this.db.transaction([storeName], 'readwrite');
        const store = tx.objectStore(storeName);
        
        if (Array.isArray(data)) {
            for (const item of data) {
                await store.put(item);
            }
        } else {
            await store.put(data);
        }
    }

    async saveExpenseOffline(expenseData) {
        const expense = {
            ...expenseData,
            id: Date.now(),
            timestamp: new Date().toISOString(),
            synced: false
        };

        const tx = this.db.transaction(['offline_expenses'], 'readwrite');
        await tx.objectStore('offline_expenses').add(expense);
        
        this.showOfflineNotification('تم حفظ المصروف محلياً. سيتم رفعه عند عودة الاتصال.');
        return expense;
    }

    async getOfflineExpenses() {
        const tx = this.db.transaction(['offline_expenses'], 'readonly');
        return await tx.objectStore('offline_expenses').getAll();
    }

    async syncOfflineData() {
        const offlineExpenses = await this.getOfflineExpenses();
        
        for (const expense of offlineExpenses) {
            try {
                const response = await fetch('/api/expenses/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(expense)
                });

                if (response.ok) {
                    // حذف البيانات المحلية بعد المزامنة
                    const tx = this.db.transaction(['offline_expenses'], 'readwrite');
                    await tx.objectStore('offline_expenses').delete(expense.id);
                }
            } catch (error) {
                console.error('فشل في مزامنة المصروف:', error);
            }
        }
        
        if (offlineExpenses.length > 0) {
            this.showOfflineNotification('تم مزامنة البيانات المحفوظة محلياً');
        }
    }

    showOfflineNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'offline-notification';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => notification.remove(), 5000);
    }

    async getCachedData(storeName) {
        const tx = this.db.transaction([storeName], 'readonly');
        return await tx.objectStore(storeName).getAll();
    }
}

// تهيئة مدير العمل أوفلاين
const offlineManager = new OfflineManager();
window.offlineManager = offlineManager;