<template>
    <teleport to="body" v-if="showModal">
        <div class="fixed flex justify-center items-center z-50 inset-0 overflow-y-auto transition-opacity px-4 py-6" :aria-labelledby="ariaLabel" role="dialog" aria-modal="true">
            <div class="w-screen h-screen absolute bg-stone opacity-[.85]" @click="onOverlayClick()"></div>
            <div class="relative align-bottom bg-white rounded-md text-center shadow-xl transform transition-all sm:max-w-lg sm:w-full w-full">
                <div v-if="modalDismissible" class="absolute top-4 right-4 hover:text-blue-hover cursor-pointer" @click="onCloseModal()">
                    <XIcon class="h-5 w-5"></XIcon>
                </div>
                <div class="sm:pb-4 px-4 py-5 text-center">
                    <div class="mx-auto mb-4 flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full" :class="[bgClass]" v-if="modalStatus">
                        <ExclamationIcon class="h-8 w-8 rounded-full" v-if="modalStatus == 'error' || modalStatus == 'warning'" :class="{ 'text-red-600': modalStatus == 'error', 'text-amber-600': modalStatus == 'warning' }"/>
                        <CheckCircleIcon class="h-8 w-8 text-green-600" v-if="modalStatus == 'success'"/>
                        <InformationCircleIcon class="h-full w-full rounded-full text-stone-50" v-if="modalStatus == 'info'"/>
                    </div>
                    <div class="space-y-5">
                        <slot name="heading">
                            <h3 class="heading heading--3 heading--after" :class="{ 'text-red-600': modalStatus == 'error', 'text-amber-600': modalStatus == 'warning', 'text-green-600': modalStatus == 'success', 'text-blue': modalStatus == 'info' }">
                                {{modalTitle}}
                            </h3>
                        </slot>
                        <slot name="content">
                            <p>
                                {{modalContent}}
                            </p>
                        </slot>
                    </div>
                </div>
                <div class="px-4 py-5">
                    <slot name="footer">
                        <div v-if="modalData?.actions" class="space-x-4 flex justify-center">
                            <template v-for="action in modalData?.actions" :key="action.text">
                                <Button type="button" class="button button--stone" @click="onActionClick(action.action)" :class="[ buttonClass(action.color) ]" :disabled="action.disabled" :loading="action.loading">
                                    {{action.text}}
                                </Button>
                            </template>
                        </div>
                    </slot>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import { ExclamationIcon, CheckCircleIcon, XIcon } from '@heroicons/vue/outline'
import { InformationCircleIcon } from '@heroicons/vue/solid'
import emitter from '@/mixins/emitter'
import Button from '@/Components/Button.vue'

export default {
    components: {
        ExclamationIcon,
        CheckCircleIcon,
        InformationCircleIcon,
        XIcon,
        Link,
        Button
    },

    mixins: [ emitter ],

    emits: ['modal.close'],

    props: {
        title: String,
        status: String,
        content: String,
        dismissible: Boolean
    },

    data() {
        return {
            modalData: undefined
        }
    },

    mounted() {
        this.emitter.on('modal.open', modalData => {
            this.openModal(modalData)
        })
        this.emitter.on('modal.close', () => {
            this.closeModal()
        })
    },
    
    watch: {
        '$page.props.modal': {
            handler() {
                this.openModal({ ...this.$page.props.modal})
            },
            immediate: true
        },
        showModal: {
            handler() {
                document.body.style.overflowY = this.showModal ? 'hidden' : ''
            },
            immediate: true
        }
    },

    beforeUnmount() {
        document.body.style.overflowY = ''
    },

    methods: {
        openModal(modalData) {
            if (!modalData || Object.keys(modalData).length === 0) {
                return
            }
            this.modalData = modalData
        },
        closeModal() {
            this.$emit('modal.close', { ...this.modalData });
            this.modalData = undefined
        },
        onCloseModal() {
            this.closeModal()
        },
        onOverlayClick() {
            if (this.modalDismissible) {
                this.closeModal()
            }
        },
        onActionClick(action) {
            if (action.type == 'redirect') {
                this.$inertia.get(action.url)
            }
            if (action.type == 'close') {
                // no extra action
            }
            if (action.type == 'resolve') {
                action.resolve()
            }
            if (action.type == 'reject') {
                action.reject()
            }
            if (this.modalAutoClose) {
                this.closeModal()
            }
        },
        buttonClass(color) {
            if (color == undefined) {
                return 'button--stone'
            }
            return `button--${color}`
        }
    },

    computed: {
        showModal() {
            return !!this.modalData || !!this.title
        },
        ariaLabel() {
            return this.modalTitle ?? 'System Modal'
        },
        modalTitle() {
            return this.modalData?.title ?? this.title
        },
        modalContent() {
            return this.modalData?.content ?? this.content
        },
        modalStatus() {
            return this.modalData?.status ?? this.status
        },
        modalDismissible() {
            return this.modalData?.dismissible ?? this.dismissible
        },
        modalAutoClose() {
            return this.modalData?.autoClose ?? true
        },
        bgClass() {
            if (this.modalStatus == 'error') {
                return 'bg-red-200'
            }
            if (this.modalStatus == 'warning') {
                return 'bg-amber-200'
            }
            if (this.modalStatus == 'success') {
                return 'bg-green-200'
            }
            if (this.modalStatus == 'info') {
                return '!h-14 !w-14';
            }
            return 'bg-gray-200'
        }
    }

}
</script>

<style lang="postcss">

    .status {

        &--error {
            @apply bg-red-100;

            svg {
                @apply text-red-600;
            }
        }
        &--info {
            @apply bg-green-100;

            svg {
                @apply text-green-600;
            }
        }
        &--warning {
            @apply bg-yellow-100;

            svg {
                @apply text-yellow-600;
            }
        }
        &--info {
            @apply bg-stone-15;

            svg {
                @apply text-blue;
            }
        }
    }

</style>