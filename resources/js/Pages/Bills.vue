<template>
    <Head title="Računi"/>
    <div class="w-full bg-stone-15 p-8 rounded-md">
        <h2 class="text-2xl font-medium mb-6">Računi</h2>

        <!-- Form for creating specification -->
        <div class="bg-white rounded-md p-6 filter drop-shadow mb-6">
            <form @submit.prevent="createSpecification" class="space-y-4">
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex flex-col flex-1 min-w-[200px]">
                        <label class="text-sm font-medium text-gray-700 mb-1">Stranka:</label>
                        <Input
                            type="combobox"
                            v-model="form.entity"
                            :options="entityOptions"
                            :error="errors.entity_id || formErrors.entity_id"
                            @query-change="onEntitySearch"
                        />
                        <span v-if="formErrors.entity_id || errors.entity_id" class="text-red-600 text-sm mt-1">
                            {{ formErrors.entity_id || errors.entity_id }}
                        </span>
                        <span v-if="formErrors.error || errors.error" class="text-red-600 text-sm mt-1">
                            {{ formErrors.error || errors.error }}
                        </span>
                    </div>
                    <div class="flex flex-col min-w-[150px]">
                        <label class="text-sm font-medium text-gray-700 mb-1">Od datuma:</label>
                        <Input
                            type="date"
                            v-model="form.date_from"
                            :error="errors.date_from"
                            class="w-full"
                            :startingView="'year'"
                        />
                    </div>
                    <div class="flex flex-col min-w-[150px]">
                        <label class="text-sm font-medium text-gray-700 mb-1">Do datuma:</label>
                        <Input
                            type="date"
                            v-model="form.date_to"
                            :error="errors.date_to"
                            class="w-full"
                            :startingView="'year'"
                        />
                    </div>
                    <div class="flex items-end">
                        <Button 
                            type="submit" 
                            class="button button--stone"
                            :disabled="isSubmitting"
                            :loading="isSubmitting"
                        >
                            Ustvari specifikacijo
                        </Button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bills table -->
        <table class="bg-white w-full divide-y divide-stone">
            <thead class="text-white uppercase tracking-wider font-medium text-xs text-left">
                <tr>
                    <th scope="col" class="pl-6 py-3 rounded-tl-md bg-blue">
                        ID
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Stranka
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Znesek
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Leto
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Datum
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Kompenzacije
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Akcije
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone">
                <tr v-if="bills.data && bills.data.length === 0" class="h-20">
                    <td colspan="7" class="pl-6 py-4 text-center text-gray-500">
                        Ni računov
                    </td>
                </tr>
                <tr v-for="bill in bills.data" :key="bill.id" class="h-20 hover:bg-gray-200">
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{ bill.id }}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{ bill.entity ? bill.entity.company_name : 'N/A' }}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{ formatCurrency(bill.amount) }}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{ bill.year }}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{ formatDate(bill.date) }}
                    </td>
                    <td class="pl-6 py-4">
                        <span v-if="bill.compenzations && bill.compenzations.length > 0">
                            {{ bill.compenzations.map(c => c.name).join(', ') }}
                        </span>
                        <span v-else class="text-gray-400">Ni kompenzacij</span>
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        <a 
                            :href="route('bills.pdf.download', { id: bill.id })"
                            class="inline-flex items-center px-3 py-1 bg-blue text-white text-sm rounded hover:bg-blue-600 transition"
                            title="Prenos PDF računa"
                        >
                            <DownloadIcon class="h-4 w-4 mr-1"/>
                            Prenos
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <pagination class="mt-6" :links="bills.links" />
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/mixins/adminLayout'
import Pagination from '@/Components/Pagination'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import { DownloadIcon } from '@heroicons/vue/outline'

