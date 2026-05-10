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

        <!-- Entities selection -->
        <div v-for="(component, index) in components" :key="index" class="flex space-x-4 items-end">
          <InputGroup 
            class="flex-1" 
            type="combobox" 
            :name="'compenzationEntities[' + index + ']'" 
            :options="getAvailableOptions(index)" 
            v-model="component.data.compenzationEntity.value" 
            label="Stranka" 
            :error="form.errors['compenzationEntities.' + index]"
            @change="clearDynamicError(index, 'compenzationEntity')"/>
          
          <!-- Button to remove an entity (if more than one exists) -->
          <Button 
            v-if="index > 0"
            class="button button--danger h-10 mb-0.5" 
            @click="removeComponent(index)" 
            :loading="form.processing">
            Odstrani
          </Button>
        </div>

        <!-- Button to add a new entity -->
        <div v-if="hasAvailableEntities" class="flex justify-start">
          <Button 
            class="button button--stone" 
            @click="addComponent" 
            :loading="form.processing">
            Dodaj stranko
          </Button>
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
        allEntitiesOptions: [], // Shrani vse možnosti
        select_entity: 'Izberi stranko',
        components: [
          { 
            type: 'InputGroup', 
            data: { compenzationEntity: { value: '' }, errors: { compenzationEntity: '' }, processing: false } 
          }
        ],
      }
    },
    beforeMount() {
      // Počisti localStorage PRED vsem - najzgodneje možno
      localStorage.removeItem('components');
    },
    created() {
      // Počisti localStorage PRED vsem
      localStorage.removeItem('components');
      // Resetiraj komponente že ob kreiranju komponente - pokliči clearComponents()
      this.clearComponents();
    },
    async mounted() {
      // Počisti localStorage, da se ne naložijo stare komponente
      localStorage.removeItem('components');
      
      // Vedno začni z eno komponento - pokliči clearComponents()
      this.clearComponents();
      
      const { data: response } = await axios.get(route('admin.api.entities'))
      // Prikaži vse stranke
      this.allEntitiesOptions = response.data.map(item => {
        return {
          label: item.company_name,
          key: item.id
        }
      });
      this.entitiesOptions = [...this.allEntitiesOptions];

      // Nastavi prvo podjetje kot privzeto izbrano v prvem dropdown-u
      if (this.allEntitiesOptions.length > 0 && this.components.length > 0) {
        this.components[0].data.compenzationEntity.value = this.allEntitiesOptions[0];
      }

      console.log('Entity Options:', this.entitiesOptions);
      console.log('Components after mount:', this.components.length);
      
      // Počisti localStorage tudi po naložitvi - z zamudo, da se zagotovi, da se počisti
      setTimeout(() => {
        // Pokliči clearComponents() za zagotovitev, da se komponente resetirajo
        this.clearComponents();
        // Po čiščenju ponovno nastavi prvo podjetje
        if (this.allEntitiesOptions.length > 0 && this.components.length > 0) {
          this.components[0].data.compenzationEntity.value = this.allEntitiesOptions[0];
        }
      }, 100);
    },
    computed: {
      // Vrne seznam že izbranih podjetij (njihove key-e)
      selectedEntityKeys() {
        return this.components
          .map(component => component.data.compenzationEntity.value?.key)
          .filter(key => key !== undefined && key !== null && key !== '');
      },
      // Preveri, ali so še na voljo neizbrane stranke
      hasAvailableEntities() {
        return this.selectedEntityKeys.length < this.allEntitiesOptions.length;
      }
    },
    methods: {
      // Vrne razpoložljive možnosti za določen dropdown (izključi že izbrana podjetja)
      getAvailableOptions(currentIndex) {
        const currentValue = this.components[currentIndex]?.data.compenzationEntity.value;
        const currentKey = currentValue?.key;
        
        return this.allEntitiesOptions.filter(option => {
          // Vključi trenutno izbrano vrednost v tem dropdown-u
          if (option.key === currentKey) {
            return true;
          }
          // Izključi vse druge že izbrane vrednosti
          return !this.selectedEntityKeys.includes(option.key);
        });
      },
      // Override fakeData metodo iz mixina za izbiro pravih podjetij iz baze
      fakeData() {
        // Kliči originalno fakeData metodo za ostale podatke
        const originalFakeData = this.$options.mixins
          .find(m => m.methods && m.methods.fakeData)
          ?.methods.fakeData;
        
        if (originalFakeData) {
          originalFakeData.call(this, this.form);
        }
        
        // Izberi random število podjetij (1 do maksimalno število razpoložljivih)
        const maxEntities = Math.min(5, this.allEntitiesOptions.length);
        const entitiesCount = Math.floor(Math.random() * maxEntities) + 1;
        
        // Izberi random podjetja iz razpoložljivih
        const shuffled = [...this.allEntitiesOptions].sort(() => 0.5 - Math.random());
        const selectedEntities = shuffled.slice(0, entitiesCount);
        
        // Počisti obstoječe komponente
        this.components = [];
        
        // Dodaj komponente za vsako izbrano podjetje
        selectedEntities.forEach((entity, index) => {
          this.components.push({
            type: 'InputGroup',
            data: { 
              compenzationEntity: { value: entity }, 
              errors: { compenzationEntity: '' }, 
              processing: false 
            }
          });
        });
      },
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
            // Počisti localStorage po uspešni validaciji
            localStorage.removeItem('components');
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
    // Dodaj novo komponento
    const newIndex = this.components.length;
    this.components.push(
    { 
        type: 'InputGroup', 
        data: { compenzationEntity: { value: '' }, errors: { compenzationEntity: '' }, processing: false }
    });
    
    // Počakaj, da se komponenta doda, nato nastavi prvo razpoložljivo podjetje
    this.$nextTick(() => {
      const availableOptions = this.getAvailableOptions(newIndex);
      if (availableOptions.length > 0) {
        this.components[newIndex].data.compenzationEntity.value = availableOptions[0];
      }
    });
    // NE shranjuj v localStorage - komponente se ne smejo shranjevati
    },

    removeComponent(index) {
        this.components.splice(index, 1);
    },

    clearComponents() {
        if (this.components.length > 0) {
            this.components[0].data.compenzationEntity.value = '';
            this.components = this.components.slice(0, 1);
        }
        // Počisti localStorage ob ročnem čiščenju
        localStorage.removeItem('components');
    }
    },
    watch: {
        // Prepreči shranjevanje komponent v localStorage in resetiraj, če se naložijo
        components: {
            handler(newVal, oldVal) {
                // Preveri, ali se komponente naložijo iz localStorage
                const savedComponents = localStorage.getItem('components');
                if (savedComponents && newVal.length > 1) {
                    // Če se komponente naložijo iz localStorage, jih resetiraj
                    setTimeout(() => {
                        localStorage.removeItem('components');
                        if (this.components.length > 1) {
                            this.components = [
                                { 
                                    type: 'InputGroup', 
                                    data: { compenzationEntity: { value: '' }, errors: { compenzationEntity: '' }, processing: false } 
                                }
                            ];
                        }
                    }, 10);
                }
            },
            deep: true,
            immediate: false
        }
    },
  }
  </script>
  
  <style lang="postcss">
    @import '../../css/form.css';
  </style>
  