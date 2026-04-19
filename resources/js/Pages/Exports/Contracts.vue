<template>
    <Head title="Izvoz pogodb" />

    <div class="w-full bg-stone-15 p-8 rounded-md">
        <h1 class="text-2xl font-bold mb-6">Izvoz pogodb</h1>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-md shadow-sm">
                <h2 class="text-lg font-semibold mb-4">Parametri izvoza</h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Datum od</label>
                            <Input v-model="form.date_from" type="date" name="date_from" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Datum do</label>
                            <Input v-model="form.date_to" type="date" name="date_to" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <Button
                    type="button"
                    @click="exportContracts"
                    :loading="form.processing"
                    :disabled="form.processing || !normalizedDateFrom || !normalizedDateTo"
                    class="button button--blue"
                >
                    Izvozi pogodbe
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
                date_to: '',
                processing: false
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
        exportContracts() {
            this.form.processing = true
            const params = new URLSearchParams({
                format: 'xml',
                date_from: this.normalizedDateFrom,
                date_to: this.normalizedDateTo
            })
            const url = `${this.route('exports.contracts.export')}?${params.toString()}`
            window.open(url, '_blank')

            setTimeout(() => {
                this.form.processing = false
            }, 1000)
        }
    }
}
</script>
