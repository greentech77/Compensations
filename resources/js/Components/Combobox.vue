<template>
    <Combobox
        as="div"
        class="relative w-full"
        :model-value="modelValue"
        :disabled="disabled"
        nullable
        @update:modelValue="updateValue"
    >
        <ComboboxButton
            class="flex items-center w-full border border-stone-50 rounded-md bg-white px-3 py-2 text-left cursor-pointer focus:outline-none focus:border-stone focus:shadow transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
            :class="{
                '!border-red-600 border-opacity-30 bg-red-600 bg-opacity-10 focus:border-opacity-100 focus:bg-opacity-20': error
            }"
        >
            <span
                class="flex-auto overflow-hidden whitespace-nowrap overflow-ellipsis"
                :class="{ 'text-gray-400': !modelValue?.label }"
            >
                {{ modelValue?.label || placeholder || 'Izberi...' }}
            </span>
            <SelectorIcon class="!h-6 icon ml-2 text-gray-400 shrink-0" aria-hidden="true" />
        </ComboboxButton>

        <TransitionRoot
            leave="transition ease-in duration-100"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
            @after-leave="query = ''"
        >
            <ComboboxOptions
                class="absolute z-20 mt-1 w-full bg-white shadow-lg rounded-md ring-1 ring-black ring-opacity-5 overflow-hidden focus:outline-none"
            >
                <div class="p-2 border-b border-stone-50 bg-stone-15/40">
                    <div class="relative">
                        <SearchIcon
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none"
                            aria-hidden="true"
                        />
                        <ComboboxInput
                            class="w-full border border-stone-50 rounded-md bg-white pl-9 pr-3 py-2 text-sm focus:outline-none focus:border-stone focus:shadow transition duration-150"
                            :displayValue="() => query"
                            :placeholder="searchPlaceholder"
                            autocomplete="off"
                            @change="query = $event.target.value"
                        />
                    </div>
                </div>

                <div class="max-h-60 overflow-auto py-1">
                    <div
                        v-if="filteredOptions.length === 0"
                        class="cursor-default select-none py-3 px-4 text-gray-500 text-sm text-center"
                    >
                        <template v-if="query">Ni rezultatov za "{{ query }}".</template>
                        <template v-else>Ni razpoložljivih možnosti.</template>
                    </div>

                    <ComboboxOption
                        v-for="option in filteredOptions"
                        as="template"
                        :key="option.key"
                        :value="option"
                        v-slot="{ active, selected }"
                    >
                        <li
                            class="cursor-pointer select-none relative py-2 pl-10 pr-4"
                            :class="{
                                'bg-stone text-white': active,
                                'text-gray-900': !active
                            }"
                        >
                            <span
                                class="block truncate"
                                :class="{ 'font-medium': selected, 'font-normal': !selected }"
                            >
                                {{ option.label }}
                            </span>
                            <span
                                v-if="selected"
                                class="absolute inset-y-0 left-0 flex items-center pl-3"
                                :class="{ 'text-white': active, 'text-stone': !active }"
                            >
                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ComboboxOption>
                </div>
            </ComboboxOptions>
        </TransitionRoot>
    </Combobox>
</template>

<script>
import { Combobox, ComboboxInput, ComboboxButton, ComboboxOptions, ComboboxOption, TransitionRoot } from '@headlessui/vue'
import { CheckIcon, SelectorIcon, SearchIcon } from '@heroicons/vue/solid'

export default {
    components: {
        Combobox,
        ComboboxInput,
        ComboboxButton,
        ComboboxOptions,
        ComboboxOption,
        TransitionRoot,
        CheckIcon,
        SelectorIcon,
        SearchIcon
    },

    props: {
        modelValue: Object,
        error: Boolean,
        options: Array,
        name: String,
        disabled: Boolean,
        placeholder: {
            type: String,
            default: 'Izberi...'
        },
        searchPlaceholder: {
            type: String,
            default: 'Iskanje...'
        }
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
