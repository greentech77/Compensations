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
                    <th scope="col" class="pl-6 py-3 rounded-tl-md bg-blue cursor-pointer select-none" @click="toggleSort('name')">
                        <span class="inline-flex items-center gap-1">
                            Naziv kompenzacije
                            <span class="text-[10px] leading-none opacity-90">{{ sortIndicator('name') }}</span>
                        </span>
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue cursor-pointer select-none" @click="toggleSort('date')">
                        <span class="inline-flex items-center gap-1">
                            Datum
                            <span class="text-[10px] leading-none opacity-90">{{ sortIndicator('date') }}</span>
                        </span>
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
                    <th scope="col" class="pl-6 py-3 rounded-tr-md bg-blue">
                        Dokumenti
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
                    <td class="pl-6 py-4 whitespace-nowrap" @click.stop>
                        <div class="flex items-center gap-2">
                            <a
                                v-if="compenzation.proposal && compenzation.proposal.file_path"
                                :href="route('compenzations.compenzation.pdf.download', { id: compenzation.id, type: 'proposal' })"
                                class="inline-flex items-center px-3 py-1.5 bg-blue text-white text-xs rounded hover:bg-blue-600 transition"
                                title="Predlog kompenzacije"
                            >
                                <DownloadIcon class="h-4 w-4 mr-1"/>
                                Predlog kompenzacije
                            </a>
                            <a
                                v-if="compenzation.implementation_agreement && compenzation.implementation_agreement.file_path"
                                :href="route('compenzations.compenzation.pdf.download', { id: compenzation.id, type: 'implementation' })"
                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition"
                                title="Pogodba o izvedbi"
                            >
                                <DownloadIcon class="h-4 w-4 mr-1"/>
                                Pogodba o izvedbi
                            </a>
                            <a
                                v-if="compenzation.realization_agreement && compenzation.realization_agreement.file_path"
                                :href="route('compenzations.compenzation.pdf.download', { id: compenzation.id, type: 'realization' })"
                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition"
                                title="Pogodba o unovčenju"
                            >
                                <DownloadIcon class="h-4 w-4 mr-1"/>
                                Pogodba o unovčenju
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <pagination class="mt-6" :links="compenzations.links" />
    </div>

</template>


<script>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/mixins/adminLayout'
import Pagination from '@/Components/Pagination'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import { DownloadIcon } from '@heroicons/vue/outline'

export default {

    layout: AdminLayout,

    components: {
        Head,
        Pagination,
        Button,
        Input,
        DownloadIcon
    },

    props: {
        compenzations: Object,
        filters: {
            type: Object,
            default: () => ({
                search: null,
                sort: 'date',
                direction: 'asc'
            })
        }
    },

    data() {
        return {
            localFilters: {
                search: this.filters?.search ?? ''
            },
            sort: this.filters?.sort ?? 'date',
            direction: this.filters?.direction ?? 'asc',
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
                    this.sort = newFilters.sort ?? 'date';
                    this.direction = newFilters.direction ?? 'asc';
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
            const params = this.buildQueryParams();

            this.$inertia.get(this.route('compenzations'), params, {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        },
        clearFilters() {
            this.localFilters.search = '';

            // Reset to defaults but preserve current sort selection so the user
            // does not lose their column ordering when clearing the search.
            this.$inertia.get(this.route('compenzations'), this.buildQueryParams({ search: null }), {
                preserveState: true,
                preserveScroll: true
            });
        },
        buildQueryParams(overrides = {}) {
            const params = {};

            if (this.localFilters.search) {
                params.search = this.localFilters.search;
            }

            // Only include sort params when they differ from the defaults so
            // URLs stay clean for the common case.
            if (this.sort && this.sort !== 'date') {
                params.sort = this.sort;
            }
            if (this.direction && this.direction !== 'asc') {
                params.direction = this.direction;
            }

            return Object.assign(params, overrides);
        },
        toggleSort(column) {
            if (this.sort === column) {
                this.direction = this.direction === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort = column;
                this.direction = 'asc';
            }

            this.$inertia.get(this.route('compenzations'), this.buildQueryParams(), {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        },
        sortIndicator(column) {
            if (this.sort !== column) {
                return '▲▼';
            }
            return this.direction === 'asc' ? '▲' : '▼';
        }
    },
    beforeUnmount() {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
    }
}
</script>



