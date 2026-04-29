import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel({
                input: ['resources/js/app.js'],
                refresh: [
                    'resources/views/**',
                    'routes/**',
                ],
            }),
        ],
        server: {
            host: env.VITE_DEV_HOST || '127.0.0.1',
            port: Number(env.VITE_DEV_PORT) || 5173,
            strictPort: true,
            cors: true,
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
