importScripts(
    '/dist/workbox/workbox-v6.5.4/workbox-sw.js'
);

const {setCatchHandler, registerRoute, setDefaultHandler} = workbox.routing;
const {strategies} = workbox;
const {precacheAndRoute, matchPrecache} = workbox.precaching;
const {cacheNames} = workbox.core;

precacheAndRoute(self.__WB_MANIFEST);

const cacheName = cacheNames.runtime;

const urlArr = [
    '/fr',
    '/en'
];

const manifestURLs = urlArr.map((entry) => {
    // Create a full, absolute URL to make routing easier.
    const url = new URL(entry, self.location);
    return new Request(url.href);
});

self.addEventListener('install', (event) => {

    // const cache = await caches.open(cacheName);
    const populateCache = async () => {
        caches.open(cacheName).then((cache) => cache.addAll(manifestURLs))
    };

    event.waitUntil(populateCache());
});

for (const url of urlArr) {
    registerRoute(
        // new RegExp('/fr'),
        (new URL(url, self.location)).href,
        new strategies.NetworkFirst({cacheName, matchOptions : {
                ignoreVary: true
            }})
    );
}

registerRoute(
    new RegExp('/admin'),
    new strategies.NetworkOnly()
);

registerRoute(
    new RegExp('/admin/*'),
    new strategies.NetworkOnly()
);

setCatchHandler(async ({ url, request, event }) => {
    console.log(event)

    // Return the precached offline page if a document is being requested
    if (event.request.destination === 'document') {
        return matchPrecache('/');
    }

    return Response.error();
});
