<template>
    
    <section class="bg-white rounded-md filter drop-shadow relative">
        <div class="flex bg-blue px-4 h-12 items-center rounded-t-md" :class="{ 'rounded-b-md': emptyForm }">
            <h2 class="text-sm uppercase text-white leading-4 mr-auto">{{title}}</h2>
            <Button class="button button--icon-white text-white" :disabled="!editable" v-if="emptyForm" @click.prevent="startForm">
                <PlusCircleIcon class="h-6 w-6"/>
            </Button>
            <div v-else>
                <Button class="button button--icon-white text-white" v-if="enableDelete" :disabled="edit || !editable" @click.prevent="edit = true">
                    <MinusCircleIcon class="h-6 w-6"/>
                </Button>
                <Button class="button button--icon-white text-white" :disabled="edit || !editable" @click.prevent="edit = true">
                    <PencilAltIcon class="h-6 w-6"/>
                </Button>
            </div>
            
        </div>
        <div class="p-4 flex flex-col gap-4" v-if="!emptyForm">
            <form @submit.prevent="onSubmitForm()" class="flex flex-col gap-y-4">
                <slot :edit="edit" :form="form"></slot>
                <div class="flex justify-end space-x-4" v-if="edit">
                    <Button class="button button--white" @click.prevent="reset()" :disabled="form.processing">{{ $t('common.cancel') }}</Button>
                    <Button class="button button--stone" type="submit" :loading="form.processing">{{ $t('common.save') }}</Button>
                </div>
            </form>
            <slot name="footer" v-if="!edit"></slot>
        </div>
    </section>

</template>

<script>
import { PencilAltIcon, PlusCircleIcon, MinusCircleIcon } from '@heroicons/vue/outline'
import Button from '@/Components/Button.vue'

export default {

    components: {
        PencilAltIcon,
        PlusCircleIcon,
        MinusCircleIcon,
        Button
    },
    
    props: {
        title: String,
        form: Object,
        patchRoute: String,
        editable: {
            type: Boolean,
            default: true
        },
        enableDelete: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            edit: false
        }
    },

    methods: {
        reset() {
            this.form.reset()
            this.edit = false
        },
        startForm() {
            this.edit = true
        },
        onSubmitForm() {
            this.form.patch(this.patchRoute, {
                onSuccess: () => {
                    this.edit = false
                },
                onError: () => {
                    this.edit = true
                },
                preserveScroll: true,
                preserveState: page => Object.keys(page.props.errors).length
            })
            
        }
    },

    computed: {
        emptyForm() {
            if (this.edit) {
                return false
            }
            const data = this.form.data()
            for (const prop in data) {
                if (['action'].includes(prop)) {
                    continue
                }
                if (data[prop] != undefined) {
                    return false
                }
            }
            return true
        }
    }
        
}
</script>