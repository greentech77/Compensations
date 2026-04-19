<template>
    <Head title="Podjetja"/>
    <div class="w-full bg-stone-15 p-8 rounded-md">

        <div class="flex justify-end space-x-4 mb-6">

        <Button class="button button--stone" @click="addEntity()">Dodaj podjetje</Button>

        </div>

        <table class="bg-white w-full divide-y divide-stone">
            <thead class="text-white uppercase tracking-wider font-medium text-xs text-left">
                <tr>
                    <th scope="col" class="pl-6 py-3 rounded-tl-md bg-blue">
                        Ime podjetja
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Pošta
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Davčna številka
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Matična številka
                    </th>
                    <th scope="col" class="pl-6 py-3 bg-blue">
                        Email
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone">
                <tr v-for="entity in entities.data" :key="entity.vat_num" class="h-20 cursor-pointer hover:bg-gray-200" @click="viewEntity(entity)">
                    <td class="pl-6 py-4 whitespace-nowrap">
                        <strong>{{entity.company_name}}</strong>
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{entity.post_town}}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{entity.vat_num}}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{entity.registration_num}}
                    </td>
                    <td class="pl-6 py-4 whitespace-nowrap">
                        {{entity.email}}
                    </td>
                </tr>
            </tbody>
        </table>
        <pagination class="mt-6" :links="entities.links" />
    </div>

</template>


<script>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/mixins/adminLayout'
import Pagination from '@/Components/Pagination'
import Button from '@/Components/Button.vue'

export default {

    layout: AdminLayout,

    components: {
        Head,
        Link,
        Pagination,
        Button
    },

    props: {
        entities: Object,
    },

    methods: {
         viewEntity(entity) {
            this.$inertia.visit(this.route('entities.entity', {
                id: entity.id
            }))
        },
        addEntity() {
            this.$inertia.get(this.route('entities.entity.register'));
        }
    }
}
</script>



