<template>
    <section id="contact" ref="contactSection">
        <v-card flat tile>
            <v-container class="my-5 my-md-13">
                <v-row class="my-0 my-md-8">
                    <v-col cols="12" md="7">
                        <div class="d-flex flex-column ga-3 ga-md-5">
                            <div class="text-uppercase text-title-large font-weight-semibold text-primary reveal-item">
                                Contact
                            </div>
                            <div class="text-headline-small reveal-item">
                                {{ data.heading }}
                            </div>
                            <div class="text-medium-emphasis whitespace-pre-line reveal-item">
                                {{ data.description }}
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-center justify-md-start ga-3 my-4 my-md-10">
                            <div v-for="item in data.socials" class="reveal-item">
                                <v-icon-btn icon variant="tonal" size="x-large" color="primary"
                                    class="translate-y-hover" :href="item.linkUrl" target="_blank">
                                    <v-icon size="33">
                                        <span v-html="getIconSvg(item.icon)" />
                                    </v-icon>
                                </v-icon-btn>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="5">
                        <v-img :src="data.profileImage?.url" :position="$vuetify.display.mdAndUp ? 'right' : 'center'"
                            aspect-ratio="1" alt="Project Character Image"
                            class="clamped-img [--img-min-h:180px] [--img-max-h:285px] fade-bottom reveal-item" />
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </section>

    <section id="contact-body" ref="contactBodySection">
        <v-card flat tile color="surface-light">
            <v-container class="d-flex flex-column ga-5 my-5 my-md-13" :max-width="450">
                <div class="reveal-item">
                    <div class="text-center text-headline-small text-md-headline-medium font-weight-medium">Send Email
                    </div>
                </div>
                <v-form @submit.prevent="submit" :disabled="loading">
                    <v-row gap="13" class="mt-3 reveal-item">
                        <v-col cols="12">
                            <v-text-field v-model="name" color="primary" variant="solo" flat label="Name" rounded="lg"
                                density="comfortable" clearable :error-messages="errors.name"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="email" color="primary" variant="solo" flat label="Email" rounded="lg"
                                density="comfortable" clearable :error-messages="errors.email"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="message" color="primary" auto-grow variant="solo" flat label="Message"
                                rounded="lg" density="comfortable" clearable :error-messages="errors.message"
                                autocomplete="off">
                            </v-textarea>
                        </v-col>
                        <v-col cols="12">
                            <v-btn type="submit" color="primary" variant="flat" rounded="pill" size="x-large" block
                                prepend-icon="i-ri-send-plane-fill" :loading="loading"
                                :disabled="!meta.dirty || loading">
                                Send Email
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-container>
        </v-card>
    </section>
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, nextTick, watch } from "vue";
import * as yup from "yup";
import { storeToRefs } from "pinia";
import { useContactStore } from "@/stores/resources";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useUnsavedChanges } from "@/composables/useUnsavedChanges";
import { useScrollReveal } from "@/composables/useScrollReveal";
import { getIconSvg } from "@/src/utils/icon";
const contactStore = useContactStore();
const { data: data, loaded } = storeToRefs(contactStore);
const contactSection = ref(null);
const contactBodySection = ref(null);
const schema = yup.object({
    name: yup.string().label('Name').required().min(2),
    email: yup.string().label('Email').email().required(),
    message: yup.string().label('Message').required(),
});
const { defineField, errors, loading, submit, meta } = useValidatedForm(schema, async (values) => {
    const response = await axios.post('/api/contact', values);
    return { message: response.data.message };
});
useUnsavedChanges(meta);
const [name] = defineField('name');
const [email] = defineField('email');
const [message] = defineField('message');
const contactReveal = useScrollReveal(contactSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
const contactBodyReveal = useScrollReveal(contactBodySection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
watch(
    loaded,
    async (isLoaded) => {
        if (!isLoaded) return;
        await nextTick();
        contactReveal.refresh();
        contactBodyReveal.refresh();
    },
    { immediate: true }
);
</script>