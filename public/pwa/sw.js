const CACHE_NAME = 'futebolize-v1';

const FILES_TO_CACHE = [
  '/',
  'assets/css/app.min.css',
  'assets/js/app.min.js',
  '/pwa/manifest.json'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(FILES_TO_CACHE))
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.map(key => key !== CACHE_NAME && caches.delete(key))
      )
    )
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response =>
      response || fetch(event.request)
    )
  );
});

self.addEventListener('push', event => {
  const data = event.data.json();
  const options = {
    body: data.body,
    icon: data.icon,
    badge: '/pwa/icons/icon-96.png'
  };
  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});
