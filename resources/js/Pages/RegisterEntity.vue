<template>
    <Head title="Novo podjetje"/>
    <MainRegisterBlock>
    
        <div class="form-wrap">

            <h1 class="form__heading-1">Vnos novega podjetja</h1>
            <!--p class="form__body-text mb-6">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. </p-->
            
            <SteppedForm :steps="steps" v-model:currentStepIndex="currentStepIndex" @changeStep="onChangeStep">

                <template v-for="(step, index) in steps" :key="index" v-slot:[`step-${index}`]>
                    <component :is="step.component" v-bind="step.props" @cancel="onCancelStep" @finish="onFinishStep"/>
                </template>

            </SteppedForm>

        </div>

    </MainRegisterBlock>
    
</template>

<script>
import MainRegisterBlock from '@/Layouts/MainRegisterBlock.vue'
import AdminLayout from '@/Layouts/AdminLayout'
import { Head } from '@inertiajs/vue3';
import SteppedForm from '@/Forms/SteppedForm.vue'
import RegisterData from '@/Forms/RegisterData.vue'
import RegisterFinish from '@/Forms/RegisterFinish.vue'
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
    },

    mixins: [stepperControllerMixin],

    data() {
        const entityDataForm = this.$inertia.form({
            companyName: undefined,
            vatNum: undefined,
            registrationNum: undefined,
            name: undefined,
            lastname: undefined,
            email: undefined,
            phone: undefined,
            address: undefined,
            postNum: undefined,
            postTown: undefined,
            bankAccount: undefined,
            bankBIC: undefined,
            bankName: undefined
        })

        const finalForms = {
            entityData: entityDataForm
        }

        return {
            // Začetek na koraku 2: „Povzetek in zaključek“ (indeks je 0‑based).
            currentStepIndex: 0,
            steps: [
                {
                    enabled: true,
                    title: 'Podatki o podjetju',
                    component: markRaw(RegisterData),
                    props: {
                        form: entityDataForm
                    },
                    get valid() {
                        return this.props.form.wasSuccessful && !this.props.form.isDirty
                    }
                },{
                    enabled: true,
                    title: 'Povzetek in zaključek',
                    component: markRaw(RegisterFinish),
                    props: {
                        forms: finalForms
                    }
                }
            ]
        }
     },
     created() {
        if (process.env.MIX_APP_ENV == 'local') {
            this.currentStepIndex = 0
        }
    },

}


</script>

<style lang="postcss">

    @import '../../css/form.css';

</style>