export default {
    layout: AdminLayout,

    components: {
        Head,
        Link,
        Pagination,
        Button,
        Input,
        DownloadIcon
    },

    props: {
        bills: Object,
        entities: Array,
        filters: {
            type: Object,
            default: () => ({
                entity_id: null,
                date_from: null,
                date_to: null
            })
        },
        selectedEntity: {
            type: Object,
            default: null
        },
        errors: {
            type: Object,
            default: () => ({})
        }
    },

    data() {
        return {
            form: {
                entity: null,
                date_from: null,
                date_to: null
            },
            localFilters: {
                entity: this.filters?.entity_id ? { key: this.filters.entity_id, label: '' } : null,
                date_from: this.filters?.date_from ? new Date(this.filters.date_from) : null,
                date_to: this.filters?.date_to ? new Date(this.filters.date_to) : null
            },
            isSubmitting: false,
            formErrors: {}
        }
    },

    computed: {
        entityOptions() {
            if (!this.entities) return [];
            return this.entities.map(entity => ({
                key: entity.id,
                label: entity.company_name
            }));
        },
        hasActiveFilters() {
            return this.localFilters.entity || this.localFilters.date_from || this.localFilters.date_to;
        }
    },

    watch: {
        filters: {
            handler(newFilters) {
                if (newFilters) {
                    this.localFilters.entity = newFilters.entity_id 
                        ? { key: newFilters.entity_id, label: this.getEntityName(newFilters.entity_id) } 
                        : null;
                    this.localFilters.date_from = newFilters.date_from ? new Date(newFilters.date_from) : null;
                    this.localFilters.date_to = newFilters.date_to ? new Date(newFilters.date_to) : null;
                }
            },
            immediate: true
        },
        entities: {
            handler() {
                // Update entity label when entities are loaded
                if (this.localFilters.entity && this.localFilters.entity.key) {
                    this.localFilters.entity.label = this.getEntityName(this.localFilters.entity.key);
                }
                // Initialize form entity if selectedEntity is provided
                if (this.selectedEntity && !this.form.entity) {
                    this.form.entity = {
                        key: this.selectedEntity.id,
                        label: this.selectedEntity.company_name
                    };
                }
            },
            immediate: true
        },
        selectedEntity: {
            handler(newEntity) {
                if (newEntity) {
                    this.form.entity = {
                        key: newEntity.id,
                        label: newEntity.company_name
                    };
                    this.form.date_from = this.filters?.date_from ? new Date(this.filters.date_from) : null;
                    this.form.date_to = this.filters?.date_to ? new Date(this.filters.date_to) : null;
                }
            },
            immediate: true
        }
    },

    methods: {
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}.${month}.${year}`;
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('sl-SI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value) + ' €';
        },
        getEntityName(entityId) {
            const entity = this.entities?.find(e => e.id === entityId);
            return entity ? entity.company_name : '';
        },
        createSpecification() {
            console.log('createSpecification called', {
                entity: this.form.entity,
                date_from: this.form.date_from,
                date_to: this.form.date_to
            });

            // Clear previous errors
            this.formErrors = {};

            // Validate form
            if (!this.form.entity || !this.form.entity.key) {
                this.formErrors.entity_id = 'Prosimo izberite stranko.';
                return;
            }

            if (!this.form.date_from) {
                this.formErrors.date_from = 'Prosimo izberite datum od.';
                return;
            }

            if (!this.form.date_to) {
                this.formErrors.date_to = 'Prosimo izberite datum do.';
                return;
            }

            const payload = {
                entity_id: this.form.entity.key,
                date_from: this.formatDateForRequest(this.form.date_from),
                date_to: this.formatDateForRequest(this.form.date_to)
            };

            console.log('Sending request with payload:', payload);
            console.log('Route:', this.route('bills.specification'));

            this.isSubmitting = true;

            this.$inertia.post(this.route('bills.specification'), payload, {
                preserveScroll: true,
                onStart: () => {
                    console.log('Request started');
                    this.formErrors = {};
                },
                onSuccess: (page) => {
                    console.log('Request successful', page);
                    this.isSubmitting = false;
                    this.formErrors = {};
                    // Reset form after successful creation
                    this.form.entity = null;
                    this.form.date_from = null;
                    this.form.date_to = null;
                    // Reload bills to show the new bill in the table
                    this.$inertia.reload({ only: ['bills'] });
                },
                onError: (errors) => {
                    console.error('Napaka pri ustvarjanju specifikacije:', errors);
                    this.isSubmitting = false;
                    // Store errors for display
                    this.formErrors = errors;
                },
                onFinish: () => {
                    console.log('Request finished');
                    this.isSubmitting = false;
                }
            });
        },
        applyFilters() {
            const params = {};

            if (this.localFilters.entity) {
                params.entity_id = this.localFilters.entity.key;
            }

            if (this.localFilters.date_from) {
                params.date_from = this.formatDateForRequest(this.localFilters.date_from);
            }

            if (this.localFilters.date_to) {
                params.date_to = this.formatDateForRequest(this.localFilters.date_to);
            }

            this.$inertia.get(this.route('bills'), params, {
                preserveState: true,
                preserveScroll: true
            });
        },
        clearFilters() {
            this.localFilters.entity = null;
            this.localFilters.date_from = null;
            this.localFilters.date_to = null;

            this.$inertia.get(this.route('bills'), {}, {
                preserveState: true,
                preserveScroll: true
            });
        },
        formatDateForRequest(date) {
            if (!date) return null;
            
            // Handle Date object
            if (date instanceof Date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
            
            // Handle string (already formatted)
            if (typeof date === 'string') {
                // If it's already in YYYY-MM-DD format, return as is
                if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
                    return date;
                }
                // Otherwise try to parse it
                const d = new Date(date);
                if (!isNaN(d.getTime())) {
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }
            }
            
            console.error('Invalid date format:', date);
            return null;
        },
        onEntitySearch(query) {
            // Query change is handled by Combobox component
            // This method can be used for additional logic if needed
        }
    }
}
</script>

