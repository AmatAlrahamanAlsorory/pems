class EnhancedOfflineManager {
    constructor() {
        this.db = null;
        this.isOnline = navigator.onLine;
        this.syncQueue = [];
        this.retryAttempts = 0;
        this.maxRetries = 5;
        this.connectionQuality = 'good';
        this.syncInProgress = false;
        this.init();
    }

    async init() {
        await this.initDB();
        this.registerServiceWorker();
        this.setupEventListeners();
        await this.cacheEssentialData();
        this.startConnectionMonitoring();
        this.setupPeriodicSync();
        this.setupBandwidthOptimization();
    }

    async initDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('PEMS_ENHANCED_DB', 2);
            
            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve();
            };
            
            request.onupgradeneeded = event => {
                const db = event.target.result;
                
                // متاجر البيانات الأساسية
                if (!db.objectStoreNames.contains('offline_expenses')) {
                    const store = db.createObjectStore('offline_expenses', { keyPath: 'id' });
                    store.createIndex('timestamp', 'timestamp');
                    store.createIndex('priority', 'priority');
                }
                
                if (!db.objectStoreNames.contains('sync_queue')) {
                    const syncStore = db.createObjectStore('sync_queue', { keyPath: 'id', autoIncrement: true });
                    syncStore.createIndex('timestamp', 'timestamp');
                    syncStore.createIndex('priority', 'priority');
                    syncStore.createIndex('attempts', 'attempts');
                }
                
                if (!db.objectStoreNames.contains('failed_syncs')) {
                    const failedStore = db.createObjectStore('failed_syncs', { keyPath: 'id', autoIncrement: true });
                    failedStore.createIndex('failed_at', 'failed_at');
                }
                
                if (!db.objectStoreNames.contains('compressed_data')) {
                    db.createObjectStore('compressed_data', { keyPath: 'key' });
                }
                
                if (!db.objectStoreNames.contains('connection_logs')) {
                    const logStore = db.createObjectStore('connection_logs', { keyPath: 'id', autoIncrement: true });
                    logStore.createIndex('timestamp', 'timestamp');
                }
            };
        });
    }

    setupEventListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.retryAttempts = 0;
            this.logConnectionEvent('online');
            this.updateConnectionStatus();
            this.processOfflineQueue();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.logConnectionEvent('offline');
            this.updateConnectionStatus();
        });
        
        // مراقبة جودة الاتصال
        if ('connection' in navigator) {
            navigator.connection.addEventListener('change', () => {
                this.updateConnectionQuality();
            });
        }
        
        // مراقبة استخدام البطارية
        if ('getBattery' in navigator) {
            navigator.getBattery().then(battery => {
                battery.addEventListener('levelchange', () => {
                    this.adjustSyncFrequency(battery.level);
                });
            });
        }
    }

    updateConnectionStatus() {
        const statusElement = document.getElementById('connection-status');
        if (statusElement) {
            let statusText = 'غير متصل';
            let className = 'offline';
            
            if (this.isOnline) {
                switch (this.connectionQuality) {
                    case 'slow':
                        statusText = 'اتصال ضعيف';
                        className = 'slow';
                        break;
                    case 'medium':
                        statusText = 'اتصال متوسط';
                        className = 'medium';
                        break;
                    default:
                        statusText = 'متصل';
                        className = 'online';
                }
            }
            
            statusElement.className = className;
            statusElement.textContent = statusText;
        }
        
        this.updateSyncStrategy();
    }

    async saveExpenseOffline(expenseData) {
        const expense = {
            ...expenseData,
            id: Date.now(),
            timestamp: new Date().toISOString(),
            synced: false,
            priority: this.calculatePriority(expenseData),
            compressed: await this.compressData(expenseData)
        };

        const tx = this.db.transaction(['offline_expenses'], 'readwrite');
        await tx.objectStore('offline_expenses').add(expense);
        
        await this.addToSyncQueue({
            type: 'expense',
            data: expense,
            endpoint: '/api/expenses/sync',
            priority: expense.priority,
            size: JSON.stringify(expense).length
        });
        
        this.showOfflineNotification('تم حفظ المصروف محلياً. سيتم رفعه عند تحسن الاتصال.', 'success');
        return expense;
    }

    calculatePriority(expenseData) {
        let priority = 'normal';
        
        if (expenseData.amount > 50000) priority = 'high';
        else if (expenseData.amount > 10000) priority = 'medium';
        
        if (expenseData.category_id === 901) priority = 'urgent'; // طوارئ طبية
        
        return priority;
    }

    async compressData(data) {
        // ضغط البيانات للمناطق ضعيفة الاتصال
        const jsonString = JSON.stringify(data);
        
        if ('CompressionStream' in window) {
            const stream = new CompressionStream('gzip');
            const writer = stream.writable.getWriter();
            const reader = stream.readable.getReader();
            
            writer.write(new TextEncoder().encode(jsonString));
            writer.close();
            
            const chunks = [];
            let done = false;
            
            while (!done) {
                const { value, done: readerDone } = await reader.read();
                done = readerDone;
                if (value) chunks.push(value);
            }
            
            return new Uint8Array(chunks.reduce((acc, chunk) => [...acc, ...chunk], []));
        }
        
        return jsonString; // fallback
    }

    async processOfflineQueue() {
        if (!this.isOnline || this.syncInProgress) return;
        
        this.syncInProgress = true;
        
        try {
            const queueItems = await this.getSyncQueue();
            
            // ترتيب حسب الأولوية والحجم
            queueItems.sort((a, b) => {
                const priorityOrder = { 'urgent': 4, 'high': 3, 'medium': 2, 'normal': 1, 'low': 0 };
                const priorityDiff = priorityOrder[b.priority] - priorityOrder[a.priority];
                
                if (priorityDiff !== 0) return priorityDiff;
                
                // إذا كان الاتصال ضعيف، أعطي أولوية للملفات الأصغر
                if (this.connectionQuality === 'slow') {
                    return a.size - b.size;
                }
                
                return a.timestamp - b.timestamp;
            });
            
            const batchSize = this.getBatchSize();
            const batch = queueItems.slice(0, batchSize);
            
            for (const item of batch) {
                try {
                    await this.syncItemWithRetry(item);
                    await this.removeFromSyncQueue(item.id);
                    this.retryAttempts = 0;
                } catch (error) {
                    console.error('فشل في مزامنة العنصر:', error);
                    await this.handleSyncFailure(item);
                }
            }
            
            if (batch.length > 0) {
                this.showOfflineNotification(`تم مزامنة ${batch.length} عنصر`, 'success');
            }
        } finally {
            this.syncInProgress = false;
        }
    }

    getBatchSize() {
        switch (this.connectionQuality) {
            case 'slow': return 1;
            case 'medium': return 3;
            case 'good': return 5;
            default: return 1;
        }
    }

    async syncItemWithRetry(item) {
        const maxAttempts = 3;
        let lastError;
        
        for (let attempt = 1; attempt <= maxAttempts; attempt++) {
            try {
                await this.syncItem(item);
                return;
            } catch (error) {
                lastError = error;
                
                if (attempt < maxAttempts) {
                    const delay = Math.pow(2, attempt) * 1000; // exponential backoff
                    await new Promise(resolve => setTimeout(resolve, delay));
                }
            }
        }
        
        throw lastError;
    }

    async syncItem(item) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.getTimeout());
        
        try {
            const response = await fetch(item.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(item.data),
                signal: controller.signal
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            return await response.json();
        } finally {
            clearTimeout(timeoutId);
        }
    }

    getTimeout() {
        switch (this.connectionQuality) {
            case 'slow': return 30000; // 30 ثانية
            case 'medium': return 15000; // 15 ثانية
            default: return 10000; // 10 ثواني
        }
    }

    startConnectionMonitoring() {
        setInterval(() => {
            this.checkConnectionQuality();
        }, 30000);
    }

    async checkConnectionQuality() {
        if (!this.isOnline) return;
        
        const startTime = Date.now();
        try {
            const response = await fetch('/api/ping', {
                method: 'HEAD',
                cache: 'no-cache'
            });
            
            const responseTime = Date.now() - startTime;
            
            if (responseTime > 5000) {
                this.connectionQuality = 'slow';
            } else if (responseTime > 2000) {
                this.connectionQuality = 'medium';
            } else {
                this.connectionQuality = 'good';
            }
            
            await this.logConnectionQuality(responseTime);
        } catch (error) {
            this.connectionQuality = 'poor';
        }
        
        this.updateConnectionStatus();
    }

    async logConnectionEvent(event) {
        const tx = this.db.transaction(['connection_logs'], 'readwrite');
        await tx.objectStore('connection_logs').add({
            event,
            timestamp: Date.now(),
            quality: this.connectionQuality
        });
    }

    async logConnectionQuality(responseTime) {
        const tx = this.db.transaction(['connection_logs'], 'readwrite');
        await tx.objectStore('connection_logs').add({
            event: 'quality_check',
            timestamp: Date.now(),
            response_time: responseTime,
            quality: this.connectionQuality
        });
    }

    setupPeriodicSync() {
        // تكييف تكرار المزامنة حسب جودة الاتصال
        const getSyncInterval = () => {
            switch (this.connectionQuality) {
                case 'slow': return 300000; // 5 دقائق
                case 'medium': return 120000; // دقيقتان
                default: return 60000; // دقيقة واحدة
            }
        };
        
        const scheduleNextSync = () => {
            setTimeout(() => {
                if (this.isOnline && !this.syncInProgress) {
                    this.processOfflineQueue();
                }
                scheduleNextSync();
            }, getSyncInterval());
        };
        
        scheduleNextSync();
    }

    setupBandwidthOptimization() {
        // تحسين استخدام النطاق الترددي
        if ('connection' in navigator) {
            const connection = navigator.connection;
            
            if (connection.saveData) {
                // تفعيل وضع توفير البيانات
                this.enableDataSaver();
            }
        }
    }

    enableDataSaver() {
        // تقليل حجم البيانات المرسلة
        this.dataSaverMode = true;
        this.showOfflineNotification('تم تفعيل وضع توفير البيانات', 'info');
    }

    adjustSyncFrequency(batteryLevel) {
        if (batteryLevel < 0.2) {
            // تقليل تكرار المزامنة عند انخفاض البطارية
            this.lowPowerMode = true;
            this.showOfflineNotification('تم تفعيل وضع توفير الطاقة', 'warning');
        } else {
            this.lowPowerMode = false;
        }
    }

    updateSyncStrategy() {
        // تحديث استراتيجية المزامنة حسب الظروف
        if (this.connectionQuality === 'slow' || this.lowPowerMode) {
            // مزامنة العناصر عالية الأولوية فقط
            this.priorityOnlySync = true;
        } else {
            this.priorityOnlySync = false;
        }
    }

    showOfflineNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `offline-notification ${type}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="ml-2">${this.getNotificationIcon(type)}</span>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="mr-auto text-white hover:text-gray-200">
                    ×
                </button>
            </div>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, type === 'error' ? 10000 : 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            'info': 'ℹ️',
            'success': '✅',
            'warning': '⚠️',
            'error': '❌'
        };
        return icons[type] || icons.info;
    }

    async addToSyncQueue(item) {
        const queueItem = {
            ...item,
            timestamp: Date.now(),
            attempts: 0
        };
        
        const tx = this.db.transaction(['sync_queue'], 'readwrite');
        await tx.objectStore('sync_queue').add(queueItem);
    }

    async getSyncQueue() {
        const tx = this.db.transaction(['sync_queue'], 'readonly');
        const items = await tx.objectStore('sync_queue').getAll();
        
        if (this.priorityOnlySync) {
            return items.filter(item => ['urgent', 'high'].includes(item.priority));
        }
        
        return items;
    }

    async removeFromSyncQueue(id) {
        const tx = this.db.transaction(['sync_queue'], 'readwrite');
        await tx.objectStore('sync_queue').delete(id);
    }

    async handleSyncFailure(item) {
        item.attempts = (item.attempts || 0) + 1;
        
        if (item.attempts >= this.maxRetries) {
            const tx = this.db.transaction(['failed_syncs'], 'readwrite');
            await tx.objectStore('failed_syncs').add({
                ...item,
                failed_at: Date.now(),
                error: 'Max retries exceeded'
            });
            
            await this.removeFromSyncQueue(item.id);
            this.showOfflineNotification('فشل في مزامنة بعض البيانات. تحقق من الاتصال.', 'error');
        } else {
            // تحديث عدد المحاولات
            const tx = this.db.transaction(['sync_queue'], 'readwrite');
            await tx.objectStore('sync_queue').put(item);
        }
    }

    async getOfflineStats() {
        const queueItems = await this.getSyncQueue();
        const failedItems = await this.getFailedSyncs();
        const connectionLogs = await this.getRecentConnectionLogs();
        
        return {
            pending_sync: queueItems.length,
            failed_sync: failedItems.length,
            connection_quality: this.connectionQuality,
            is_online: this.isOnline,
            data_saver_mode: this.dataSaverMode || false,
            low_power_mode: this.lowPowerMode || false,
            recent_connection_events: connectionLogs.length
        };
    }

    async getFailedSyncs() {
        const tx = this.db.transaction(['failed_syncs'], 'readonly');
        return await tx.objectStore('failed_syncs').getAll();
    }

    async getRecentConnectionLogs() {
        const tx = this.db.transaction(['connection_logs'], 'readonly');
        const index = tx.objectStore('connection_logs').index('timestamp');
        const range = IDBKeyRange.lowerBound(Date.now() - 3600000); // آخر ساعة
        return await index.getAll(range);
    }

    async cacheEssentialData() {
        if (this.isOnline) {
            try {
                const categories = await fetch('/api/categories').then(r => r.json());
                await this.storeData('categories', categories);
                
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
}

// تهيئة مدير العمل أوفلاين المحسن
const enhancedOfflineManager = new EnhancedOfflineManager();
window.enhancedOfflineManager = enhancedOfflineManager;