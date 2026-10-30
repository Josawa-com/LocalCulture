import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

/*
|--------------------------------------------------------------------------
| Host dev server
|--------------------------------------------------------------------------
| Default: 127.0.0.1 (hanya bisa dibuka dari komputer ini).
| Untuk membuka dari HP / perangkat lain di jaringan yang sama, jalankan:
|
|     DEV_HOST=0.0.0.0 npm run dev
|
| atau pakai skrip: ./dev.sh --lan
*/
const devHost = process.env.DEV_HOST || '127.0.0.1';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
                bunny('Fraunces', {
                    weights: [500, 600, 700],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: devHost,
        // Port tetap agar public/hot selalu menunjuk ke alamat yang benar.
        port: 5173,
        strictPort: true,
        // Saat diakses lewat IP LAN, HMR juga harus menunjuk ke IP itu.
        hmr: devHost === '0.0.0.0'
            ? { host: process.env.DEV_HMR_HOST || 'localhost' }
            : undefined,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
