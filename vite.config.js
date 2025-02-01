import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa'
import vuetify from 'vite-plugin-vuetify'

export default defineConfig({
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
        vuetify()
    ],
});
