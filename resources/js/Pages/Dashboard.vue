<template>
    <Head title="Nadzorna plošča" />

    <div class="w-full max-w-none -mx-4 space-y-6 rounded-md bg-stone-15 p-4 md:-mx-6 md:p-8">
        <!-- 1. KPI strip ------------------------------------------------- -->
        <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="bg-white rounded-md border border-stone p-4 shadow-card">
                <div class="text-xs uppercase text-gray-500 tracking-wider">Aktivne kompenzacije</div>
                <div class="text-2xl font-semibold mt-1">{{ kpis.compenzations_active ?? 0 }}</div>
                <div class="text-xs text-gray-500 mt-1">še ne zaključene</div>
            </div>
            <div class="bg-white rounded-md border border-stone p-4 shadow-card">
                <div class="text-xs uppercase text-gray-500 tracking-wider">Zaključene {{ kpis.year }}</div>
                <div class="text-2xl font-semibold mt-1">{{ kpis.compenzations_finished_year ?? 0 }}</div>
                <div class="text-xs text-gray-500 mt-1">finished = true</div>
            </div>
            <div class="bg-white rounded-md border border-stone p-4 shadow-card">
                <div class="text-xs uppercase text-gray-500 tracking-wider">Skupni znesek {{ kpis.year }}</div>
                <div class="text-2xl font-semibold mt-1">{{ formatCurrency(kpis.total_amount_year ?? 0) }}</div>
                <div class="text-xs text-gray-500 mt-1">vsota amount</div>
            </div>
            <div class="bg-white rounded-md border border-stone p-4 shadow-card">
                <div class="text-xs uppercase text-gray-500 tracking-wider">Povp. razlika %</div>
                <div class="text-2xl font-semibold mt-1">
                    {{ kpis.avg_percent_diff_year != null ? formatPercentage(kpis.avg_percent_diff_year) : '–' }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Razlika zneskov: {{ formatCurrency(kpis.sum_amount_diff_year ?? 0) }}
                </div>
            </div>
            <div class="bg-white rounded-md border border-stone p-4 shadow-card">
                <div class="text-xs uppercase text-gray-500 tracking-wider">Število podjetij</div>
                <div class="text-2xl font-semibold mt-1">{{ kpis.entities_count ?? 0 }}</div>
                <div class="text-xs text-gray-500 mt-1">vseh strank</div>
            </div>
            <div class="bg-white rounded-md border border-stone p-4 shadow-card">
                <div class="text-xs uppercase text-gray-500 tracking-wider">Računi {{ kpis.year }}</div>
                <div class="text-2xl font-semibold mt-1">{{ kpis.bills_year_count ?? 0 }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ formatCurrency(kpis.bills_year_amount ?? 0) }}</div>
            </div>
        </section>

        <!-- 2. Charts row ------------------------------------------------- -->
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-md border border-stone p-6 shadow-card">
                <h2 class="text-lg font-semibold mb-4">Mesečni znesek kompenzacij</h2>
                <div class="h-72">
                    <Bar :data="amountChartData" :options="amountChartOptions" />
                </div>
            </div>
            <div class="bg-white rounded-md border border-stone p-6 shadow-card">
                <h2 class="text-lg font-semibold mb-4">Število dokumentov po mesecih</h2>
                <div class="h-72">
                    <Line :data="countChartData" :options="countChartOptions" />
                </div>
            </div>
        </section>

        <!-- 3. Tables row: latest compenzations + bills ------------------ -->
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-md border border-stone shadow-card overflow-hidden">
                <div class="flex flex-col gap-2 border-b border-stone-15 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <h2 class="text-lg font-semibold">Zadnje kompenzacije</h2>
                    <Link :href="route('compenzations')" class="text-sm text-blue hover:text-blue-hover">
                        Vse kompenzacije →
                    </Link>
                </div>
                <div class="overflow-x-auto touch-pan-x">
                <table class="min-w-[36rem] w-full divide-y divide-stone-15">
                    <thead class="text-white uppercase tracking-wider font-medium text-xs text-left">
                        <tr>
                            <th class="pl-6 py-3 bg-blue">Naziv</th>
                            <th class="pl-6 py-3 bg-blue">Datum</th>
                            <th class="pl-6 py-3 bg-blue">Znesek</th>
                            <th class="pl-6 py-3 bg-blue pr-6">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-15">
                        <tr v-if="recentCompenzations.length === 0">
                            <td colspan="4" class="px-6 py-6 text-center text-gray-500">Ni kompenzacij.</td>
                        </tr>
                        <tr
                            v-for="row in recentCompenzations"
                            :key="row.id"
                            class="h-14 cursor-pointer hover:bg-stone-15"
                            @click="goToCompenzation(row.id)"
                        >
                            <td class="pl-6 py-3 whitespace-nowrap">
                                <strong>{{ row.name }}</strong>
                                <div class="text-xs text-gray-500">{{ row.first_entity }}<span v-if="row.second_entity"> ↔ {{ row.second_entity }}</span></div>
                            </td>
                            <td class="pl-6 py-3 whitespace-nowrap">{{ formatDate(row.date) }}</td>
                            <td class="pl-6 py-3 whitespace-nowrap">{{ formatCurrency(row.amount) }}</td>
                            <td class="pl-6 py-3 whitespace-nowrap pr-6">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="row.finished
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-orange/20 text-orange-hover'"
                                >
                                    {{ row.finished ? 'Zaključena' : 'V teku' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>

            <div class="bg-white rounded-md border border-stone shadow-card overflow-hidden">
                <div class="flex flex-col gap-2 border-b border-stone-15 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <h2 class="text-lg font-semibold">Zadnji računi</h2>
                    <Link :href="route('bills')" class="text-sm text-blue hover:text-blue-hover">
                        Vsi računi →
                    </Link>
                </div>
                <div class="overflow-x-auto touch-pan-x">
                <table class="min-w-[32rem] w-full divide-y divide-stone-15">
                    <thead class="text-white uppercase tracking-wider font-medium text-xs text-left">
                        <tr>
                            <th class="pl-6 py-3 bg-blue">ID</th>
                            <th class="pl-6 py-3 bg-blue">Stranka</th>
                            <th class="pl-6 py-3 bg-blue">Znesek</th>
                            <th class="pl-6 py-3 bg-blue pr-6">Datum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-15">
                        <tr v-if="recentBills.length === 0">
                            <td colspan="4" class="px-6 py-6 text-center text-gray-500">Ni računov.</td>
                        </tr>
                        <tr
                            v-for="bill in recentBills"
                            :key="bill.id"
                            class="h-14 hover:bg-stone-15"
                        >
                            <td class="pl-6 py-3 whitespace-nowrap">{{ bill.id }}</td>
                            <td class="pl-6 py-3 whitespace-nowrap">{{ bill.entity_name || '—' }}</td>
                            <td class="pl-6 py-3 whitespace-nowrap">{{ formatCurrency(bill.amount) }}</td>
                            <td class="pl-6 py-3 whitespace-nowrap pr-6">{{ formatDate(bill.date) }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </section>

        <!-- 4. Side panels: warnings, top entities, quick actions --------- -->
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="bg-white rounded-md border border-stone shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-stone-15 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Pozor: nezaključene</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange/20 text-orange-hover">
                        {{ unfinished.length }}
                    </span>
                </div>
                <div class="divide-y divide-stone-15">
                    <p v-if="unfinished.length === 0" class="px-6 py-6 text-center text-gray-500">
                        Vse kompenzacije so zaključene.
                    </p>
                    <div
                        v-for="row in unfinished"
                        :key="row.id"
                        class="px-6 py-3 cursor-pointer hover:bg-stone-15"
                        @click="goToCompenzation(row.id)"
                    >
                        <div class="flex items-center justify-between">
                            <strong class="text-sm">{{ row.name }}</strong>
                            <span class="text-xs text-gray-500">{{ formatDate(row.date_finished || row.date) }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ row.first_entity }}<span v-if="row.second_entity"> ↔ {{ row.second_entity }}</span>
                        </div>
                        <div class="text-xs font-medium mt-1">{{ formatCurrency(row.amount) }}</div>
                    </div>
                </div>
                <div v-if="unfinished.length > 0" class="px-6 py-3 border-t border-stone-15">
                    <Link :href="route('compenzations')" class="text-sm text-blue hover:text-blue-hover">
                        Pojdi na kompenzacije →
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-md border border-stone shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-stone-15">
                    <h2 class="text-lg font-semibold">Top 5 strank</h2>
                </div>
                <ol class="divide-y divide-stone-15">
                    <li v-if="topEntities.length === 0" class="px-6 py-6 text-center text-gray-500">
                        Ni podatkov.
                    </li>
                    <li
                        v-for="(row, idx) in topEntities"
                        :key="row.id"
                        class="px-6 py-3 flex items-center justify-between cursor-pointer hover:bg-stone-15"
                        @click="goToEntity(row.id)"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-6 h-6 inline-flex items-center justify-center rounded-full bg-blue text-white text-xs font-semibold">
                                {{ idx + 1 }}
                            </span>
                            <div class="min-w-0">
                                <div class="font-medium truncate" :title="row.company_name">{{ row.company_name }}</div>
                                <div class="text-xs text-gray-500">{{ row.compenzations_count }} kompenzacij</div>
                            </div>
                        </div>
                        <div class="text-sm font-semibold whitespace-nowrap">{{ formatCurrency(row.total_amount) }}</div>
                    </li>
                </ol>
            </div>

            <div class="bg-white rounded-md border border-stone shadow-card p-6">
                <h2 class="text-lg font-semibold mb-4">Hitre akcije</h2>
                <div class="space-y-3">
                    <button
                        type="button"
                        class="button button--stone w-full"
                        @click="newCompenzation"
                    >
                        Dodaj kompenzacijo
                    </button>
                    <button
                        type="button"
                        class="button button--stone w-full"
                        @click="newEntity"
                    >
                        Dodaj podjetje
                    </button>
                    <button
                        type="button"
                        class="button button--stone w-full"
                        @click="goToExports"
                    >
                        Izvozi
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-6">
                    Podatki posodobljeni: {{ formatDateTime(summary.generated_at) }}
                </p>
            </div>
        </section>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/mixins/adminLayout'
import { Bar, Line } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler,
} from 'chart.js'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler,
)

