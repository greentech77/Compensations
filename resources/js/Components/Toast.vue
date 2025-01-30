<template>
    
    <div class="fixed bottom-6 right-6 pointer-events-none">
        <div class="absolute bottom-0 right-0 flex flex-col-reverse gap-y-4">
            <template v-for="toast in toastArray" :key="toast.uuid">
                <div class="flex items-center bg-gray-200 py-3 px-4 w-64 text-gray-900 rounded-md border border-gray-600 drop-shadow-md"  :class="typeClass(toast.type)" >
                    <span class="mr-4 text-sm">
                        {{toast.text}}
                    </span>
                    <Button class="button button--icon pointer-events-auto cursor-pointer ml-auto" @click="removeToast(toast.uuid)">
                        <XIcon class="w-6 h-6" :class="typeClass(toast.type)"/>
                    </Button>
                </div>
            </template>
        </div>
    </div>

</template>

<script>
import { XIcon } from '@heroicons/vue/outline'
import Button from '@/Components/Button.vue'

const classMap = {
    success: '!bg-green-200 !text-green-900 !border-green-400',
    error: '!bg-red-200 !text-red-900 !border-red-400',
}

export default {

    components: {
        XIcon,
        Button
    },
    
    data() {
        return {
            toastArray: []
        }
    },

    watch: {
        '$page.props'() {
            if (this.$page.props.toast) {
                this.addToast(this.$page.props.toast)
            }
        }
    },

    methods: {
        addToast(toast) {
            const uuid = Math.random().toString(36).substring(3,9)
            const timeoutId = setTimeout(() => {
                this.removeToast(uuid)
            }, 5000)
            this.toastArray.push({
                text: toast.text,
                type: toast.type,
                timeoutId: timeoutId,
                uuid: uuid
            })
        },
        removeToast(uuid) {
            this.toastArray = this.toastArray.filter(toast => {
                if (toast.uuid == uuid) {
                    clearTimeout(toast.timeoutId)
                    return false
                }
                return true
            })
        },
        typeClass(type) {
            return classMap[type]
        }
    }

}
</script>