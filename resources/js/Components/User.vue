<template>

    <div class="relative" v-if="user">
        <Menu>
            <MenuButton
                as="button"
                class="flex min-h-[44px] min-w-0 max-w-[min(11rem,46vw)] items-center rounded-md p-2 text-left font-medium hover:bg-gray-200 hover:text-blue-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-orange focus-visible:ring-offset-2"
            >
                <UserCircleIcon class="mr-2 h-6 w-6 shrink-0" aria-hidden="true" />
                <span class="min-w-0 truncate">{{ user.name }}</span>
                <ChevronDownIcon class="ml-1 h-4 w-4 shrink-0" aria-hidden="true" />
            </MenuButton>
            <MenuItems
                class="absolute right-0 z-50 mt-2 max-h-[min(70vh,24rem)] w-56 min-w-[12rem] max-w-[calc(100vw-1rem)] origin-top-right overflow-auto rounded-md bg-white p-2 shadow-lg ring-1 ring-black/5 focus:outline-none"
            >
                <MenuItem v-if="settingsUrl" v-slot="{ active }">
                    <Link
                        :href="settingsUrl"
                        class="flex min-h-[44px] items-center rounded-md px-3 py-2 text-sm"
                        :class="active ? 'bg-stone-15 text-blue' : 'text-gray-900'"
                    >
                        <CogIcon class="mr-3 h-4 w-4 shrink-0" aria-hidden="true" />
                        Nastavitve
                    </Link>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                    <Link
                        :href="logoutUrl"
                        method="post"
                        class="flex min-h-[44px] w-full items-center rounded-md px-3 py-2 text-left text-sm"
                        :class="active ? 'bg-stone-15 text-blue' : 'text-gray-900'"
                    >
                        <LogoutIcon class="mr-3 h-4 w-4 shrink-0" aria-hidden="true" />
                        Logout
                    </Link>
                </MenuItem>
            </MenuItems>
        </Menu>
    </div>

</template>

<script>
import { UserCircleIcon, ChevronDownIcon, LogoutIcon, CogIcon } from '@heroicons/vue/outline';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { Link } from '@inertiajs/vue3'

export default {

    components: {
        UserCircleIcon,
        ChevronDownIcon,
        Menu,
        MenuButton,
        MenuItems,
        MenuItem,
        Link,
        LogoutIcon,
        CogIcon
    },

    props: {
        settingsUrl: String,
        logoutUrl: String
    },

    computed: {
        user() {
            return this.$page.props.auth.user
        }
    },

}
</script>
