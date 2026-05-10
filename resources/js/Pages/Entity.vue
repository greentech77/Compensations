<template>
    <Head :title="entity.company_name"/>

    <div class="w-full max-w-none -mx-4 rounded-md bg-stone-15 p-4 md:-mx-6 md:p-8">
        <section class="flex flex-wrap gap-4 lg:flex-nowrap lg:gap-0 lg:space-x-4">
            <div class="w-full space-y-4 rounded-md bg-white p-4 shadow-card filter drop-shadow sm:p-6 lg:w-1/4">
                <h1 class="text-2xl font-medium ">{{entity.company_name}}</h1>
            </div>

            <div class="mt-4 ml-0 w-full space-y-4 sm:ml-10 lg:mt-0 lg:ml-0 lg:w-3/4">
                <section class="relative z-10 rounded-md bg-white p-4 shadow-card filter drop-shadow sm:p-6">
                    <form @submit.prevent="onSubmitSection(formdata)" class="space-y-4">
                        <div class="flex items-center">
                            <h2 class="text-lg font-medium flex-auto">Osnovni podatki</h2>
                            <button class="button button--icon pl-3 text-stone hover:text-stone-hover" :disabled="formdata.edit" @click.prevent="toggleEditMode(formdata)">
                                <PencilAltIcon class="h-6 w-6"/>
                            </button>
                        </div>
                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup class="w-full md:w-1/4 flex-auto" v-model="formdata.form.vat_num" label="Davčna številka" :error="formdata.form.errors['vat_num']" @change="formdata.form.clearErrors('vat_num')" :edit="formdata.edit"/> 
                            <InputGroup class="w-full md:w-1/4 flex-auto" v-model="formdata.form.registration_num" label="Matična številka" :error="formdata.form.errors['registration_num']" @change="formdata.form.clearErrors('registration_num')" :edit="formdata.edit"/>
                            <InputGroup class="w-full md:w-1/4 flex-auto" v-model="formdata.form.name" label="Ime" :error="formdata.form.errors['name']" @change="formdata.form.clearErrors('name')" :edit="formdata.edit"/>
                            <InputGroup class="w-full md:w-1/4 flex-auto" v-model="formdata.form.lastname" label="Priimek" :error="formdata.form.errors['lastname']" @change="formdata.form.clearErrors('lastname')" :edit="formdata.edit"/>
                        </div>
                        <div class="flex items-center">
                            <h2 class="text-lg font-medium flex-auto">Kontaktni podatki</h2>
                        </div>
                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.email" label="E-mail" :error="formdata.form.errors['email']" @change="formdata.form.clearErrors('email')" :edit="formdata.edit"/> 
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.mobile" label="GSM" :error="formdata.form.errors['mobile']" @change="formdata.form.clearErrors('mobile')" :edit="formdata.edit"/>
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.phone" label="Telefon" :error="formdata.form.errors['phone']" @change="formdata.form.clearErrors('phone')" :edit="formdata.edit"/>
                        </div>
                        <div class="flex items-center">
                            <h2 class="text-lg font-medium flex-auto">Naslov in sedež podjetja</h2>
                        </div>
                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.address" label="Ulica in hišna številka" :error="formdata.form.errors['address']" @change="formdata.form.clearErrors('address')" :edit="formdata.edit"/> 
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.post_num" label="Poštna številka" :error="formdata.form.errors['post_num']" @change="formdata.form.clearErrors('post_num')" :edit="formdata.edit"/>
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.post_town" label="Pošta" :error="formdata.form.errors['post_town']" @change="formdata.form.clearErrors('post_town')" :edit="formdata.edit"/>
                        </div>
                        <div class="flex items-center">
                            <h2 class="text-lg font-medium flex-auto">Transakcijski račun</h2>
                        </div>
                        <div class="flex space-y-4 md:space-y-0 md:space-x-4 flex-wrap md:flex-nowrap">
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.bank_account" label="IBAN številka računa" :error="formdata.form.errors['bank_account']" @change="formdata.form.clearErrors('bank_account')" :edit="formdata.edit"/> 
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.bank_bic" label="BIC banke" :error="formdata.form.errors['bank_bic']" @change="formdata.form.clearErrors('bank_bic')" :edit="formdata.edit"/>
                            <InputGroup class="w-full md:w-1/3 flex-auto" v-model="formdata.form.bank_name" label="Naziv banke" :error="formdata.form.errors['bank_name']" @change="formdata.form.clearErrors('bank_name')" :edit="formdata.edit"/>
                        </div>
                        
                        
                        <div class="flex justify-end space-x-4" v-if="formdata.edit">
                            <Button class="button button--white" @click.prevent="resetSection(formdata)" :disabled="formdata.form.processing">Prekliči</Button>
                            <Button class="button button--stone" type="submit" :loading="formdata.form.processing">Shrani</Button>
                        </div>
                    </form>
                </section>
            </div>
        </section>        
    </div>

    <div class="mx-auto w-full max-w-8xl min-[2100px]:max-w-none flex-auto justify-center px-0 py-6 md:py-12">
        <h2 class="mb-5 flex-auto text-lg font-medium">Kompenzacije</h2>
        <div class="w-full max-w-none rounded-md bg-stone-15 p-4 md:p-8">
            <div v-if="entity.compenzations && entity.compenzations.length > 0">
                <div class="-mx-1 overflow-x-auto rounded-md border border-stone bg-white touch-pan-x md:mx-0">
                <table class="min-w-[56rem] w-full divide-y divide-stone bg-white">
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
                                PDF dokumenti
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone">
                        <tr v-for="compenzation in sortedCompenzations" :key="compenzation.id">
                            <td class="pl-6 py-4 text-sm">
                                <Link :href="route('compenzations.compenzation', { id: compenzation.id })" class="text-blue hover:underline">
                                    {{ compenzation.name }}
                                </Link>
                            </td>
                            <td class="pl-6 py-4 text-sm">
                                {{ formatDate(compenzation.date) }}
                            </td>
                            <td class="pl-6 py-4 text-sm">
                                {{ formatAmount(compenzation.amount) }} €
                            </td>
                            <td class="pl-6 py-4 text-sm">
                                {{ formatPercentage(compenzation.implementation_agreement?.discount) }}
                            </td>
                            <td class="pl-6 py-4 text-sm">
                                {{ formatPercentage(compenzation.realization_agreement?.commission) }}
                            </td>
                            <td class="pl-6 py-4 text-sm">
                                <div class="flex flex-wrap gap-2">
                                    <a 
                                        v-if="compenzation.proposal && compenzation.proposal.file_path"
                                        :href="route('entities.compenzation.pdf.download', { 
                                            entityId: entity.id, 
                                            compenzationId: compenzation.id, 
                                            type: 'proposal' 
                                        })"
                                        class="inline-flex items-center px-3 py-1 bg-blue text-white text-xs rounded hover:bg-blue-600 transition"
                                        title="Prenos kompenzacije"
                                    >
                                        <DownloadIcon class="h-4 w-4 mr-1"/>
                                        Kompenzacija
                                    </a>
                                    <a 
                                        v-if="compenzation.implementation_agreement && compenzation.implementation_agreement.file_path"
                                        :href="route('entities.compenzation.pdf.download', { 
                                            entityId: entity.id, 
                                            compenzationId: compenzation.id, 
                                            type: 'implementation' 
                                        })"
                                        class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition"
                                        title="Prenos pogodbe o izvedbi"
                                    >
                                        <DownloadIcon class="h-4 w-4 mr-1"/>
                                        Pogodba o izvedbi
                                    </a>
                                    <a 
                                        v-if="compenzation.realization_agreement && compenzation.realization_agreement.file_path"
                                        :href="route('entities.compenzation.pdf.download', { 
                                            entityId: entity.id, 
                                            compenzationId: compenzation.id, 
                                            type: 'realization' 
                                        })"
                                        class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition"
                                        title="Prenos pogodbe o unovčenju"
                                    >
                                        <DownloadIcon class="h-4 w-4 mr-1"/>
                                        Pogodba o unovčenju
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            <div v-else class="bg-white p-8 text-center text-gray-500">
                Ni kompenzacij za to podjetje.
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/mixins/adminLayout'
import InputGroup from '@/Components/InputGroup.vue'
import { PencilAltIcon, DownloadIcon } from '@heroicons/vue/outline';
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'

