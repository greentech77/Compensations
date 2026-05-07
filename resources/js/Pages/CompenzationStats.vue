<template>
    <Head title="Statistika kompenzacij" />

    <div class="w-full bg-stone-15 p-8 rounded-md">
        <div class="flex justify-between items-end space-x-4 mb-6">
            <div class="flex space-x-4 items-end">
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Datum od</label>
                    <Input type="date" v-model="localFilters.date_from" class="w-48" />
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Datum do</label>
                    <Input type="date" v-model="localFilters.date_to" class="w-48" />
                </div>
                <Button class="button button--stone" @click="applyFilters">Filtriraj</Button>
                <Button v-if="hasActiveFilters" class="button button--stone" @click="clearFilters">Počisti</Button>
            </div>
            <div class="flex space-x-4">
                <Button class="button button--stone" @click="exportStats">Izvozi statistiko</Button>
                <Button class="button button--stone" @click="backToCompenzations">Nazaj</Button>
            </div>
        </div>

        <table class="bg-white w-full divide-y divide-stone">
            <thead class="text-white uppercase tracking-wider font-medium text-xs text-left">
                <tr>
                    <th class="pl-6 py-3 rounded-tl-md bg-blue">Kompenzacija</th>
                    <th class="pl-6 py-3 bg-blue">Prva stranka</th>
                    <th class="pl-6 py-3 bg-blue">Druga stranka</th>
                    <th class="pl-6 py-3 bg-blue">Znesek</th>
                    <th class="pl-6 py-3 bg-blue">Popust</th>
                    <th class="pl-6 py-3 bg-blue">Provizija</th>
                    <th class="pl-6 py-3 bg-blue">Razlika %</th>
                    <th class="pl-6 py-3 bg-blue">Razlika zneskov</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone">
                <tr v-for="row in stats.rows" :key="row.id" class="h-16">
                    <td class="pl-6 py-4 whitespace-nowrap"><strong>{{ row.name }}</strong></td>
                    <td class="pl-6 py-4 whitespace-nowrap">{{ row.first_entity }}</td>
                    <td class="pl-6 py-4 whitespace-nowrap">{{ row.second_entity }}</td>
                    <td class="pl-6 py-4 whitespace-nowrap">{{ formatCurrency(row.amount) }}</td>
                    <td class="pl-6 py-4 whitespace-nowrap">{{ formatPercentage(row.discount) }}</td>
                    <td class="pl-6 py-4 whitespace-nowrap">{{ formatPercentage(row.commission) }}</td>
                    <td class="pl-6 py-4 whitespace-nowrap">{{ formatPercentage(row.percent_diff) }}</td>
                    <td class="pl-6 py-4 whitespace-nowrap">{{ formatCurrency(row.amount_diff) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="mt-6 bg-white rounded-md p-4 shadow-sm border border-stone">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-xs uppercase text-gray-500">Število kompenzacij</div>
                    <div class="text-lg font-semibold">{{ stats.summary.count }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500">Povprečna razlika %</div>
                    <div class="text-lg font-semibold">{{ formatPercentage(stats.summary.avg_percent_diff) }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500">Skupna razlika zneskov</div>
                    <div class="text-lg font-semibold">{{ formatCurrency(stats.summary.sum_amount_diff) }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/mixins/adminLayout'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'

export default {
    layout: AdminLayout,

    components: {
        Head,
        Button,
        Input
    },

    props: {
        stats: {
            type: Object,
            required: true
        },
        filters: {
            type: Object,
            default: () => ({
                date_from: null,
                date_to: null
            })
        }
    },

    data() {
        return {
            localFilters: {
                date_from: this.filters?.date_from ?? null,
                date_to: this.filters?.date_to ?? null
            }
        }
    },

    computed: {
        hasActiveFilters() {
            return this.localFilters.date_from || this.localFilters.date_to
        }
    },

    methods: {
        formatCurrency(value) {
            return new Intl.NumberFormat('sl-SI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value) + ' €'
        },
        formatPercentage(value) {
            if (value == null || value === '' || isNaN(Number(value))) return ''
            return `${Number(value).toLocaleString('sl-SI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })} %`
        },
        applyFilters() {
            const params = {}
            if (this.localFilters.date_from) params.date_from = this.localFilters.date_from
            if (this.localFilters.date_to) params.date_to = this.localFilters.date_to
            this.$inertia.get(this.route('compenzations.stats'), params)
        },
        clearFilters() {
            this.localFilters.date_from = null
            this.localFilters.date_to = null
            this.$inertia.get(this.route('compenzations.stats'))
        },
        exportStats() {
            const params = new URLSearchParams()
            if (this.localFilters.date_from) params.append('date_from', this.localFilters.date_from)
            if (this.localFilters.date_to) params.append('date_to', this.localFilters.date_to)

            const baseUrl = this.route('compenzations.stats.export')
            const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl
            window.open(url, '_blank')
        },
        backToCompenzations() {
            this.$inertia.get(this.route('compenzations'))
        }
    }
}
</script>
