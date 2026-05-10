<template>
    <template v-if="edit">
        <input
            class="rounded !border-none !ring-0 bg-stone-30 checked:bg-stone checked:hover:bg-stone-hover checked:focus:bg-stone-hover mr-3"
            :id="name"
            type="checkbox"
            v-model="proxy"
        >
        <slot name="label">
            <label class="hover:text-blue-hover" :for="name">{{ label }}</label>
        </slot>
    </template>
    <template v-else>
        <span
            class="inline-flex items-center mr-3 h-4 w-4 rounded"
            :class="modelValue ? 'bg-stone' : 'bg-stone-30'"
        >
            <svg v-if="modelValue" class="h-4 w-4 text-white" viewBox="0 0 16 16" fill="currentColor">
                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
            </svg>
        </span>
        <slot name="label">
            <span>{{ label }}</span>
        </slot>
    </template>
</template>

<script>
export default {
    emits: ['update:modelValue'],

    props: {
        modelValue: Boolean,
        label: String,
        name: String,
        edit: {
            type: Boolean,
            default: true,
        },
    },

    computed: {
        proxy: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue', value)
            },
        },
    },
}
</script>

<style lang="postcss" scoped>

    [type=checkbox] {
        box-shadow: none !important;
    }

</style>