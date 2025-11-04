const CACHE_NAME = 'pasam-doner-v1.0.2';
const urlsToCache = [
  './',
  './p15.html',
  './manifest.json',
  './flor.jpg'
];

// Kurulum - NETWORK FIRST stratejisi
self.addEventListener('install', function(event) {
  console.log('Service Worker installing... v1.0.2');
  event.waitUntil(
    caches.open(CACHE_NAME)
    .then(function(cache) {
      console.log('Cache açıldı');
      return cache.addAll(urlsToCache);
    })
    .then(function() {
      console.log('Tüm kaynaklar önbelleğe alındı');
      return self.skipWaiting();
    })
  );
});

// Aktivasyon - Eski cache'leri temizle
self.addEventListener('activate', function(event) {
  console.log('Service Worker activating... v1.0.2');
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheName !== CACHE_NAME) {
            console.log('Eski cache siliniyor:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(function() {
      console.log('Service Worker aktif - v1.0.2');
      return self.clients.claim();
    })
  );
});

// NETWORK FIRST stratejisi - Her zaman ağdan al, cache fallback olarak kullan
self.addEventListener('fetch', function(event) {
  // Sadece GET isteklerini işle
  if (event.request.method !== 'GET') return;
  
  // HTML sayfaları için Network First stratejisi
  if (event.request.url.includes('.html') || event.request.url === self.location.origin + '/') {
    event.respondWith(
      fetch(event.request)
      .then(function(response) {
        // Başarılı response'u cache'e kaydet
        const responseClone = response.clone();
        caches.open(CACHE_NAME)
          .then(function(cache) {
            cache.put(event.request, responseClone);
          });
        return response;
      })
      .catch(function() {
        // Ağ hatası durumunda cache'ten servis et
        return caches.match(event.request);
      })
    );
  } else {
    // Diğer kaynaklar (resimler, manifest vb.) için Cache First
    event.respondWith(
      caches.match(event.request)
      .then(function(response) {
        return response || fetch(event.request);
      })
    );
  }
});

// Versiyon kontrolü için mesaj işleme
self.addEventListener('message', function(event) {
  if (event.data === 'skipWaiting') {
    self.skipWaiting();
  }
  
  if (event.data === 'clearCache') {
    caches.delete(CACHE_NAME)
      .then(function() {
        console.log('Cache temizlendi');
      });
  }
});