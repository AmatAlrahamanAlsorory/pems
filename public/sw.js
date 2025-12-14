const CACHE_NAME = 'pems-v1';
const urlsToCache = [
  '/',
  '/dashboard',
  '/expenses/create',
  '/custodies',
  '/css/app.css',
  '/js/app.js',
  '/offline.html'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) return response;
        
        return fetch(event.request).catch(() => {
          if (event.request.destination === 'document') {
            return caches.match('/offline.html');
          }
        });
      })
  );
});

// مزامنة البيانات عند عودة الاتصال
self.addEventListener('sync', event => {
  if (event.tag === 'sync-expenses') {
    event.waitUntil(syncOfflineExpenses());
  }
});

async function syncOfflineExpenses() {
  const db = await openDB();
  const tx = db.transaction(['offline_expenses'], 'readonly');
  const expenses = await tx.objectStore('offline_expenses').getAll();
  
  for (const expense of expenses) {
    try {
      await fetch('/api/expenses/sync', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(expense)
      });
      
      // حذف البيانات المحلية بعد المزامنة
      const deleteTx = db.transaction(['offline_expenses'], 'readwrite');
      await deleteTx.objectStore('offline_expenses').delete(expense.id);
    } catch (error) {
      console.error('فشل في مزامنة المصروف:', error);
    }
  }
}

function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('PEMS_DB', 1);
    
    request.onerror = () => reject(request.error);
    request.onsuccess = () => resolve(request.result);
    
    request.onupgradeneeded = event => {
      const db = event.target.result;
      
      if (!db.objectStoreNames.contains('offline_expenses')) {
        const store = db.createObjectStore('offline_expenses', { keyPath: 'id' });
        store.createIndex('timestamp', 'timestamp');
      }
      
      if (!db.objectStoreNames.contains('categories')) {
        db.createObjectStore('categories', { keyPath: 'id' });
      }
    };
  });
}