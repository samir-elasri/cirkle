importScripts(
    '/dist/workbox/workbox-v6.5.4/workbox-sw.js'
);

const {setCatchHandler, registerRoute, setDefaultHandler} = workbox.routing;
const {strategies} = workbox;
const {precacheAndRoute, matchPrecache} = workbox.precaching;
const {cacheNames} = workbox.core;

precacheAndRoute([{"revision":"731c0964718194b9ce90869927eb205a","url":"dist/compiled/main.js"},{"revision":"a3681b8621661bc0c3b0980211c9b06c","url":"dist/compiled/main.min.css"},{"revision":"83958d3c0066d09a2783447f6c3b4d78","url":"dist/compiled/main.prod.js"},{"revision":"4714b785a292c8e1c1398f34614995bb","url":"dist/compiled/wysiwyg.min.css"},{"revision":"7c86de9fa6d0aa038243f436f13f3660","url":"favicon.ico"}]);

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
