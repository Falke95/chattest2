self.addEventListener('install', function(e) {
e.waitUntil(caches.open('blabstore').then(function(cache){
return cache.addAll([

'app/network.html',
'app/512.png',
'app/256.png',
'app/128.png'
     
])}))})

self.addEventListener('fetch',function(e) {
e.respondWith(caches.match(e.request).then(function(response){return response || fetch(e.request)}))})