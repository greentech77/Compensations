<template>
    <Head title="Nova kompenzacija"/>
        <MainRegisterBlock>
    
        <div class="form-wrap">

            <h1 class="form__heading-1">Vnos nove kompenzacije</h1>

            <SteppedForm :steps="steps" v-model:currentStepIndex="currentStepIndex" @changeStep="onChangeStep">

                <template v-for="(step, index) in steps" :key="index" v-slot:[`step-${index}`]>
                    <component :is="step.component" :key="`step-${index}-${componentKey}`" v-bind="step.props" @cancel="onCancelStep" @finish="onFinishStep"/>
                </template>

            </SteppedForm>

        </div>

    </MainRegisterBlock>
    
</template>

<script>
import MainRegisterBlock from '@/Layouts/MainRegisterBlock.vue'
import AdminLayout from '@/Layouts/AdminLayout'
import { Head } from '@inertiajs/inertia-vue3';
import SteppedForm from '@/Forms/SteppedForm.vue'
import CompenzationData from '@/Forms/CompenzationData.vue'
import CompenzationFinish from '@/Forms/CompenzationFinish.vue'
import { markRaw } from 'vue'
import { stepperControllerMixin } from '@/Forms/steppedMixins'

export default {
    layout: AdminLayout,

    components: {
        Head,
        SteppedForm,
        MainRegisterBlock
    },

    props: {
        errors: Object,
        entities: Object,
    },

    mixins: [stepperControllerMixin],

    data() {
        const compenzationDataForm = this.$inertia.form({
            compenzationDate: undefined,
            compenzationAmount: undefined,
            compenzationEntities: undefined,
            compenzationDiscount: undefined,
            compenzationCommission: undefined,
            discountWithVat: false,
            /*date_finished: undefined,
            date_payed: undefined,
            storno: undefined,
            finished: undefined,
            with_ddv: undefined*/
        })

        const finalForms = {
            compenzationData: compenzationDataForm
        }

        return {
            componentKey: Date.now(), // Key za rekreacijo komponente ob vsakem odpiranju
            steps: [
                {
                    enabled: true,
                    title: 'Podatki o kompenzaciji',
                    component: markRaw(CompenzationData),
                    props: {
                        form: compenzationDataForm,
                        entities: this.entities
                    },
                    get valid() {
                        return this.props.form.wasSuccessful && !this.props.form.isDirty
                    }
                },{
                    enabled: true,
                    title: 'Povzetek in zaključek',
                    component: markRaw(CompenzationFinish),
                    props: {
                        forms: finalForms
                    }
                }
            ]
        }
     },

    beforeMount() {
        // Počisti localStorage PRED vsem - najzgodneje možno
        localStorage.removeItem('components');
    },
    created() {
        //if (process.env.MIX_APP_ENV == 'local') {
            this.currentStepIndex = 0
        //}
        // Počisti localStorage ob odpiranju forme, da se vedno začne z eno komponento
        localStorage.removeItem('components');
    },
    mounted() {
        // Počisti localStorage še enkrat za varnost
        localStorage.removeItem('components');
        // Rekreiraj komponento ob vsakem odpiranju - posodobi key
        this.componentKey = Date.now();
    },

}


</script>

<style lang="postcss">

    @import '../../css/form.css';

</style>