const BRAND = {
    blue: '#002640',
    stone: '#3D667A',
    cyan: '#25A0B5',
    orange: '#ED7D31',
}

const SL_MONTHS_LONG = [
    'januar', 'februar', 'marec', 'april', 'maj', 'junij',
    'julij', 'avgust', 'september', 'oktober', 'november', 'december',
]

export default {
    layout: AdminLayout,

    components: {
        Head,
        Link,
        Bar,
        Line,
    },

    props: {
        summary: {
            type: Object,
            required: true,
            default: () => ({
                kpis: {},
                monthly: [],
                recent_compenzations: [],
                recent_bills: [],
                top_entities: [],
                unfinished: [],
                generated_at: null,
            }),
        },
    },

    computed: {
        kpis() {
            return this.summary?.kpis ?? {}
        },
        monthly() {
            return this.summary?.monthly ?? []
        },
        recentCompenzations() {
            return this.summary?.recent_compenzations ?? []
        },
        recentBills() {
            return this.summary?.recent_bills ?? []
        },
        topEntities() {
            return this.summary?.top_entities ?? []
        },
        unfinished() {
            return this.summary?.unfinished ?? []
        },
        amountChartData() {
            const labels = this.monthly.map((m) => m.label)
            const amounts = this.monthly.map((m) => m.amount)
            return {
                labels,
                datasets: [
                    {
                        label: 'Znesek kompenzacij (€)',
                        data: amounts,
                        backgroundColor: BRAND.blue,
                        borderRadius: 4,
                        maxBarThickness: 32,
                    },
                ],
            }
        },
        amountChartOptions() {
            const fmt = this.formatCurrency
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => fmt(ctx.parsed.y),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => fmt(value),
                        },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            }
        },
        countChartData() {
            const labels = this.monthly.map((m) => m.label)
            return {
                labels,
                datasets: [
                    {
                        label: 'Kompenzacije',
                        data: this.monthly.map((m) => m.count),
                        borderColor: BRAND.cyan,
                        backgroundColor: 'rgba(37, 160, 181, 0.15)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3,
                    },
                    {
                        label: 'Računi',
                        data: this.monthly.map((m) => m.bills_count),
                        borderColor: BRAND.orange,
                        backgroundColor: 'rgba(237, 125, 49, 0.15)',
                        tension: 0.3,
                        fill: false,
                        pointRadius: 3,
                    },
                ],
            }
        },
        countChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, stepSize: 1 },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            }
        },
    },

    methods: {
        formatCurrency(value) {
            const num = Number(value)
            if (!Number.isFinite(num)) return ''
            return new Intl.NumberFormat('sl-SI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(num) + ' €'
        },
        formatPercentage(value) {
            if (value == null || value === '' || isNaN(Number(value))) return ''
            return `${Number(value).toLocaleString('sl-SI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })} %`
        },
        formatDate(value) {
            if (!value) return ''
            const date = new Date(value)
            if (isNaN(date.getTime())) return ''
            const day = String(date.getDate()).padStart(2, '0')
            const month = String(date.getMonth() + 1).padStart(2, '0')
            const year = date.getFullYear()
            return `${day}.${month}.${year}`
        },
        formatDateTime(value) {
            if (!value) return ''
            const date = new Date(value)
            if (isNaN(date.getTime())) return ''
            const day = String(date.getDate()).padStart(2, '0')
            const monthName = SL_MONTHS_LONG[date.getMonth()]
            const year = date.getFullYear()
            const hh = String(date.getHours()).padStart(2, '0')
            const mm = String(date.getMinutes()).padStart(2, '0')
            return `${day}. ${monthName} ${year}, ${hh}:${mm}`
        },
        goToCompenzation(id) {
            this.$inertia.visit(this.route('compenzations.compenzation', { id }))
        },
        goToEntity(id) {
            this.$inertia.visit(this.route('entities.entity', { id }))
        },
        newCompenzation() {
            localStorage.removeItem('components')
            this.$inertia.get(this.route('compenzations.compenzation.new'))
        },
        newEntity() {
            this.$inertia.get(this.route('entities.entity.register'))
        },
        goToExports() {
            this.$inertia.get(this.route('exports.index'))
        },
    },
}
</script>
