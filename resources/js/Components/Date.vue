<template>
    <Datepicker 
        class="border border-stone-50 rounded-md bg-white w-full px-3 py-2 focus:ring-0 focus:border-stone filter focus:shadow transition duration-150"
        :class="{ '!border-red-600 border-opacity-30 bg-red-600 bg-opacity-10 focus:border-opacity-100 focus:bg-opacity-20': error }" 
        v-model="modelValue" 
        inputFormat="dd. MM. yyyy" 
        :startingView="startingView || 'year'" 
        :locale="locale"></Datepicker>
</template>

<script>
import Datepicker from 'vue3-datepicker'
import { sl, enGB as en } from 'date-fns/locale'

export default {
    components: { Datepicker },

    props: {
        'modelValue': Date,
        'error': Boolean,
        'startingView': String
    },

    emits: ['update:modelValue'],

    data() {
        return {
            locale: sl,
            locales: {
                en, sl
            }
        }
    },

    mounted() {
        this.locale = this.locales[this.$root.$i18n.locale]
    },

    watch: {
        modelValue() {
            this.$emit('update:modelValue', this.modelValue)
            this.$emit('input', this.modelValue)
        },
        '$i18n.locale': function(newLocale) {
            this.locale = this.locales[newLocale]
        }
    },

}
</script>

<style lang="postcss">

    .v3dp__datepicker {
        --elem-selected-bg-color: var(--stone);
        --elem-selected-color: white;
        --elem-hover-bg-color: var(--stone);
        --elem-hover-color: white;
        --text-color: var(--blue);
        --popout-bg-color: var(--stone-15);
        --heading-hover-color: var(--stone-30);
        --elem-disabled-color: var(--stone);
        --divider-color: var(--stone);
    }
    .v3dp__popout {
        --box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    .v3dp__divider {
        border-bottom: none !important;
    }

</style>