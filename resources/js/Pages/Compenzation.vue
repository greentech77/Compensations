<template>
    <Head :title="compenzation.name"/>

    <div class="w-full max-w-none -mx-4 rounded-md bg-stone-15 p-4 md:-mx-6 md:p-8">
        <section class="flex flex-wrap lg:flex-nowrap space-x-4">
            <div class="bg-white rounded-md p-6 filter drop-shadow lg:w-1/4 space-y-4">
                <h1 class="text-2xl font-medium ">{{compenzation.name}}</h1>
            </div>


            <div class="lg:w-3/4 w-full mt-4 lg:mt-0 ml-0 sm:ml-10 lg:ml-0 space-y-4">
                <section class="bg-white rounded-md p-6 filter drop-shadow relative z-10">
                    <form @submit.prevent="onSubmitSection(formdata)" class="space-y-4">
                        <div class="flex items-center">
                            <h2 class="text-lg font-medium flex-auto">Osnovni podatki</h2>
                            <button class="button button--icon pl-3 text-stone hover:text-stone-hover" :disabled="formdata.edit" @click.prevent="toggleEditMode(formdata)">
                                <PencilAltIcon class="h-6 w-6"/>
                            </button>
                        </div>
                    
                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup 
                                class="w-full md:w-1/2 flex-auto" 
                                v-model="formdata.form.date"
                                label="Datum" 
                                type="date"
                                :error="formdata.form.errors.date" 
                                @change="formdata.form.clearErrors('date')" 
                                :edit="formdata.edit"
                            />
                            <InputGroup class="w-full md:w-1/2 flex-auto" type="currency" v-model="formdata.form.amount" label="Znesek" :error="formdata.form.errors.amount" @change="formdata.form.clearErrors('amount')" :edit="formdata.edit"/>
                        </div>

                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup 
                                class="w-full md:w-1/2 flex-auto" 
                                v-model="formdata.form.date_payed"
                                label="Datum plačila" 
                                type="date"
                                :error="formdata.form.errors.date_payed" 
                                @change="formdata.form.clearErrors('date_payed')" 
                                :edit="formdata.edit"
                            />
                            <div class="w-full md:w-1/2 flex-auto flex items-center">
                                <Checkbox 
                                    v-model="formdata.form.finished"
                                    name="finished" 
                                    label="Zaključena" 
                                    class="flex items-center"
                                    :disabled="!formdata.edit"
                                />
                            </div>
                        </div>

                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup class="w-full md:w-1/3 flex-auto" type="percent" v-model="formdata.form.discount" label="Diskont" :error="formdata.form.errors.discount" @change="formdata.form.clearErrors('discount')" :edit="formdata.edit"/> 
                            <div class="w-full md:w-1/3 flex-auto flex items-center">
                                <Checkbox 
                                    v-model="formdata.form.with_ddv"
                                    name="discountWithVat" 
                                    label="Z DDV" 
                                    class="flex items-center"
                                    :disabled="!formdata.edit"
                                />
                            </div>
                            <InputGroup class="w-full md:w-1/3 flex-auto" type="percent" v-model="formdata.form.commission" label="Provizija" :error="formdata.form.errors['formattedCommission']" @change="formdata.form.clearErrors('formattedCommission')" :edit="formdata.edit"/>
                        </div>

                        <!-- Entities selection -->
                        <div v-for="(entity, index) in formdata.form.entities" :key="index" class="flex space-x-4 items-end">
                            <InputGroup 
                                class="flex-1" 
                                type="select" 
                                :name="'compenzationEntities[' + index + ']'" 
                                :options="getAvailableOptions(index)" 
                                v-model="formdata.form.entities[index]"
                                label="Stranka" 
                                :error="formdata.form.errors['compenzationEntities.' + index]"
                                @change="formdata.form.clearErrors('compenzationEntities.' + index)"
                                :edit="formdata.edit"
                            />
                            
                            <!-- Button to remove an entity (if more than one exists) -->
                            <Button 
                                v-if="index > 0 && formdata.edit"
                                class="button button--danger h-10 mb-0.5" 
                                @click="removeComponent(index)" 
                                :loading="formdata.form.processing"
                            >
                                Odstrani
                            </Button>
                        </div>

                        <!-- Button to add a new entity -->
                        <div v-if="formdata.edit && hasAvailableEntities" class="flex justify-start">
                            <Button 
                                class="button button--stone" 
                                @click="addComponent" 
                                :loading="formdata.form.processing"
                            >
                                Dodaj stranko
                            </Button>
                        </div>

                        <div class="flex justify-end space-x-4" v-if="formdata.edit">
                            <Button class="button button--white" @click.prevent="resetSection(formdata)" :disabled="formdata.form.processing">Prekliči</Button>
                            <Button class="button button--stone" type="submit" :loading="formdata.form.processing">Shrani</Button>
                        </div>
                    </form>
                </section>

                <!-- PDF Download Section -->
                <section class="bg-white rounded-md p-6 filter drop-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium">Prenos PDF dokumentov</h2>
                        <Button
                            v-if="!hasAllPdfs"
                            type="button"
                            class="button button--stone text-sm !py-1 !px-4"
                            :loading="regenerating"
                            @click="regeneratePdfs"
                        >
                            Regeneriraj PDF dokumente
                        </Button>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a
                            v-if="compenzation.proposal && compenzation.proposal.file_path"
                            :href="route('compenzations.compenzation.pdf.download', { 
                                id: compenzation.id, 
                                type: 'proposal' 
                            })"
                            class="inline-flex items-center px-4 py-2 bg-blue text-white text-sm rounded hover:bg-blue-600 transition"
                            title="Prenos kompenzacije"
                        >
                            <DownloadIcon class="h-5 w-5 mr-2"/>
                            Predlog kompenzacije
                        </a>
                        <a 
                            v-if="compenzation.implementation_agreement && compenzation.implementation_agreement.file_path"
                            :href="route('compenzations.compenzation.pdf.download', { 
                                id: compenzation.id, 
                                type: 'implementation' 
                            })"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition"
                            title="Prenos pogodbe o izvedbi"
                        >
                            <DownloadIcon class="h-5 w-5 mr-2"/>
                            Pogodba o izvedbi
                        </a>
                        <a 
                            v-if="compenzation.realization_agreement && compenzation.realization_agreement.file_path"
                            :href="route('compenzations.compenzation.pdf.download', { 
                                id: compenzation.id, 
                                type: 'realization' 
                            })"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition"
                            title="Prenos pogodbe o unovčenju"
                        >
                            <DownloadIcon class="h-5 w-5 mr-2"/>
                            Pogodba o unovčenju
                        </a>
                        <div v-if="!hasAnyPdf" class="text-gray-500 text-sm">
                            Ni generiranih PDF dokumentov. Klikni gumb "Regeneriraj PDF dokumente" zgoraj.
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>

  </template>
  
  <script>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/mixins/adminLayout'
