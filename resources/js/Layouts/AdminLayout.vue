<template>

    <div class="min-h-screen flex">

        <Sidebar class="hidden w-80 pt-[calc(var(--top-bar-height)*2+var(--main-bar-height))] md:block">
            <AdminNavLinks />
        </Sidebar>

        <div class="min-w-0 flex-auto">
            <header class="sticky top-0 z-20 md:mx-4 md:top-[calc(var(--top-bar-height)*-1)]">
                <div class="hidden h-[var(--top-bar-height)] bg-blue md:block md:rounded-b-md"></div>
                <div class="flex h-[var(--main-bar-height)] flex-row items-stretch border-b border-blue bg-white">
                    <div class="flex h-full w-full min-w-0 items-center px-2 sm:px-4">
                        <button
                            type="button"
                            class="mr-2 inline-flex min-h-[44px] min-w-[44px] shrink-0 items-center justify-center rounded-md text-blue hover:bg-stone-15 md:hidden focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange"
                            aria-label="Odpri meni"
                            :aria-expanded="mobileNavOpen"
                            aria-controls="admin-mobile-nav"
                            @click="mobileNavOpen = true"
                        >
                            <MenuIcon class="h-7 w-7" aria-hidden="true" />
                        </button>
                        <div class="flex min-w-0 flex-1 items-center sm:w-[210px] sm:flex-none">
                            <Link
                                :href="route('home')"
                                class="truncate py-2 text-blue hover:text-blue-hover focus-visible:rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange"
                            >
                                Domov
                            </Link>
                        </div>
                    </div>
                    <div class="flex h-full flex-none items-center pr-2 sm:pr-4">
                        <User :logout-url="route('logout')" />
                    </div>
                </div>
                <div
                    class="flex h-[var(--top-bar-height)] min-h-[var(--top-bar-height)] items-center gap-2 overflow-x-auto whitespace-nowrap border-b border-blue bg-white px-4 text-sm"
                >
                    <span class="shrink-0">
                        Administracija
                    </span>
                    <template v-for="item in breadcrumb" :key="item.label">
                        <ChevronRightIcon class="h-5 w-5 shrink-0 inline-block" aria-hidden="true" />
                        <Link
                            v-if="item.route"
                            :href="item.route"
                            class="shrink-0 hover:text-blue-hover focus-visible:rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange"
                        >
                            {{ item.label }}
                        </Link>
                        <span v-else class="shrink-0">
                            {{ item.label }}
                        </span>
                    </template>

                </div>
            </header>
            <MainBlock>
                <slot />
            </MainBlock>
        </div>

    </div>

    <AdminMobileDrawer :open="mobileNavOpen" @close="mobileNavOpen = false" />

    <Modal />
    <Toast />

</template>

<script>
import { Link } from '@inertiajs/vue3'
import Sidebar from '@/Layouts/Sidebar.vue'
import MainBlock from '@/Layouts/MainBlock.vue'
import AdminNavLinks from '@/Components/AdminNavLinks.vue'
import AdminMobileDrawer from '@/Components/AdminMobileDrawer.vue'
import { ChevronRightIcon, MenuIcon } from '@heroicons/vue/outline'
import User from '@/Components/User.vue'
import Modal from '@/Components/Modal.vue'
import Toast from '@/Components/Toast.vue'

export default {

    components: {
        Sidebar,
        MainBlock,
        AdminNavLinks,
        AdminMobileDrawer,
        Link,
        ChevronRightIcon,
        MenuIcon,
        User,
        Modal,
        Toast,
    },

    data() {
        return {
            mobileNavOpen: false,
        }
    },

    computed: {
        breadcrumb() {
            return this.$page.props.breadcrumb
        },
    },

}
</script>