export default {
    layout: AdminLayout,

    components: {
        Head,
        Link,
        InputGroup,
        PencilAltIcon,
        DownloadIcon,
        Label,
        Button
    },

    props: {
        entity: Object
    },

    data() {
        const data = {}

        data['formdata'] = {
            form: this.$inertia.form({
                ...this.entity,
                action: 'update'
            }),
            edit: false
        }

        data['sort'] = 'id'
        data['direction'] = 'asc'

        return {
            ...data,
        }
    },

    computed: {
        sortedCompenzations() {
            const list = [...(this.entity.compenzations || [])]
            list.sort((a, b) => {
                let cmp = 0
                if (this.sort === 'name') {
                    cmp = String(a.name || '').localeCompare(String(b.name || ''), 'sl', { sensitivity: 'base' })
                } else if (this.sort === 'date') {
                    const ta = a.date ? new Date(a.date).getTime() : 0
                    const tb = b.date ? new Date(b.date).getTime() : 0
                    cmp = ta === tb ? 0 : ta < tb ? -1 : 1
                } else {
                    cmp = (Number(a.id) || 0) - (Number(b.id) || 0)
                }
                const applyDir = this.sort === 'id' ? 1 : (this.direction === 'desc' ? -1 : 1)
                if (cmp !== 0) {
                    return cmp * applyDir
                }
                return (Number(a.id) || 0) - (Number(b.id) || 0)
            })
            return list
        }
    },

    methods: {
        toggleSort(column) {
            if (this.sort === column) {
                this.direction = this.direction === 'asc' ? 'desc' : 'asc'
            } else {
                this.sort = column
                this.direction = 'asc'
            }
        },
        sortIndicator(column) {
            if (this.sort !== column) {
                return '▲▼'
            }
            return this.direction === 'asc' ? '▲' : '▼'
        },
        toggleEditMode(section, state) {
            if (state !== undefined) {
                section.edit = state
            } else {
                section.edit = !section.edit
            }
        },
        resetSection(section) {
            section.form.reset()
            this.toggleEditMode(section)
        },
        onSubmitSection(section) {
            console.log(section.form);
            section.form.patch(this.route('entities.entity.patch', {
                id: this.entity.id
            }),{
                onSuccess: () => {
                    this.toggleEditMode(section, false)
                }
            })
        },
        formatDate(date) {
            if (!date) return 'N/A';
            const d = new Date(date);
            return d.toLocaleDateString('sl-SI', { 
                year: 'numeric', 
                month: '2-digit', 
                day: '2-digit' 
            });
        },
        formatAmount(amount) {
            if (!amount) return '0,00';
            return parseFloat(amount).toLocaleString('sl-SI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
        formatPercentage(value) {
            if (value == null || value === '' || isNaN(Number(value))) return '';
            return `${Number(value).toLocaleString('sl-SI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })} %`;
        }
    },

}
</script>

<style lang="postcss">


</style>