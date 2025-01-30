<template>
    
    <div class="min-h-screen flex">

        <Sidebar class="w-80 pt-[calc(var(--top-bar-height)*2+var(--main-bar-height))]">
            <nav class="w-full text-lg">
                <ul class="space-y-6 my-3">
                    <li class="hover:text-gray-400 text-sm uppercase tracking-wider" :class="{'text-orange hover:text-orange-hover': activeRoute('dashboard')}">
                        <Link :href="route('dashboard')">
                            <HomeIcon class="w-6 h-6 inline-block mr-4"/>Nadzorna plošča
                        </Link>
                    </li>
                    <li class="hover:text-gray-400 text-sm uppercase tracking-wider" :class="{'text-orange hover:text-orange-hover': activeRoute('entities')}">
                        <Link :href="route('entities')">
                            <OfficeBuildingIcon class="w-6 h-6 inline-block mr-4"/>Podjetja
                        </Link>
                    </li>
                    <li class="hover:text-gray-400 text-sm uppercase tracking-wider" :class="{'text-orange hover:text-orange-hover': activeRoute('compenzations')}">
                        <Link :href="route('compenzations')">
                            <CurrencyEuroIcon class="w-6 h-6 inline-block mr-4"/>Kompenzacije
                        </Link>
                    </li>
                </ul>
            </nav>
        </Sidebar>

        <div class="flex-auto">
            <header class="md:mx-4 sticky top-0 md:top-[calc(var(--top-bar-height)*-1)] z-20">
                <div class="hidden md:block bg-blue md:rounded-b-md h-[var(--top-bar-height)]"></div>
                <div class="flex flex-row items-stretch border-b border-blue h-[var(--main-bar-height)] bg-white">
                    <div class="h-full px-4 flex w-full">
                        <div class="h-full flex items-center w-[210px]">
                            <Link :href="route('home')">
                                <!--img src="@/../images/BTN-logo.svg" alt="BTNaložbe logo"-->
                                Domov
                            </Link>
                        </div>
                    </div>
                    <div class="pr-4 flex h-full items-center flex-none">
                        <User :logout-url="route('logout')"/>
                    </div>
                </div>
                <div class="border-b border-blue h-[var(--top-bar-height)] px-4 flex items-center space-x-4 bg-white">
                    <span>
                        Administracija
                    </span>
                    <template v-for="item in breadcrumb" :key="item.label">
                        <ChevronRightIcon class="w-5 h-5 inline-block"/>
                        <Link v-if="item.route" :href="item.route" class="hover:text-blue-hover">
                            {{item.label}}
                        </Link>
                        <span v-else>
                            {{item.label}}
                        </span>
                    </template>
                    
                </div>
            </header>
            <MainBlock>
                <slot/>
            </MainBlock>
        </div>

    </div>
    
    <Modal/>
    <Toast/>

</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Sidebar from '@/Layouts/Sidebar.vue'
import MainBlock from '@/Layouts/MainBlock.vue'
import { ChevronRightIcon, HomeIcon, UserIcon, DocumentReportIcon, ChartPieIcon, OfficeBuildingIcon, CurrencyEuroIcon } from '@heroicons/vue/outline';
import currentRoute from '@/mixins/currentRoute'
import User from '@/Components/User.vue'
import Modal from '@/Components/Modal.vue'
import Toast from '@/Components/Toast.vue'

export default {

    mixins: [currentRoute],

    components: {
        Sidebar, 
        MainBlock,
        Link,
        ChevronRightIcon,
        HomeIcon, 
        UserIcon,
        DocumentReportIcon,
        ChartPieIcon,
        OfficeBuildingIcon,
        CurrencyEuroIcon,
        User,
        Modal,
        Toast
    },

    computed: {
        breadcrumb() {
            return this.$page.props.breadcrumb
        }
    },

}
</script>