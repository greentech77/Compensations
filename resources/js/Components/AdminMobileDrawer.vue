<template>
    <Teleport to="body">
        <div
            v-if="open"
            id="admin-mobile-nav"
            class="fixed inset-0 z-[100] md:hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="admin-mobile-nav-title"
        >
            <div
                class="absolute inset-0 bg-black/50"
                aria-hidden="true"
                @click="close"
            />
            <div
                class="absolute top-0 bottom-0 left-0 flex w-[min(20rem,calc(100vw-2rem))] max-w-[100vw] flex-col bg-blue text-white shadow-elevated"
            >
                <div class="flex shrink-0 items-center justify-between border-b border-white/20 px-3 py-2">
                    <span id="admin-mobile-nav-title" class="px-2 text-sm font-medium uppercase tracking-wider text-white/90">
                        Meni
                    </span>
                    <button
                        type="button"
                        class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-md text-white hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange"
                        aria-label="Zapri meni"
                        @click="close"
                    >
                        <XIcon class="h-8 w-8" aria-hidden="true" />
                    </button>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto px-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                    <AdminNavLinks mobile />
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script>
import { XIcon } from '@heroicons/vue/outline'
import { router } from '@inertiajs/vue3'
import AdminNavLinks from '@/Components/AdminNavLinks.vue'

let openCount = 0

function lockBodyScroll(lock) {
    if (lock) {
        openCount += 1
        if (openCount === 1) {
            document.body.style.overflow = 'hidden'
        }
    } else {
        openCount = Math.max(0, openCount - 1)
        if (openCount === 0) {
            document.body.style.overflow = ''
        }
    }
}

export default {
    components: {
        XIcon,
        AdminNavLinks,
    },

    props: {
        open: {
            type: Boolean,
            default: false,
        },
    },

    emits: ['close'],

    watch: {
        open(val) {
            lockBodyScroll(val)
        },
    },

    mounted() {
        this._onKeydown = (e) => {
            if (e.key === 'Escape' && this.open) {
                this.close()
            }
        }
        document.addEventListener('keydown', this._onKeydown)
        router.on('start', () => {
            if (this.open) {
                this.close()
            }
        })
        if (this.open) {
            lockBodyScroll(true)
        }
    },

    beforeUnmount() {
        document.removeEventListener('keydown', this._onKeydown)
        if (this.open) {
            lockBodyScroll(false)
        }
    },

    methods: {
        close() {
            this.$emit('close')
        },
    },
}
</script>
