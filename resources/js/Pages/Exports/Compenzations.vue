<template>
    <Head title="Kompenzacije - Izvoz in statistika" />

    <div class="w-full bg-stone-15 p-8 rounded-md">
        <h1 class="text-2xl font-bold mb-6">Kompenzacije</h1>

        <div class="bg-white p-6 rounded-md shadow-sm border border-stone">
            <h2 class="text-lg font-semibold mb-4">Obdobje</h2>

            <div class="flex flex-wrap items-end gap-4">
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Datum od</label>
                    <Input type="date" v-model="form.date_from" class="w-48" />
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Datum do</label>
                    <Input type="date" v-model="form.date_to" class="w-48" />
                </div>
                <Button
                    type="button"
                    class="button button--stone"
                    :disabled="!normalizedDateFrom || !normalizedDateTo"
                    @click="showCompenzations"
                >
                    Prikaži
                </Button>
                <Button
                    type="button"
                    class="button button--stone"
                    :disabled="!normalizedDateFrom || !normalizedDateTo"
                    @click="exportXml"
                >
                    Izvozi XML
                </Button>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
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
    data() {
        return {
            form: {
                date_from: '',
                date_to: ''
            }
        }
    },
    computed: {
        normalizedDateFrom() {
            return this.normalizeDate(this.form.date_from)
        },
        normalizedDateTo() {
            return this.normalizeDate(this.form.date_to)
        }
    },
    methods: {
        normalizeDate(value) {
            if (!value) return ''
            if (typeof value === 'string') return value
            if (value instanceof Date && !Number.isNaN(value.getTime())) {
                const year = value.getFullYear()
                const month = String(value.getMonth() + 1).padStart(2, '0')
                const day = String(value.getDate()).padStart(2, '0')
                return `${year}-${month}-${day}`
            }
            return ''
        },
        showCompenzations() {
            this.$inertia.get(this.route('compenzations.stats'), {
                date_from: this.normalizedDateFrom,
                date_to: this.normalizedDateTo
            })
        },
        exportXml() {
            const params = new URLSearchParams({
                format: 'xml',
                date_from: this.normalizedDateFrom,
                date_to: this.normalizedDateTo
            })
            window.open(`${this.route('compenzations.export')}?${params.toString()}`, '_blank')
        }
    }
}
</script>
