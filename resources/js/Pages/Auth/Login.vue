<template>

    <div class="w-screen h-screen sm:bg-stone flex items-center justify-center">

        <div class="w-screen sm:w-112 sm:rounded-md bg-white px-8 py-12 filter sm:drop-shadow-lg">

            <h1 class="text-xl font-medium text-center mb-8">Prijava</h1>

            <form @submit.prevent="onSubmit">
                <div class="mb-6">
                    <InputGroup for="email" type="email" v-model="loginForm.email" :label="$t('common.email')" required autofocus autocomplete="off" :error="loginForm.errors.email" @blur="loginForm.clearErrors()"></InputGroup>
                </div>

                <div class="mb-12">
                    <InputGroup for="password" type="password" v-model="loginForm.password" :label="$t('common.password')" required autocomplete="off" :error="loginForm.errors.password" @blur="loginForm.clearErrors()"></InputGroup>
                </div>

                <div>
                    <Button class="button button--stone-30 w-full" :loading="loginForm.processing" type="submit">Prijava</Button>
                </div>
            </form>

        </div>
    </div>

    <!--BreezeGuestLayout>
        <Head title="Log in" />

        <BreezeValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">

            <div>
                <BreezeLabel for="email" value="Email" />
                <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <BreezeLabel for="password" value="Password" />
                <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <BreezeCheckbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Forgot your password?
                </Link>

                <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </BreezeButton>
            </div>
        </form>
    </BreezeGuestLayout-->
</template>

<script>
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import InputGroup from '@/Components/InputGroup.vue';
import { Head, Link } from '@inertiajs/vue3';


export default {

    components: {
        Button,
        Checkbox,
        Input,
        Label,
        Head,
        Link,
        InputGroup
    },

    data() {
        return {
            loginForm: this.$inertia.form({
                email: undefined,
                password: undefined
            })
        }
    },

    methods: {
        onSubmit() {
            this.loginForm.post(this.route('login'), {
                onFinish: () => this.loginForm.reset('password'),
            })
        }
    }
}

</script>

<style lang="postcss">

</style>

