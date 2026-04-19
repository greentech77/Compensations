require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createI18n } from 'vue-i18n';
import { get as getLocale } from './services/locale';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    progress: {
        color: '#4B5563',
    },
    resolve: (name) => {
        const pages = require.context('./Pages', true, /\.vue$/);
        return pages(`./${name}.vue`);
    },
    async setup({ el, App, props, plugin }) {
        const data = await getLocale();
        localStorage.setItem('locale', data.locale);
        const i18n = createI18n({ ...data });

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .mixin({ methods: { route } })
            .mount(el);
    },
});
