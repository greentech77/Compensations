<template>
    <Head title="Kompenzacije"/>
    <div class="w-full bg-stone-15 p-8 rounded-md">
        <div class="bg-white border border-stone rounded-md p-6 mb-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Iskanje</label>
                        <Input
                            type="text"
                            v-model="localFilters.search"
                            class="w-96"
                            placeholder="Naziv, ID, datum, znesek, diskont ali provizija"
                        />
                    </div>
                    <Button class="button button--stone" @click="applyFilters()">Filtriraj</Button>
                    <Button
                        v-if="hasActiveFilters"
                        class="button button--stone"
                        @click="clearFilters()"
                    >
                        Počisti
                    </Button>
                </div>
                <Button class="button button--stone" @click="addcompenzation()">Dodaj kompenzacijo</Button>
            </div>
        </div>

        <table class="bg-white w-full divide-y divide-stone">
            <thead class="text-white uppercase tracking-wider font-medium text-xs text-left">
                <tr>
                    <th scope="col" class="pl-6 py-3 rounded-tl-md bg-blue">
                        Naziv kompenzacije
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Datum
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Znesek
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Diskont
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Provizija
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone">
                <tr v-for="compenzation in compenzations.data" :key="compenzation.id" class="h-20 cursor-pointer hover:bg-gray-200" @click="viewCompenzation(compenzation)">
                    <td class="pl-6 py-4 whitespace-nowrap">
                        <strong>{{compenzation.name}}</strong>
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{formatDate(compenzation.date)}}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{formatCurrency(compenzation.amount)}}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{formatPercentage(compenzation.implementation_agreement.discount)}}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{formatPercentage(compenzation.realization_agreement.commission)}}
                    </td>
                </tr>
            </tbody>
        </table>
        <pagination class="mt-6" :links="compenzations.links" />
    </div>

</template>


<script>
import { Head } from '@inertiajs/inertia-vue3'
import AdminLayout from '@/mixins/adminLayout'
import Pagination from '@/Components/Pagination'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'

export default {

    layout: AdminLayout,

    components: {
        Head,
        Pagination,
        Button,
        Input
    },

    props: {
        compenzations: Object,
        filters: {
            type: Object,
            default: () => ({
                search: null
            })
        }
    },

    data() {
        return {
            localFilters: {
                search: this.filters?.search ?? ''
            },
            searchTimeout: null
        }
    },

    computed: {
        hasActiveFilters() {
            return !!this.localFilters.search;
        }
    },

    watch: {
        filters: {
            handler(newFilters) {
                if (newFilters) {
                    this.localFilters.search = newFilters.search ?? '';
                }
            },
            immediate: true
        },
        'localFilters.search': function () {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.applyFilters();
            }, 300);
        }
    },

    methods: {
        formatDate(dateString) {
            const date = new Date(dateString); // Parse the date string
            const day = String(date.getDate()).padStart(2, '0'); // Ensure 2-digit day
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Ensure 2-digit month
            const year = date.getFullYear(); // Get the year
            return `${day}.${month}.${year}`; // Format in DD.MM.YYYY
        },
        formatCurrency(value) {
            // Convert to locale-specific format with `,` as the decimal separator and `.` as thousands separator
            return new Intl.NumberFormat('sl-SI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value) + ' €'; // Add the Euro symbol at the end
        },
        formatPercentage(value) {
          if (value == null || value === '' || isNaN(Number(value))) return '';
          return `${Number(value).toLocaleString('sl-SI', { 
              minimumFractionDigits: 2, 
              maximumFractionDigits: 2 
          })} %`
        },
         viewCompenzation(compenzation) {
            this.$inertia.visit(this.route('compenzations.compenzation', {
                id: compenzation.id
            }))
        },
        addcompenzation() {
            // Počisti localStorage PRED navigacijo, da se komponente ne naložijo
            localStorage.removeItem('components');

            this.$inertia.get(this.route('compenzations.compenzation.new'));
        },
        applyFilters() {
            const params = {};
            
            if (this.localFilters.search) {
                params.search = this.localFilters.search;
            }

            this.$inertia.get(this.route('compenzations'), params, {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        },
        clearFilters() {
            this.localFilters.search = '';
            
            this.$inertia.get(this.route('compenzations'), {}, {
                preserveState: true,
                preserveScroll: true
            });
        }
    },
    beforeUnmount() {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
    }
}
</script>



