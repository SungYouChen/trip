const CACHE_NAME = 'elktrip-v3.0';
const STATIC_ASSETS = [
    '/icon_logo.png',
    '/favicon.ico',
];

// 1. 安裝：存入基本圖片
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
});

// 2. 激活：清理舊版本
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(keys.map((k) => k !== CACHE_NAME && caches.delete(k)));
        }).then(() => self.clients.claim())
    );
});

// 3. 獲取：【智慧快取策略】
self.addEventListener('fetch', (event) => {
    // 絕對保險：只處理 GET，排除登入、API、登出等敏感操作
    if (event.request.method !== 'GET') return;
    if (event.request.url.includes('/api/') || event.request.url.includes('/logout') || event.request.url.includes('/login')) return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // 如果網路正常，順便把這頁存起來 (如果是 HTML 或圖片)
                if (response && response.status === 200 && response.type === 'basic') {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, copy);
                    });
                }
                return response;
            })
            .catch(() => {
                // 【關鍵：沒網時！】從倉庫拿存好的
                return caches.match(event.request);
            })
    );
});
