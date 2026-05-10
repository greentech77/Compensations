<template>
    <div v-if="links.length > 3">
        <div class="-mb-1 flex flex-wrap gap-1 sm:gap-0">
            <template v-for="(link, p) in links" :key="p">
                <div
                    v-if="link.url === null"
                    class="mb-1 rounded border px-2 py-2 text-xs leading-4 text-gray-400 sm:mr-1 sm:px-4 sm:py-3 sm:text-sm"
                    v-html="link.label"
                />
                <Link
                    v-else
                    class="mb-1 rounded border px-2 py-2 text-xs leading-4 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-orange focus-visible:ring-offset-1 sm:mr-1 sm:px-4 sm:py-3 sm:text-sm"
                    :class="{ 'bg-blue text-white hover:bg-blue': link.active }"
                    :href="link.url"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
<script>

import { Link } from '@inertiajs/vue3';

    export default {

        components: {
            Link
        },
        props: {
            links: Array
        },
    }
</script>