import InputGroup from '@/Components/InputGroup.vue'
import { PencilAltIcon, DownloadIcon } from '@heroicons/vue/outline';
import Label from '@/Components/Label.vue'
import Checkbox from '@/Components/Checkbox.vue'
import Button from '@/Components/Button.vue'
import { dateFormat, dateTimeFormat, percentFormat, currencyFormat, dateFormatSL} from '@/mixins/filters'

export default {
    layout: AdminLayout,

    components: {
        Head,
        Link,
        InputGroup,
        PencilAltIcon,
        DownloadIcon,
        Label,
        Button,
        Checkbox
    },

    props: {
        compenzation: Object,
        entities:Object
    },

    data() {
        const data = {}

        data['formdata'] = {
            form: this.$inertia.form({
                action: 'update',
                amount: this.compenzation.amount ? this.compenzation.amount : null,
                date: this.compenzation.date ? new Date(this.compenzation.date) : null,
                date_payed: this.compenzation.date_payed ? new Date(this.compenzation.date_payed) : null,
                finished: this.compenzation.finished ? this.booleanFormat(this.compenzation.finished) : false,
                discount: this.compenzation.implementation_agreement?.discount || null,
                with_ddv: this.compenzation.implementation_agreement?.with_ddv ? this.booleanFormat(this.compenzation.implementation_agreement.with_ddv) : false,
                commission: this.compenzation.realization_agreement?.commission || null,
                entities: this.compenzation.compenzation_entity 
                    ? this.compenzation.compenzation_entity.map(entity => ({
                        key: entity.entity.id,
                        label: entity.entity.company_name
                    }))
                    : []
            }),
            edit: false
        }

        return {
            ...data,
            entitiesOptions: [],
            allEntitiesOptions: [], // Store all available entities
            regenerating: false
        }
    },
    async mounted() {
      const { data: response } = await axios.get(route('admin.api.entities'))
      this.allEntitiesOptions = response.data.map(item => {
        return {
          label: item.company_name,
          key: item.id
        }
      });
      this.entitiesOptions = [...this.allEntitiesOptions];
    },

    computed: {
        // Get list of already selected entity keys
        selectedEntityKeys() {
            return this.formdata.form.entities
                .map(entity => entity?.key)
                .filter(key => key !== undefined && key !== null && key !== '');
        },
        // Check if there are still available entities to select
        hasAvailableEntities() {
            return this.selectedEntityKeys.length < this.allEntitiesOptions.length;
        },
        // Check if any PDF is available
        hasAnyPdf() {
            return (this.compenzation.proposal && this.compenzation.proposal.file_path) ||
                   (this.compenzation.implementation_agreement && this.compenzation.implementation_agreement.file_path) ||
                   (this.compenzation.realization_agreement && this.compenzation.realization_agreement.file_path);
        },
        // Whether all expected PDFs are present
        hasAllPdfs() {
            const proposal = this.compenzation.proposal && this.compenzation.proposal.file_path
            const implAgr = this.compenzation.implementation_agreement
            const realAgr = this.compenzation.realization_agreement
            const implOk = !implAgr || !implAgr.id || implAgr.file_path
            const realOk = !realAgr || !realAgr.id || realAgr.file_path
            return Boolean(proposal && implOk && realOk)
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
            this.toggleEditMode(section, false)
        },
        onSubmitSection(section) {
            section.form.patch(this.route('compenzations.compenzation.patch', {
                id: this.compenzation.id
            }),{
                onSuccess: () => {
                    this.toggleEditMode(section, false)
                }
            })
        },
        // Get available options for a specific dropdown (exclude already selected entities)
        getAvailableOptions(currentIndex) {
            const currentValue = this.formdata.form.entities[currentIndex];
            const currentKey = currentValue?.key;
            
            return this.allEntitiesOptions.filter(option => {
                // Include current selected value in this dropdown
                if (option.key === currentKey) {
                    return true;
                }
                // Exclude all other already selected values
                return !this.selectedEntityKeys.includes(option.key);
            });
        },
        addComponent() {
            // Get first available entity
            const availableOptions = this.allEntitiesOptions.filter(option => {
                return !this.selectedEntityKeys.includes(option.key);
            });
            
            // Add a new entity with first available option or empty
            this.formdata.form.entities.push({
                key: availableOptions.length > 0 ? availableOptions[0].key : null,
                label: availableOptions.length > 0 ? availableOptions[0].label : ''
            });
        },

        removeComponent(index) {
            this.formdata.form.entities.splice(index, 1);
        },

        regeneratePdfs() {
            if (this.regenerating) return
            this.regenerating = true
            this.$inertia.post(this.route('compenzations.compenzation.pdf.regenerate', { id: this.compenzation.id }), {}, {
                preserveScroll: true,
                onFinish: () => { this.regenerating = false }
            })
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
  