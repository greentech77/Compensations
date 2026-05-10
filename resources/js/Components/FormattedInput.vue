<template>
    <input ref="inputRef" type="text"/>
</template>

<script>
import { useCurrencyInput } from 'vue-currency-input'
import { watch } from 'vue'

export default {
    props: {
        modelValue: Number,
        type: String
    },

    emits: ['update:modelValue', 'input', 'change', 'blur'],

    setup(props) {

        const options = {
            locale: 'de',
            currency: 'EUR',
        }

        if (props.type =='unit') {
            options['precision'] = 6
            options['currencyDisplay'] = 'hidden'
        }

        if (props.type =='currency') {
            options['precision'] = 2
            options['currencyDisplay'] = 'symbol'
            // useGrouping defaults to true: shows "3.023,91 €" when blurred, "3023,91" when focused
        }

        const { inputRef, setValue } = useCurrencyInput(options)

        watch(() => props.modelValue, () => {
            setValue(props.modelValue)
        })

        return { inputRef }
    }
}
</script>
