<template>
    <section id="login" ref="loginSection">
        <v-container>
            <v-card class="pa-6 mx-auto mt-10 reveal-item" max-width="400" rounded="xl" :loading="loading"
                :disabled="loading">
                <template v-if="loading" #loader>
                    <v-progress-linear indeterminate color="primary" />
                </template>
                <v-card-title class="text-center px-0">Hi, Admin!</v-card-title>
                <v-card-subtitle class="text-center px-0">Log in to your account</v-card-subtitle>
                <Alert />
                <v-form @submit.prevent="submit" class="mt-5">
                    <v-text-field v-model="email" color="primary" variant="solo" flat label="Email" rounded="lg"
                        density="comfortable" class="mb-1" clearable :error-messages="errors.email" />
                    <PasswordField v-model="password" label="Password" :error-messages="errors.password" />
                    <v-checkbox v-model="remember" color="primary" hide-details density="compact"
                        class="text-label-large">
                        <template #label>
                            <span class="text-label-large">Remember me</span>
                        </template>
                    </v-checkbox>
                    <v-btn type="submit" color="primary" variant="flat" rounded="pill" size="x-large" block
                        class="my-3">
                        Log In
                    </v-btn>
                    <v-btn color="surface-variant" variant="plain" rounded="pill" size="x-large"
                        :prepend-icon="mdiArrowLeft" block text="Go back to home"
                        @click="$router.replace({ name: 'home' })">
                    </v-btn>
                </v-form>
            </v-card>
        </v-container>
    </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { mdiArrowLeft } from "@mdi/js";
import * as yup from "yup";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useValidatedForm } from "@/composables/useValidatedForm";
import PasswordField from "@/components/forms/PasswordField";
import Alert from "@/components/ui/Alert";
import { useAlert } from "@/composables/useAlert";
import { useScrollReveal } from '@/composables/useScrollReveal';
const loginSection = ref(null);
const route = useRoute();
const router = useRouter();
const { warning, error } = useAlert();
const auth = useAuthStore();
const schema = yup.object({
    email: yup.string().label('Email').email().required(),
    password: yup.string().label('Password').required(),
    remember: yup.boolean()
});
const { defineField, errors, loading, submit } = useValidatedForm(schema, async (values) => {
    await auth.login(values);
    router.replace({ name: 'manage-content' });
},
    { resetOnSuccess: false, useAlertForErrors: true }
);
const [email] = defineField('email');
const [password] = defineField('password');
const [remember] = defineField('remember');
onMounted(() => {
    if (route.query.reason === 'session_expired') {
        warning('Your session has expired. Please log in again.');
    } else if (route.query.reason === 'unauthenticated') {
        error('You are unauthenticated. Please log in again.');
    }
    router.replace({ name: 'login' });
});
useScrollReveal(loginSection, { selector: '.reveal-item', y: 20 });
</script>