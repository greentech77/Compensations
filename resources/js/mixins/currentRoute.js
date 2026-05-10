import { router } from '@inertiajs/vue3'

export default {

    data() {
        return {
            currentRoute: ''
        }
    },

    created() {
        router.on('navigate', (event) => {
            this.currentRoute = this.route().current()
        })
    },

    mounted() {
        this.currentRoute = this.route().current()
    },

    methods: {
        activeRoute(name) {
            return this.currentRoute.startsWith(name); 
        }
    }

}