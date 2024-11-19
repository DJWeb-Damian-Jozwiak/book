import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

console.log('Hello World');
createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })

        const page = Object.keys(pages)
            .find(path => path.toLocaleLowerCase()
                .includes(name.toLocaleLowerCase()))

        if (!page) {
            throw new Error(`Page ${name} not found. Available pages: ${Object.keys(pages).join(', ')}`)
        }

        return pages[page].default
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})