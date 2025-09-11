import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // ✅ CORRECTION: Les DEUX points d'entrée
                'resources/js/app.js',        // Pages publiques
                'resources/js/admin/app.js',  // Admin
            ],
            refresh: [
                'resources/views/**/*.blade.php',
            ],
        }),
    ],
    
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json',
        sourcemap: false,
        
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor-common': ['axios'],
                    'ui-public': ['alpinejs', 'tom-select', 'flatpickr', 'sortablejs'],
                    'charts': ['apexcharts'],
                },
            },
        },
        
        chunkSizeWarningLimit: 600,
    },
    
    server: {
        hmr: { host: 'localhost' },
        watch: { usePolling: true, interval: 1000 },
    },
    
    css: {
        postcss: './postcss.config.js',
    },
});

