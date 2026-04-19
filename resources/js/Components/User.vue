<template>

    <div class="relative" v-if="user">
        <Menu>
            <MenuButton as="button" class="flex items-center font-medium hover:text-blue-hover hover:bg-gray-200 p-2 rounded-md">
                <UserCircleIcon class="h-6 w-6 mr-3"/> 
                <span>{{user.name}}</span>
                <ChevronDownIcon class="h-4 w-4 ml-3"/>
            </MenuButton>
            <MenuItems class="absolute z-10 mt-3 w-full bg-white shadow-lg rounded-md p-3 ring-1 ring-black ring-opacity-5 focus:outline-none">
                <MenuItem v-if="settingsPath">
                    <Link :href="settingsPath" class="hover:text-blue-hover">
                        <span class="flex items-center">
                            <CogIcon class="h-4 w-4 ml-3"/> Nastavitve
                        </span>
                    </Link>
                </MenuItem>
                <MenuItem>
                    <Link :href="logoutUrl" method="post" class="hover:text-blue-hover">
                        <span class="flex items-center">
                            <LogoutIcon class="h-4 w-4 mr-3"/> Logout
                        </span>
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

    mounted() {
        // console.log(this);
    }
    
}
</script>