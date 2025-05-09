import '../css/app.css';

import "./bootstrap.js";

import BaseLayout from "@/Layouts/BaseLayout.vue";

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import VueTheMask from 'vue-the-mask'

// Vuetify
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'

const vuetify = createVuetify({
    theme: {
        defaultTheme: 'light',
    }
})

const appName = import.meta.env.VITE_APP_NAME || 'AviGrow';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        );

        page.then((module) => {
            module.default.layout ??= BaseLayout;
        });

        return page;
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(vuetify)
            .use(plugin)
            .use(VueTheMask)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
