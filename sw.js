importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.6.1/workbox-sw.js');

const version = "v1.0.3";
const blackList = [
  'webadmin',
  'checkout',
  'customer'
];

config = {};
loadCachedConfig();

self.addEventListener('install', async e => {
  config = await loadConfigs(true);
  loadPreCache();
  return self.skipWaiting();
});

workbox.core.setCacheNameDetails({
  prefix: config.cachePrefix,
  suffix: config.cacheSuffix,
  precache: 'precache-'+config.cachePrefix,
  runtime: 'runtime-'+config.cachePrefix
}); 

workbox.routing.registerRoute(
  /.*\.js/,
  workbox.strategies.networkFirst({
    cacheName: 'js-cache',
    plugins: [
      new workbox.expiration.Plugin({
        maxAgeSeconds: config.cacheMaxAgeSeconds,
      }),
    ]
  })
);

workbox.routing.registerRoute(
  /.*\.css/,
  workbox.strategies.staleWhileRevalidate({
    cacheName: 'css-cache',
    plugins: [
      new workbox.expiration.Plugin({
        maxAgeSeconds: config.cacheMaxAgeSeconds,
      }),
    ]
  })
);

workbox.routing.registerRoute(
  /.*\.(?:png|jpg|jpeg|svg|gif|ico)/g,
  workbox.strategies.staleWhileRevalidate({
    cacheName: 'image-cache',
    plugins: [
      new workbox.expiration.Plugin({
        maxAgeSeconds: config.cacheMaxAgeSeconds,
      }),
    ]
  })
);

workbox.routing.registerRoute(
  /.*\.(?:ttf|otf|woff|woff2|eot)/g,
  workbox.strategies.staleWhileRevalidate({
    cacheName: 'font-cache',
    plugins: [
      new workbox.expiration.Plugin({
        maxAgeSeconds: config.cacheMaxAgeSeconds
      }),
    ]
  })
);

self.addEventListener('activate', e => {
  self.clients.claim();
});

self.addEventListener('fetch', async e => {
  const req = e.request;
  const url = new URL(req.url);
  const destination = req.destination;
  var response;
  var fetchPermited = true;

  if(config === {})
  {
    await loadCachedConfig();
    console.log('Config loaded on fetch!');
  }

  blackList.forEach(tag => {
    if(req.url.includes(tag)){
      fetchPermited = false;
    }
  });

  if (fetchPermited && (url.origin === location.origin && 
    req.method === 'GET' && 
    req.headers.get('accept').includes('text/html') && 
    destination === 'document'
  )) {
    e.waitUntil(
      e.respondWith( networkAndCache(req) )
    );
  }
  return e;
});

self.addEventListener('sync', e => {
  if(e.tag !== config.cacheSuffix)
  {
    flushCache(e);
    console.log('Cache cleared!');
  }
});

async function flushCache(e)
{
  e.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          return caches.delete(cacheName);
        })
      );
    })
  );

  config = await loadConfigs(true);
  await loadPreCache();
}

async function loadPreCache()
{
  const offFetch = await fetch(createCacheBustedRequest(config.staticAssets.offline));
  const offCache = await caches.open(config.cacheName.offline);
  await offCache.put(config.staticAssets.offline, offFetch);

  const cache = await caches.open(config.cacheName.default);
  await cache.addAll(config.staticAssets.default);
}

async function networkAndCache(req) {
  const cache = await caches.open(config.cacheName.default);
  try {
    const fresh = await fetch(req);
    await cache.put(req, fresh.clone());
    return fresh;
  } catch (e) {
    console.log('Fetch failed!', e);
    const cached = await cache.match(req).then( response => {
      return response || fallBackCache();
    });
    return cached;
  }
}

async function fallBackCache()
{
  console.log('Loaded FallBack Cache instead!');
  const cache = await caches.open(config.cacheName.offline);
  const cached = await cache.match(config.staticAssets.offline);
  return cached;
}

function createCacheBustedRequest(url)
{
  let request = new Request(url, {cache: 'reload'});
  if ('cache' in request) {
    return request;
  }

  let bustedUrl = new URL(url, self.location.href);
  bustedUrl.search += (bustedUrl.search ? '&' : '') + 'cachebust=' + Date.now();
  return new Request(bustedUrl);
}

async function loadConfigs(install = false)
{
  const configSetup = {
    configUrl: '/pwa/config/cache',
    configCacheName: 'config-cache'
  };
  const cache = await caches.open(configSetup.configCacheName);

  try {
    const fresh = await fetch(configSetup.configUrl);
    const clone = fresh.clone();
    if(fresh.status !== 200)
    {
      throw new Error('Looks like there was a problem. Status Code: ' + fresh.status);
    }
    const data = await fresh.json();
    try{
      console.log('Config cached');
      await cache.put(configSetup.configUrl, clone);
    } catch(e) {
      console.log(e);
    }

    return data;
  } catch(e) {
    console.log(e);
    if(!install){
      const cached = await cache.match(configSetup.configUrl);
      return await cached.json();
    }
    throw new Error(e);
  }
}

async function loadCachedConfig()
{
  const configSetup = {
    configUrl: '/pwa/config/cache',
    configCacheName: 'config-cache'
  };
  const cache = await caches.open(configSetup.configCacheName);
  cached = await cache.match(configSetup.configUrl);
  if(cached)
  {
    config = await cached.json();    
  }
}