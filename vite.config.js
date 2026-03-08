import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/vn-app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // TAMBAHKAN BLOK SERVER INI:
    server: {
        host: '0.0.0.0', // Membuka akses agar Vite bisa diakses dari HP
        port: 5173,
        hmr: {
            host: '192.168.1.233', // Wajib diganti dengan IP komputermu!
        },
    },
});
