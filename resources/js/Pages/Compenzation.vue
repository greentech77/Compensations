<template>
    <Head :title="compenzation.name"/>

    <div class="w-full bg-stone-15 p-8 rounded-md">
        <section class="flex flex-wrap lg:flex-nowrap space-x-4">
            <div class="bg-white rounded-md p-6 filter drop-shadow lg:w-1/4 space-y-4">
                <h1 class="text-2xl font-medium ">{{compenzation.name}}</h1>
            </div>


            <div class="lg:w-3/4 w-full mt-4 lg:mt-0 ml-0 sm:ml-10 lg:ml-0 space-y-4">
                <section class="bg-white rounded-md p-6 filter drop-shadow relative z-10">
                    <EditableForm class="relative z-30" :editable="compenzation" :title="'Osnovni podatki'" :patch-route="patchRoute" :form="compenzationForm" v-slot="{ edit, form }">
                    
                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup 
                                class="w-full md:w-1/2 flex-auto" 
                                v-model="form.date"
                                label="Datum" 
                                type="date"
                                :error="form.errors.date" 
                                @change="form.clearErrors('date')" 
                                :edit="edit"
                            />
                            <InputGroup class="w-full md:w-1/2 flex-auto" type="currency" v-model="form.amount" label="Znesek" :error="form.errors.amount" @change="form.clearErrors('amount')" :edit="edit"/>
                        </div>

                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup class="w-full md:w-1/3 flex-auto" type="percent" v-model="form.discount" label="Diskont" :error="form.errors.discount" @change="form.clearErrors('discount')" :edit="edit"/> 
                            <div class="w-full md:w-1/3 flex-auto flex items-center">
                                <Checkbox 
                                    v-model="form.with_ddv"
                                    name="discountWithVat" 
                                    label="Z DDV" 
                                    class="flex items-center"
                                />
                            </div>
                            <InputGroup class="w-full md:w-1/3 flex-auto" type="percent" v-model="form.commission" label="Provizija" :error="form.errors['formattedCommission']" @change="form.clearErrors('formattedCommission')" :edit="edit"/>
                        </div>

                        <div class="flex justify-end space-x-4" v-for="(entity, index) in form.entities" :key="index">
                        <!-- InputGroup for selecting the company name -->
                        <InputGroup 
                            class="w-full md:w-1/2 flex-auto text-left" 
                            type="select" 
                            :name="'compenzationEntities[' + index + ']'" 
                            :options="entitiesOptions" 
                            v-model="form.entities[index]"
                            label="Stranka" 
                            :error="form.errors['compenzationEntities.' + index]"
                            @change="clearDynamicError(index, 'compenzationEntity')"
                        />
                        
                        <!-- Button to add a new entity -->
                        <Button 
                            class="md:w-1/4 flex-auto button--stone h-10 mt-7" 
                            @click="addComponent" 
                            :loading="compenzationForm.processing"
                            :edit="edit"
                        >
                            Dodaj stranko
                        </Button>

                        <!-- Button to remove an entity (if more than one exists) -->
                        <Button 
                            v-if="index > 0"
                            class="md:w-1/4 flex-auto button--danger h-10 mt-7" 
                            @click="removeComponent(index)" 
                            :loading="compenzationForm.processing"
                            :edit="edit"
                        >
                            Odstrani stranko
                        </Button>
                    </div>

                    </EditableForm>
                </section>
            </div>
        </section>
    </div>

  </template>
  
  <script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import AdminLayout from '@/mixins/adminLayout'
import InputGroup from '@/Components/InputGroup.vue'
import { PencilAltIcon } from '@heroicons/vue/outline';
import Label from '@/Components/Label.vue'
import Checkbox from '@/Components/Checkbox.vue'
import Button from '@/Components/Button.vue'
import EditableForm from '@/Forms/EditableForm.vue'
import { dateFormat, dateTimeFormat, percentFormat, currencyFormat, dateFormatSL} from '@/mixins/filters'

