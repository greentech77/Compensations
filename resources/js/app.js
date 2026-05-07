import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp, router, useForm } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createI18n } from 'vue-i18n'
import { get as getLocale } from './services/locale'

// DEBUG: surface any initialization error to the DOM so we can see it even
// if the console is closed. Remove once the upgrade is stable.
const __bootDebug = (label, err) => {
    // eslint-disable-next-line no-console
    console.error(`[boot:${label}]`, err)
    const el = document.getElementById('app')
    if (el) {
        const pre = document.createElement('pre')
        pre.style.cssText = 'padding:16px;background:#fee;color:#900;font:12px/1.4 monospace;white-space:pre-wrap;border:1px solid #c00;margin:16px;'
        pre.textContent = `[boot:${label}] ${err && (err.stack || err.message || String(err))}`
        el.appendChild(pre)
    }
}
window.addEventListener('error', (e) => __bootDebug('window.error', e.error || e.message))
window.addEventListener('unhandledrejection', (e) => __bootDebug('unhandledrejection', e.reason))

const appName = document.title || 'Compenzations'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    progress: {
        color: '#4B5563',
    },
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    async setup({ el, App, props, plugin }) {
        let data
        try {
            data = await getLocale()
        } catch (err) {
            __bootDebug('getLocale', err)
            throw err
        }
        localStorage.setItem('locale', data.locale)

        let i18n
        try {
            i18n = createI18n({ legacy: true, globalInjection: true, ...data })
        } catch (err) {
            __bootDebug('createI18n', err)
            throw err
        }

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
        app.config.errorHandler = (err, instance, info) => {
            __bootDebug(`vue.errorHandler:${info}`, err)
        }

        // Backward-compat shim for legacy Options API pages.
        //
        // Inertia v1.3 installs `$inertia` as a read-only property (router
        // instance) via `app.config.globalProperties`. In Vue 3.5 overwriting
        // it throws "Cannot set property $inertia of #<Object> which has only
        // a getter". Instead, we augment the shared `router` object with the
        // `form` helper (Inertia <=1.2 exposed `this.$inertia.form()`), and
        // add a global `route()` mixin so pages keep working during the
        // upgrade. New code should import `router`/`useForm` + use the
        // globally-exposed `route()` from @routes directly.
        if (typeof router.form !== 'function') {
            router.form = (...args) => useForm(...args)
        }
        app.mixin({
            methods: {
                route(...args) {
                    return window.route(...args)
                },
            },
        })

        return app.mount(el)
    },
})
