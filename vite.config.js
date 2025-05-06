import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // server: {
    //     host: '0.0.0.0', // or your IP directly: '192.168.0.167'
    //     port: 5173,
    //     cors: {
    //         origin: 'http://192.168.0.167:8080',
    //         credentials: true,
    //     },
    //     strictPort: true,
    //     hmr: {
    //         host: '192.168.0.167' // 👈 important for hot reload and asset access
    //     }
    // },
});
