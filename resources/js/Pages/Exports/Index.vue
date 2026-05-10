<template>
    <Head title="Izvozi" />

    <div class="w-full rounded-md bg-stone-15 p-4 md:p-8">
        <h1 class="text-2xl font-bold mb-6">Izvozi</h1>
        <p class="text-gray-600 mb-8">
            Izberi tip izvoza in nadaljuj na ustrezno stran.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-md shadow-sm border border-stone">
                <h2 class="text-lg font-semibold mb-2">Računi</h2>
                <p class="text-sm text-gray-600 mb-4">XML izvoz računov po obdobju od/do.</p>
                <Button class="button button--stone" @click="go('exports.bills')">Odpri</Button>
            </div>

            <div class="bg-white p-6 rounded-md shadow-sm border border-stone">
                <h2 class="text-lg font-semibold mb-2">Pogodbe</h2>
                <p class="text-sm text-gray-600 mb-4">XML izvoz pogodb po datumskem obdobju.</p>
                <Button class="button button--stone" @click="go('exports.contracts')">Odpri</Button>
            </div>

            <div class="bg-white p-6 rounded-md shadow-sm border border-stone">
                <h2 class="text-lg font-semibold mb-2">Kompenzacije</h2>
                <p class="text-sm text-gray-600 mb-4">Statistika in XML izvoz kompenzacij po obdobju.</p>
                <Button class="button button--stone" @click="go('exports.compenzations')">Odpri</Button>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/mixins/adminLayout'
import Button from '@/Components/Button.vue'

export default {
    layout: AdminLayout,
    components: {
        Head,
        Button
    },
    methods: {
        go(routeName) {
            this.$inertia.get(this.route(routeName))
        }
    }
}
</script>
