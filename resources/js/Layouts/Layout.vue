<template>
    <div class="min-h-screen flex flex-col">
        <header class="md:mx-4 sticky top-0 md:top-[calc(var(--top-bar-height)*-1)] z-20">
            <div class="flex flex-row items-stretch border-b border-blue h-[var(--main-bar-height)] bg-white">
                <div class="h-full px-4 flex max-w-7xl mx-auto w-full">
                    <div class="h-full flex items-center w-[210px]">
                        <Link :href="route('home')">
                            <!--img src="@/../images/BTN-logo.svg" alt="BTNaložbe logo"-->
                        </Link>
                    </div>
                    <nav class="flex flex-auto flex-row justify-end items-center ml-6">
                        <ul class="flex h-full space-x-8">
                            <li class="h-full hidden md:flex items-center" :class="{'border-t-4 border-b-4 border-transparent border-b-orange': activeRoute('home')}">
                                <Link :href="route('home')">{{$t('header.home')}}</Link>
                            </li>
                            <li class="h-full hidden md:flex items-center" :class="{'border-t-4 border-b-4 border-transparent border-b-orange': activeRoute('register')}">
                                <Link :href="route('register')">{{$t('header.register')}}</Link>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex flex-col flex-auto md:mx-4">
            <slot/>
        </main>

        <footer class="md:mx-4 bg-blue md:rounded-t-md text-white">
            <div class="max-w-7xl mx-auto w-full py-8 px-4 grid grid-cols-1 gap-4 text-sm leading-6 sm:grid-cols-2 sm:mx-auto">
                <div>
                    
                </div>
  
            </div>
            <div class="hidden md:block border-t border-white h-[var(--bottom-bar-height)]">
                <div class="max-w-7xl mx-auto w-full h-full px-4 flex items-center">
                 
                </div>
            </div>
        </footer>

        <section class="md:hidden sticky bottom-0 z-20">
            <HamburgerMenu>
                <template v-slot:menu> 
                    <nav class="w-full">
                        <ul class="divide-y divide-stone font-light text-lg">
                            <li class="text-white px-12 py-6" :class="{'text-orange font-normal stroke-2px': route().current('home')}">
                                <HomeIcon class="inline-block w-8 h-8 mr-4 stroke-1px"/>
                                <Link class="align-middle" :href="route('home')">{{$t('header.home')}}</Link>
                            </li>
                            <li class="text-white px-12 py-6" :class="{'text-orange font-normal stroke-2px': route().current('register')}">
                                <UserAddIcon class="inline-block w-8 h-8 mr-4 stroke-1px"/>
                                <Link class="align-middle" :href="route('register')">{{$t('header.register')}}</Link>
                            </li>
                        </ul>
                    </nav>
                </template>
                <template v-slot:user>
                    
                </template>
            </HamburgerMenu>
        </section>

        <Modal/>

    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import { LocationMarkerIcon, PhoneIcon, PaperAirplaneIcon, HomeIcon, MenuIcon, AtSymbolIcon, UserAddIcon } from '@heroicons/vue/outline';
import Button from '@/Components/Button.vue'
import HamburgerMenu from '@/Components/HamburgerMenu.vue'
import Modal from '@/Components/Modal.vue'
import currentRoute from '@/mixins/currentRoute'

export default {

    components: {
        Link,
        LocationMarkerIcon,
        PhoneIcon,
        PaperAirplaneIcon,
        HomeIcon,
        Button,
        MenuIcon,
        HamburgerMenu,
        AtSymbolIcon,
        UserAddIcon,
        Modal
    },

    mixins: [ currentRoute ]

}
</script>

<style lang="postcss">

    .stroke-1px path {
        @apply stroke-1;
    }
    .stroke-2px path {
        @apply stroke-2;
    }

</style>