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
                                        <span v-html="item.iconSvg" class="inline-flex items-center" />
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
            <v-container class="d-flex flex-column ga-5 my-5 my-md-13" :max-width="420">
                <div class="reveal-item">
                    <div class="text-center text-headline-small text-md-headline-medium font-weight-medium">Send Email
                    </div>
                </div>
                <v-form @submit.prevent="submit" :disabled="loading">
                    <v-row gap="5" class="mt-3 reveal-item">
                        <v-col cols="12">
                            <v-text-field v-model="fields.name" color="primary" variant="solo" flat label="Name"
                                class="[&_.v-field]:rounded-2xl [&_.v-field]:border" :error-messages="errors.name" />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="fields.email" color="primary" variant="solo" flat label="Email"
                                class="[&_.v-field]:rounded-2xl [&_.v-field]:border" :error-messages="errors.email" />
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="fields.message" color="primary" auto-grow variant="solo" flat
                                label="Message" class="[&_.v-field]:rounded-2xl [&_.v-field]:border"
                                :error-messages="errors.message" autocomplete="off" />
                        </v-col>
                        <v-col cols="12">
                            <v-btn type="submit" color="primary" variant="flat" size="large" height="56" block
                                text="Send Email" class="rounded-2xl" :loading="loading"
                                :disabled="!ready || !meta.valid || loading" />
                        </v-col>
                    </v-row>
                </v-form>
            </v-container>
        </v-card>
    </section>
</template>

<script setup>
import axios from "@/plugins/axios";
import { nextTick, ref, watch } from "vue";
import * as yup from "yup";
import { storeToRefs } from "pinia";
import { useContactStore } from "@/stores/resources";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useScrollReveal } from "@/composables/useScrollReveal";

const contactStore = useContactStore();
const { data, loaded } = storeToRefs(contactStore);
const contactSection = ref(null);
const contactBodySection = ref(null);

const schema = yup.object({
    name: yup.string().label('Name').required(),
    email: yup.string().label('Email').email().required(),
    message: yup.string().label('Message').required(),
});

const { fields, errors, loading, submit, meta, ready } = useValidatedForm(schema, async (values) => {
    const response = await axios.post('/api/contact', values);
    return { message: response.data.message };
});

const contactReveal = useScrollReveal(contactSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
const contactBodyReveal = useScrollReveal(contactBodySection, { selector: '.reveal-item', stagger: 0.15, y: 40 });

watch(loaded, async (isLoaded) => {
    if (!isLoaded) return;
    await nextTick();
    contactReveal.refresh();
    contactBodyReveal.refresh();
}, { immediate: true });
</script>