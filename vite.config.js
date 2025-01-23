import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa'
import path from 'path'

export default defineConfig({
    resolve: {
        alias: {
            'ziggy-js': path.resolve('vendor/tightenco/ziggy'),
        },
    },
    build: {
        extend(config) {
            config.module.rules.push({
                test: /\.mjs$/,
                include: /node_modules/,
                type: "javascript/auto"
            });
        }
    },
    plugins: [
        VitePWA({
            registerType: 'autoUpdate',
        }),
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
