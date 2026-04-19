<template>
    <Head title="Izvoz računov" />
    
    <div class="w-full bg-stone-15 p-8 rounded-md">
        <h1 class="text-2xl font-bold mb-6">Izvoz računov</h1>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-md shadow-sm">
                <h2 class="text-lg font-semibold mb-4">Parametri izvoza</h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Datum od
                            </label>
                            <Input
                                v-model="form.date_from"
                                type="date"
                                name="date_from"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Datum do
                            </label>
                            <Input
                                v-model="form.date_to"
                                type="date"
                                name="date_to"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export button -->
            <div class="flex justify-end space-x-4">
                <Button
                    type="button"
                    @click="exportBills"
                    :loading="form.processing"
                    :disabled="form.processing || !normalizedDateFrom || !normalizedDateTo"
                    class="button button--blue"
                >
                    Izvozi račune
                </Button>
            </div>
        </div>

        <!-- Info section -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-md p-4">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Informacije</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• XML format je primeren za programsko obdelavo</li>
                <li>• Izvoz je omejen na izbran interval od/do</li>
                <li>• Izvoženi podatki vključujejo: ID, stranko, znesek, leto, datum in povezane kompenzacije</li>
            </ul>
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
        exportBills() {
            this.form.processing = true
            const params = new URLSearchParams({
                format: 'xml',
                date_from: this.normalizedDateFrom,
                date_to: this.normalizedDateTo
            })
            const url = `${this.route('exports.bills.export')}?${params.toString()}`
            window.open(url, '_blank')

            // Reset processing after a short delay
            setTimeout(() => {
                this.form.processing = false
            }, 1000)
        }
    }
}
</script>

