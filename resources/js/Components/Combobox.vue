<template>
    <Combobox as="div" class="relative w-full" v-model="modelValue" @update:modelValue="updateValue">
        <div class="relative">
            <ComboboxInput
                class="w-full border border-stone-50 rounded-md bg-white px-3 py-2 pl-10 pr-10 text-left cursor-default focus:outline-none focus:border-stone focus:shadow"
                :class="{'!border-red-600 border-opacity-30 bg-red-600 bg-opacity-10 focus:border-opacity-100 focus:bg-opacity-20': error}"
                :displayValue="(option) => option?.label || ''"
                @change="query = $event.target.value"
                :placeholder="modelValue?.label || 'Iskanje...'"
            />
            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                <SelectorIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </ComboboxButton>
        </div>

        <TransitionRoot leave="transition ease-in duration-100" leaveFrom="opacity-100" leaveTo="opacity-0" @after-leave="query = ''">
            <ComboboxOptions class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
                <div v-if="filteredOptions.length === 0 && query !== ''" class="cursor-default select-none py-2 px-4 text-gray-700">
                    Ni rezultatov.
                </div>

                <ComboboxOption
                    v-for="option in filteredOptions"
                    as="template"
                    :key="option.key"
                    :value="option"
                    v-slot="{ active, selected }"
                >
                    <li
                        class="cursor-default select-none relative py-2 pl-10 pr-4"
                        :class="{
                            'bg-stone text-white': active,
                            'text-gray-900': !active
                        }"
                    >
                        <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                            {{ option.label }}
                        </span>
                        <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3" :class="{ 'text-white': active, 'text-stone': !active }">
                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </li>
                </ComboboxOption>
            </ComboboxOptions>
        </TransitionRoot>
    </Combobox>
</template>

<script>
import { Combobox, ComboboxInput, ComboboxButton, ComboboxOptions, ComboboxOption, TransitionRoot } from '@headlessui/vue'
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid'

export default {
    components: {
        Combobox,
        ComboboxInput,
        ComboboxButton,
        ComboboxOptions,
        ComboboxOption,
        TransitionRoot,
        CheckIcon,
        SelectorIcon
    },

    props: {
        modelValue: Object,
        error: Boolean,
        options: Array,
        name: String,
        disabled: Boolean
    },

    emits: ['update:modelValue', 'change', 'query-change'],

    data() {
        return {
            query: ''
        }
    },

    computed: {
        filteredOptions() {
            if (this.query === '') {
                return this.options || []
            }

            const lowerQuery = this.query.toLowerCase()
            return (this.options || []).filter((option) =>
                option.label.toLowerCase().includes(lowerQuery)
            )
        }
    },

    watch: {
        query(newQuery) {
            this.$emit('query-change', newQuery)
        },
        modelValue(newValue) {
            if (newValue) {
                this.$emit('change', newValue)
            }
        }
    },

    methods: {
        updateValue(value) {
            this.$emit('update:modelValue', value)
        }
    }
}
</script>

<style lang="postcss">
@import '../../css/heroicons.css';
</style>

