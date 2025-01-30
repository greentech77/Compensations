<template>
    <div>
        <div class="flex items-end mb-1" v-if="label || $slots.label">
            <div class="flex-1">
                <Label :error="!!error" :for="labelSlug" v-slot:default="{ processLabel }">
                    <slot name="label">
                        {{processLabel(label)}}
                        <span v-if="required" class="text-red-600">*</span>
                    </slot>
                </Label>
            </div>
            <div class="text-xs text-stone empty:hidden">
                <slot name="labelComment"></slot>
            </div>
        </div>
        <Input :name="name || labelSlug" v-if="edit" v-model="proxy" :error="!!error" :type="type" :autocomplete="autocomplete" :startingView="startingView" :options="options" @blur="onBlur" @input="onInput" @change="onChange" :disabled="disabled" :placeholder="placeholder" @query-change="onQueryChange" :input-config="inputConfig"></Input>
        <div v-else class="border border-transparent leading-6 w-full">
            <strong>{{formatStaticValue(inputValue) ?? defaultStaticValue}}</strong>
        </div>
        <InputError :error="error"/>
    </div>
</template>

<script>
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import InputError from '@/Components/InputError.vue'
import Date from '@/Components/Date.vue'
import slugify from 'slugify'
import { dateFormat } from '@/mixins/filters'

export default {
    components: {
        Input,
        Label,
        InputError,
        Date
    },

    props: {
        modelValue: [String, Object, Number],
        error: String,
        type: {
            type: String,
            default: 'text'
        },
        label: [String, Function],
        autocomplete: String,
        startingView: String,
        options: Array,
        name: String,
        disabled: Boolean,
        placeholder: String,
        edit: {
            type: Boolean,
            default: true
        },
        required: Boolean,
        inputConfig: Object,
    },

    emits: ['update:modelValue', 'change', 'blur', 'input', 'query-change'],

    methods: {
        focus() {
            this.$refs.input.focus()
        },
        onInput($event) {
            this.$emit('input', $event)
        },
        onBlur($event) {
            this.$emit('blur', $event)
        },
        onChange($event) {
            this.$emit('change', $event)
        },
        formatStaticValue(value) {
            switch (this.type) {
                case 'date':
                    return dateFormat(value)
                case 'combobox':
                    return value.label
                case 'select':
                    if (value === undefined) {
                        return undefined
                    }
                    return this.$t(value ?? '')
                default:
                    return value
            }
        },
        onQueryChange(q) {
            this.$emit('query-change', q)
        }
    },

    watch: {
        edit() {
            this.onChange()
        }
    },

    computed: {
        proxy: {
            get() {
                return this.modelValue
            },
            set(value) {
                console.log("value: " + value);
                this.$emit('update:modelValue', value)
            },
        },
        labelSlug() {
            if (!this.label) {
                return Math.random().toString(36).slice(2, 7)
            }
            return slugify(this.label, {
                lower: true,
                remove: /[*+~.()'"!:@]/g
            })
        },
        inputValue() {
            if (this.type == 'select') {
                return this.proxy?.label
            } else {
                return this.proxy
            }
        },
        defaultStaticValue() {
            if (this.type == 'password') {
                return '••••••••'
            } else {
                return '/'
            }
        }
    }
}
</script>