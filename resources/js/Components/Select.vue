<template>
    <Listbox as="div" class="relative w-full appearance-none focus:outline-none focus:border-stone focus:shadow transition duration-150" :model-value="modelValue" @update:modelValue="$emit('update:modelValue', $event)">
        <ListboxButton 
            class="flex items-center relative w-full border border-stone-50 rounded-md bg-white px-3 py-2 text-left cursor-default focus:outline-none focus:border-stone focus:shadow"
            :class=" {'!border-red-600 border-opacity-30 bg-red-600 bg-opacity-10 focus:border-opacity-100 focus:bg-opacity-20' : error }">
                <span class="flex-auto overflow-hidden whitespace-nowrap overflow-ellipsis">{{ display }}</span>
                <SelectorIcon class="!h-6 icon" aria-hidden="true"/>
        </ListboxButton>
        <ListboxOptions class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-56 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
            <ListboxOption
                v-for="option in options"
                as="template"
                :class="{'font-semibold': option == modelValue}"
                :key="option.key"
                :value="option"
            >
                <li class="cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-stone hover:text-white text-blue">
                    <span v-if="option?.key == modelValue?.key" class="absolute inset-y-0 left-0 flex items-center pl-3" >
                        <CheckIcon class="w-5 h-5" aria-hidden="true" />
                    </span>
                    <span class="ml-7 inline-block">
                        {{ option.label }}
                    </span>
                </li>
            </ListboxOption>
        </ListboxOptions>
    </Listbox>
</template>

<script>
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue'
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid'

export default {
    components: { Listbox, ListboxButton, ListboxOptions, ListboxOption, CheckIcon, SelectorIcon },

    props: {
        modelValue: Object,
        error: Boolean,
        options: Array,
        initialDisplay: String
    },

    emits: ['update:modelValue'],

    watch: {
        modelValue() {
            this.$emit('update:modelValue', this.modelValue)
        }
    },

    computed: {
        display() {
            return this.modelValue?.label || this.initialDisplay;
        }
    }

}
</script>

<style lang="postcss">

    @import '../../css/heroicons.css';

</style>
