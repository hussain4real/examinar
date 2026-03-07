import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import type { DefineComponent, Plugin } from 'vue';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => (title ? `${title} - ${appName}` : appName),
        resolve: (name) => {
            const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');
            return pages[`./Pages/${name}.vue`]();
        },
        setup: ({ App, props, plugin }: { el: null; App: any; props: any; plugin: Plugin }) =>
            createSSRApp({ render: () => h(App, props) }).use(plugin),
    }),
);
