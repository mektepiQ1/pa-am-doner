const CACHE_NAME = 'pasam-doner-v1.0.1';
const urlsToCache = [
  '/pa-am-doner/p15.html',
  '/pa-am-doner/manifest.json',
  '/pa-am-doner/flor.jpg'
];

// Kurulum
self.addEventListener('install', function(event) {
  console.log('Service Worker installing...');
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

// Aktivasyon
self.addEventListener('activate', function(event) {
  console.log('Service Worker activating...');
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
      console.log('Service Worker aktif');
      return self.clients.claim();
    })
  );
});

// Fetch olayları
self.addEventListener('fetch', function(event) {
  // Sadece GET isteklerini işle
  if (event.request.method !== 'GET') return;
  
  event.respondWith(
    caches.match(event.request)
    .then(function(response) {
      // Önbellekte bulunursa, önbellekten döndür
      if (response) {
        console.log('Önbellekten servis ediliyor:', event.request.url);
        return response;
      }
      
      // Aksi halde ağdan getir
      console.log('Ağdan getiriliyor:', event.request.url);
      return fetch(event.request).then(function(response) {
        // Geçersiz yanıt kontrolü
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }
        
        // Yanıtı klonla (stream sadece bir kez okunabilir)
        var responseToCache = response.clone();
        
        caches.open(CACHE_NAME)
          .then(function(cache) {
            cache.put(event.request, responseToCache);
            console.log('Yeni kaynak önbelleğe alındı:', event.request.url);
          });
        
        return response;
      }).catch(function(error) {
        console.log('Fetch hatası:', error);
        // Hata durumunda fallback sayfası gösterebilirsiniz
      });
    })
  );
});

// Mesajları dinle
self.addEventListener('message', function(event) {
  if (event.data === 'skipWaiting') {
    self.skipWaiting();
  }
});