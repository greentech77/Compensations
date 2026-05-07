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

        <!-- Previously generated exports -->
        <div class="mt-8 bg-white p-6 rounded-md shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Pretekli izvozi</h2>
                <button
                    type="button"
                    class="text-sm text-blue hover:underline"
                    @click="refreshFiles"
                >
                    Osveži
                </button>
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
