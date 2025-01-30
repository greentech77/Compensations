<template>
    <form @submit.prevent="onSubmit" class="space-y-4">
      <section class="bg-white rounded-md p-6 filter drop-shadow space-y-4">
        <h2 class="text-lg font-medium">Osnovni podatki</h2>
        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
          <InputGroup class="w-full md:w-1/2 flex-auto" v-model="form.compenzationDate" name="compenzationDate" label="Datum" :error="form.errors.compenzationDate" type="date" @change="form.clearErrors('compenzationDate')"/>
          <InputGroup class="w-full md:w-1/2 flex-auto" type="currency" v-model="form.compenzationAmount" name="compenzationAmount" label="Znesek" :error="form.errors.compenzationAmount" @change="form.clearErrors('compenzationAmount')"/>
        </div>


        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap items-center">
          <InputGroup class="w-full md:w-1/3 flex-auto" type="percent" v-model="form.compenzationDiscount" label="Diskont" :error="form.errors.compenzationDiscount" @change="form.clearErrors('compenzationDiscount')" />
          <div class="w-full md:w-1/3 flex-auto flex items-center">
              <Checkbox 
                  v-model="form.discountWithVat" 
                  name="discountWithVat" 
                  label="Z DDV" 
                  class="flex items-center"
              />
          </div>
          <InputGroup class="w-full md:w-1/3 flex-auto" type="percent" v-model="form.compenzationCommission" label="Provizija" :error="form.errors.compenzationCommission" @change="form.clearErrors('compenzationCommission')" />
      </div>

        <div class="flex justify-end space-x-4" v-for="(component, index) in components" :key="index">
          <InputGroup 
            class="w-full md:w-1/2 flex-auto text-left" 
            type="select" 
            :key="index" 
            :name="'compenzationEntities[' + index + ']'" 
            :options="entitiesOptions" 
            v-model="component.data.compenzationEntity.value" 
            label="Stranka" 
            :error="form.errors['compenzationEntities.' + index]"
            @change="clearDynamicError(index, 'compenzationEntity')"/>
          <Button 
            class="md:w-1/4 flex-auto button--stone h-10 mt-7" 
            :key="index" 
            @click="addComponent" 
            :loading="form.processing">Dodaj stranko</Button>
          <Button v-if="index > 0"
            class="md:w-1/4 flex-auto button--danger h-10 mt-7" 
            :key="index" 
            @click="removeComponent(index)" 
            :loading="form.processing">Odstrani stranko</Button>
        </div>
      </section>
  
      <section class="flex justify-end space-x-4">
        <Button class="button button--white" :disabled="form.processing" @click="onBack">Nazaj</Button>
        <Button class="button button--stone" type="submit" :loading="form.processing">Naprej</Button>
      </section>

       <!-- Button to clear components -->
       <section class="flex justify-end space-x-4 mt-4">
         <Button class="button button--danger" @click="clearComponents">Izprazni polja</Button>
        </section>
    </form>
  </template>
  
  <script>
  import Checkbox from '@/Components/Checkbox.vue'
  import Button from '@/Components/Button.vue'
  import InputGroup from '@/Components/InputGroup.vue'
  import Label from '@/Components/Label.vue'
  import { fakeCompensationDataMixin } from '@/mixins/faker'
  import { stepperEventsMixin } from './steppedMixins'
  import addressMixin from '@/mixins/address'
  
  export default {
    components: {
      Checkbox,
      Button,
      InputGroup,
      Label
    },
    props: {
      form: Object,
    },
    mixins: [stepperEventsMixin, fakeCompensationDataMixin, addressMixin],
    data() {
      return {
        entitiesOptions: [],
        select_entity: 'Izberi stranko',
        components: [
          { 
            type: 'InputGroup', 
            data: { compenzationEntity: { value: '' }, errors: { compenzationEntity: '' }, processing: false } 
          }
        ],
      }
    },
    async mounted() {
      const { data: response } = await axios.get(route('admin.api.entities'))
      this.entitiesOptions = response.data.map(item => {
        return {
          label: item.company_name,
          key: item.id,
          default_value: 1
        }
      });

      console.log('Entity Options:', this.entitiesOptions);

      this.loadComponents();
    },
    methods: {
      onSubmit() {
        // Collect the dynamic data
        const dynamicEntities = this.components.map(component => component.data.compenzationEntity.value);
  
        // Add the dynamic data to the form data
        this.form.compenzationEntities = dynamicEntities;

        const formData = {
                compenzationDate: this.form.compenzationDate,
                compenzationAmount: this.form.compenzationAmount,
                compenzationDiscount: this.form.compenzationDiscount,
                compenzationCommision: this.form.compenzationCommission,
                compenzationEntities: this.form.compenzationEntities,
                discountWithVat: this.form.discountWithVat,

                // Include select field's value
                //compenzationEntity: this.components.map(component => component.data.compenzationEntity.value)
            };

        //console.log(formData)
  
        // Post the form data
        this.form.post(this.route('compenzation.data'), {
          _error_bag: 'CreateCompenzation',
          data: formData,
          onSuccess: () => {
            this.onComplete()
          },
        });
      },
      clearErrors(index, field) {
        this.components[index].data.errors[field] = '';
      },

      clearDynamicError(index, field) {
         const errorField = `compenzationEntities.${index}`;
         delete this.form.errors[errorField];
      },
      formatPercentage(value) {
          if (value == null || value === '' || isNaN(Number(value))) return '';
          return `${Number(value).toLocaleString('sl-SI', { 
              minimumFractionDigits: 2, 
              maximumFractionDigits: 2 
          })} %`
      },
          
    addComponent() {

    //const defaultEntity = this.entitiesOptions.length > 0 ? this.entitiesOptions[0].key : '';

    this.components.push(
    { 
        type: 'InputGroup', 
        data: { compenzationEntity: { value: '' }, errors: { compenzationEntity: '' }, processing: false }
    });

    this.saveComponents();
    
    },

    removeComponent(index) {
        this.components.splice(index, 1);
        this.saveComponents();
    },

    saveComponents() {
        localStorage.setItem('components', JSON.stringify(this.components));
    },

    loadComponents() {
        const savedComponents = localStorage.getItem('components');
        if (savedComponents) {
            this.components = JSON.parse(savedComponents);
        }
    },

    clearComponents() {
        if (this.components.length > 0) {
            this.components[0].data.compenzationEntity.value = '';
            this.components = this.components.slice(0, 1);
        }
        this.saveComponents();
    }
    },
    watch: {
        components: {
            handler() {
                this.saveComponents();
            },
            deep: true,
        },
    },
  }
  </script>
  
  <style lang="postcss">
    @import '../../css/form.css';
  </style>
  