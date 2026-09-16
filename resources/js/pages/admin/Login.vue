<template>
    <section id="login" ref="loginSection">
        <v-container>
            <v-card flat class="pa-6 mx-auto mt-10 shadow-lg reveal-item" max-width="400" rounded="xl"
                :loading="loading" :disabled="loading">
                <template v-if="loading" #loader>
                    <v-progress-linear indeterminate color="primary" />
                </template>
                <v-card-title class="text-center px-0">Hi, Admin!</v-card-title>
                <v-card-subtitle class="text-center px-0">Log in to your account</v-card-subtitle>
                <Alert class="rounded-[10px]" />
                <v-form @submit.prevent="submit" class="mt-5">
                    <v-text-field v-model="fields.email" color="primary" variant="solo" flat density="comfortable"
                        placeholder="Email" class="vfield-outline mb-1" :error-messages="errors.email" />
                    <PasswordField v-model="fields.password" variant="solo" flat density="comfortable"
                        placeholder="Password" class="vfield-outline" :error-messages="errors.password" />
                    <v-checkbox v-model="fields.remember" color="primary" hide-details density="compact"
                        class="text-label-large">
                        <template #label>
                            <span class="text-label-large text-medium-emphasis">Remember me</span>
                        </template>
                    </v-checkbox>
                    <v-btn type="submit" color="primary" variant="flat" height="50" size="large" block text="Log In"
                        class="rounded-2xl my-3" :disabled="!ready || !meta.valid" />
                    <v-btn variant="plain" height="50" size="large" prepend-icon="i-mdi-arrow-left" block
                        text="Go back to home" :ripple="false" class="rounded-2xl"
                        @click="$router.replace({ name: 'home' })" />
                </v-form>
            </v-card>
        </v-container>
    </section>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";
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
const { fields, errors, loading, submit, meta, ready } = useValidatedForm(schema, async (values) => {
    await auth.login(values);
    router.replace({ name: 'manage-content' });
},
    { resetOnSuccess: false, useAlertForErrors: true }
);

useScrollReveal(loginSection, { selector: '.reveal-item', y: 20 });

onMounted(async () => {
    await nextTick();
    useScrollReveal(loginSection, { selector: '.reveal-item', y: 20 });

    const { reason } = route.query;
    if (reason === 'session_expired') warning('Your session has expired. Please log in again.');
    else if (reason === 'unauthenticated') error('You are unauthenticated. Please log in again.');
    if (reason) router.replace({ name: 'login' });
});
</script>