<template>
    <Head title="Izvoz računov" />
    
    <div class="w-full max-w-none -mx-4 rounded-md bg-stone-15 p-4 md:-mx-6 md:p-8">
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

        <!-- Previously generated exports -->
        <div class="mt-8 bg-white p-6 rounded-md shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Pretekli izvozi</h2>
                <Button
                    type="button"
                    class="button button--green text-sm !py-1.5 !px-4"
                    @click="refreshFiles"
                >
                    Osveži
                </Button>
            </div>

            <p v-if="!files.length" class="text-sm text-gray-500">
                Ni še shranjenih izvozov. Po prvem izvozu se bodo pojavili tukaj.
            </p>

            <table v-else class="w-full text-sm">
                <thead class="text-left text-gray-600 border-b">
                    <tr>
                        <th class="py-2 pr-4">Datoteka</th>
                        <th class="py-2 pr-4">Ustvarjeno</th>
                        <th class="py-2 pr-4">Velikost</th>
                        <th class="py-2 text-right">Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="file in files"
                        :key="file.name"
                        class="border-b last:border-b-0 hover:bg-stone-50"
                    >
                        <td class="py-2 pr-4 font-mono text-xs break-all">{{ file.name }}</td>
                        <td class="py-2 pr-4">{{ file.modified_at_human }}</td>
                        <td class="py-2 pr-4">{{ formatSize(file.size) }}</td>
                        <td class="py-2 text-right">
                            <a
                                :href="file.url"
                                class="button button--blue inline-flex items-center gap-2 text-sm !py-1 !px-4"
                                target="_blank"
                                rel="noopener"
                            >
                                <DownloadIcon class="w-4 h-4" />
                                Prenesi
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Info section -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-md p-4">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Informacije</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• XML format je primeren za programsko obdelavo</li>
                <li>• Izvoz je omejen na izbran interval od/do</li>
                <li>• Izvoženi podatki vključujejo: ID, stranko, znesek, leto, datum in povezane kompenzacije</li>
                <li>• Ustvarjene datoteke se shranijo na strežniku in so kasneje na voljo v seznamu zgoraj</li>
            </ul>
        </div>
    </div>
</template>

<script>
import { Head, router } from '@inertiajs/vue3'
import { DownloadIcon } from '@heroicons/vue/outline'
import AdminLayout from '@/mixins/adminLayout'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'

export default {
    layout: AdminLayout,

    components: {
        Head,
        Button,
        Input,
        DownloadIcon
    },

    props: {
        files: {
            type: Array,
            default: () => []
        }
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

            // After the download starts, re-pull the page so the new file appears in the list.
            setTimeout(() => {
                this.form.processing = false
                this.refreshFiles()
            }, 1500)
        },
        refreshFiles() {
            router.reload({
                only: ['files'],
                preserveState: true,
                preserveScroll: true
            })
        },
        formatSize(bytes) {
            if (!bytes && bytes !== 0) return '—'
            if (bytes < 1024) return `${bytes} B`
            if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} kB`
            return `${(bytes / (1024 * 1024)).toFixed(2)} MB`
        }
    }
}
</script>
