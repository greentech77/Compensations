<template>
    <nav class="w-full text-lg" aria-label="Glavna navigacija">
        <ul class="my-3 space-y-1 md:space-y-6">
            <li
                v-for="item in items"
                :key="item.routeName"
                class="flex items-center text-sm uppercase tracking-wider hover:text-gray-400 md:min-h-0"
                :class="[
                    { 'text-orange hover:text-orange-hover': activeRoute(item.activePrefix) },
                    mobile ? 'min-h-[44px]' : '',
                ]"
            >
                <Link
                    :href="route(item.routeName)"
                    class="inline-flex w-full touch-manipulation items-center py-3 md:py-0 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange"
                    :aria-current="activeRoute(item.activePrefix) ? 'page' : undefined"
                >
                    <component :is="item.icon" class="mr-4 inline-block h-6 w-6 shrink-0" aria-hidden="true" />
                    {{ item.label }}
                </Link>
            </li>
        </ul>
    </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import {
    HomeIcon,
    OfficeBuildingIcon,
    CurrencyEuroIcon,
    DocumentTextIcon,
    DocumentReportIcon,
} from '@heroicons/vue/outline'
import currentRoute from '@/mixins/currentRoute'

export default {
    mixins: [currentRoute],

    components: {
        Link,
        HomeIcon,
        OfficeBuildingIcon,
        CurrencyEuroIcon,
        DocumentTextIcon,
        DocumentReportIcon,
    },

    props: {
        mobile: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            items: [
                { routeName: 'dashboard', activePrefix: 'dashboard', label: 'Nadzorna plošča', icon: 'HomeIcon' },
                { routeName: 'entities', activePrefix: 'entities', label: 'Podjetja', icon: 'OfficeBuildingIcon' },
                { routeName: 'compenzations', activePrefix: 'compenzations', label: 'Kompenzacije', icon: 'CurrencyEuroIcon' },
                { routeName: 'bills', activePrefix: 'bills', label: 'Računi', icon: 'DocumentTextIcon' },
                { routeName: 'exports.index', activePrefix: 'exports', label: 'Izvozi', icon: 'DocumentReportIcon' },
            ],
        }
    },
}
</script>
