import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: ['favicon.ico','icon-192.png','icon-512.png','manifest.json'],
            manifest: false, // use public/manifest.json
            filename: 'sw.js',
            workbox: {
                runtimeCaching: [
                    {
                        urlPattern: /\/api\/.*$/,
                        handler: 'NetworkFirst',
                        options: { cacheName: 'api-cache' }
                    }
                ]
            },
        }),
    ],
});