export default {
    layout: AdminLayout,

    components: {
        Head,
        Link,
        InputGroup,
        PencilAltIcon,
        Label,
        Button,
        Checkbox,
        EditableForm
    },

    props: {
        compenzation: Object,
        entities:Object
    },

    data() {
        const data = {}

        return {
            entitiesOptions: [],
            select_entity: 'Izberi stranko',
            compenzationForm : this.$inertia.form({
                //amount: this.compenzation.amount ? this.currencyFormat(this.compenzation.amount) : null,
                amount: this.compenzation.amount ? this.compenzation.amount : null,
                date: this.compenzation.date ? new Date(this.compenzation.date) : null,
                discount: this.compenzation.implementation_agreement.discount ? this.percentFormat(this.compenzation.implementation_agreement.discount) : null,
                with_ddv: this.compenzation.implementation_agreement.with_ddv ? this.booleanFormat(this.compenzation.implementation_agreement.with_ddv) : null,
                commission: this.compenzation.realization_agreement.commission ? this.percentFormat(this.compenzation.realization_agreement.commission) : null,
                entities: this.compenzation.compenzation_entity 
                    ? this.compenzation.compenzation_entity.map(entity => ({
                        key: entity.entity.id,
                        label: entity.entity.company_name
                    }))
                    : []
            }),
            components: [
            { 
                type: 'InputGroup', 
                data: { compenzationEntity: { value: '' }, errors: { compenzationEntity: '' }, processing: false } 
            }]
        }
    },
    async mounted() {
      const { data: response } = await axios.get(route('admin.api.entities'))
      this.entitiesOptions = response.data.map(item => {
        return {
          label: item.company_name,
          key: item.id
        }
      });


      /*console.log('Entity Options:', this.entitiesOptions);
      console.log('Entities Array:', this.entities);
      console.log('Entities 2 Array:', this.compenzationForm.entities);*/

      this.loadComponents();
    },

    created() {
        /*if (this.compenzation.compenzation_entity && this.compenzation.compenzation_entity.length > 0) {
            components.value = this.compenzation.compenzation_entity;
        }*/
    },

    computed: {
        companyNameBindings() {
            return this.compenzationForm.entitites.map((entity, index) => ({
                get: () => entity.entity.company_name,
                set: value => {
                    this.compenzationForm.entitites[index].entity.company_name = value;
            },
        }));
        },

        amountFormatted: {
            get() {
                return this.currencyFormat(this.compenzationForm.amount); // Format for display
            },
            set(value) {
                this.compenzationForm.amount = parseFloat(value.replace(/[^0-9.-]/g, '')); // Strip formatting for raw value
            }
        }
    },

    methods: {
        booleanFormat(value) {
            // Convert to boolean
            return value === 1 || value === true;
        },
        toggleEditMode(section, state) {
            if (state !== undefined) {
                section.edit = state
            } else {
                section.edit = !section.edit
            }
        },
        resetSection(section) {
            section.form.reset()
            this.toggleEditMode(section)
        },
        onSubmitSection(section) {
            //console.log(section.form);
            section.form.patch(this.route('compenzations.compenzation.patch', {
                id: this.compenzation.id
            }),{
                onSuccess: () => {
                    this.toggleEditMode(section, false)
                }
            })
        },
        addComponent() {

        //const defaultEntity = this.entitiesOptions.length > 0 ? this.entitiesOptions[0].key : '';

        // Add a new entity with default or empty values to the entities array in the form
        this.compenzationForm.entities.push({
            key: null,  // Unique key for the entity, initially null
            label: ''   // Default empty label or value
        });

        this.saveComponents();

        },

        removeComponent(index) {
            this.compenzationForm.entities.splice(index, 1);
            this.saveComponents();
        },

        saveComponents() {
            localStorage.setItem('compenzationEntities', JSON.stringify(this.compenzationForm.entities));
        },

        loadComponents() {
            const savedComponents = localStorage.getItem('components');
            if (savedComponents) {
                this.components = JSON.parse(savedComponents);
            }
        },

        clearComponents() {
            // Keep only the first entity and reset its values
            if (this.compenzationForm.entities.length > 0) {
                this.compenzationForm.entities = this.compenzationForm.entities.slice(0, 1);
                this.compenzationForm.entities[0].key = null;
                this.compenzationForm.entities[0].label = '';
            }
        },
        dateFormat,
        dateTimeFormat,
        percentFormat,
        currencyFormat,
        dateFormatSL
    },

}
</script>
  
  <style lang="postcss">
  </style>
  