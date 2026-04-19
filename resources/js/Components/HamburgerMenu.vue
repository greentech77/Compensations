<template>
    <div class="relative h-[var(--bottom-bar-height)]">
        <div class="relative bottom-0 w-full px-3 h-[var(--bottom-bar-height)] flex bg-blue justify-center items-center border-t border-white z-20" :class="{ '!fixed': opened }">
            <button class="button button--icon !text-white !hover:text-gray-400" @click="toggleMenu(!opened)">
                <MenuIcon class="w-8 h-8" v-if="!opened"/>
                <XIcon class="w-8 h-8" v-else/>
            </button>
        </div>
        <transition name="fade">
            <div class="fixed w-screen h-screen pb-[var(--bottom-bar-height)] top-0 left-0 bg-blue z-10 translate-y-0" v-if="opened">
                <div class="flex flex-col h-full">
                    <nav class="flex justify-end items-center p-4">
                        <LocaleSwitch class="header__menu-item"/>
                    </nav>
                    <div class="flex flex-auto w-full items-center p-12">   
                        <slot name="menu"/>
                    </div>
                    <div class="flex">
                        <slot name="user"/>
                    </div>
                </div>
            </div>
        </transition>
    </div>
    
</template>

<script>
import { MenuIcon, XIcon } from '@heroicons/vue/outline'
import { router } from '@inertiajs/vue3'

export default {

    components: {
        MenuIcon,
        XIcon
    },
    
    data() {
        return {
            opened: false,
        }
    },

    mounted() {
        router.on('start', (event) => {
            this.toggleMenu(false)
        })
    },

    methods: {
        toggleMenu(state) {
            this.opened = state
            document.body.style.overflowY = this.opened ? 'hidden' : ''
        }
    }

}
</script>

<style lang="postcss" scoped>

    .fade-enter-active, .fade-leave-active {
        @apply transition-transform duration-300;
    }

    .fade-enter-from, .fade-leave-to {
        @apply translate-y-full;
    }

</style>