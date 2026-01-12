import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/admin/app.js',
                'resources/css/app.css',
                'resources/css/admin/app.css'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],

    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },

    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json',
        sourcemap: false,

        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor-common': ['axios'],
                    'ui-public': ['alpinejs', 'slim-select', 'flatpickr', 'sortablejs'],
                    'charts': ['apexcharts'],
                },
            },
        },

        chunkSizeWarningLimit: 600,
    },

    optimizeDeps: {
        include: ['slim-select', 'alpinejs', 'axios', 'apexcharts', 'flatpickr', 'sortablejs']
    },
});
