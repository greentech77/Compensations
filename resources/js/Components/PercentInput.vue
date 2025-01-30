<template>
        <input
            class="form-input w-full text-left pr-8"
            :value="displayValue"
            @input="handleInput"
            @focus="isFocused = true"
            @blur="handleBlur"
            type="text"
            placeholder="Vnesi odstotek"
        />
</template>


<script>
export default {
   props: {
       modelValue: {
           type: Number,
           required: true,
       },
   },
   data() {
       return {
           isFocused: false
       };
   },
   emits: ['update:modelValue'],
   computed: {
       /*displayValue() {
           return this.isFocused 
               ? (this.modelValue != null ? this.modelValue.toString().replace('.', ',') : '')
               : (this.modelValue != null ? this.modelValue.toFixed(2).replace('.', ',') + '%' : '');
       },*/
       displayValue() {
        return this.modelValue != null 
            ? this.modelValue.toFixed(2).replace('.', ',') + ' %'
            : '';
        },
   },
   methods: {
       handleInput(event) {
           const rawValue = event.target.value.replace(/[^\d,]/g, '').replace(',', '.');
           const numericValue = parseFloat(rawValue);
           this.$emit('update:modelValue', isNaN(numericValue) ? 0 : numericValue);
       },
       handleBlur() {
           this.isFocused = false;
           this.$emit('update:modelValue', parseFloat(this.modelValue.toFixed(2)));
       },
   },
};
</script>

<style scoped>
.form-input {
    border: 1px solid #ccc;
    padding: 0.5rem;
    border-radius: 4px;
}
</style>