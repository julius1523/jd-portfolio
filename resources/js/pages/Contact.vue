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
                                Get in touch via social media or sending a message.
                            </div>
                            <div class="text-medium-emphasis reveal-item">
                                Have an inquiry or want to collaborate? Reach out to me by messaging me in one
                                of my socials or sending me a message through email.
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-center justify-md-start ga-3 my-10">
                            <div class="reveal-item">
                                <v-btn icon="mdi-facebook" class="translate-content" variant="tonal" color="primary"
                                    href="https://www.facebook.com/JuliusDolana/" target="_blank">
                                </v-btn>
                            </div>
                            <div class="reveal-item">
                                <v-btn icon="mdi-linkedin" class="translate-content" variant="tonal" color="primary"
                                    href="https://www.linkedin.com/in/julius-dolana-783bb1371/" target="_blank">
                                </v-btn>
                            </div>
                            <div class="reveal-item">
                                <v-btn icon="mdi-camera" class="translate-content" variant="tonal" color="primary"
                                    href="https://www.instagram.com/juliussss1998/" target="_blank">
                                </v-btn>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="5">
                        <div class="reveal-item">
                            <v-img src="/images/contact.png" cover height="300" width="300" class="fade-bottom"
                                :class="$vuetify.display.mdAndUp ? 'ml-auto' : 'mx-auto'" alt="Contact Person" />
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </section>

    <section id="contact-body" ref="contactBodySection">
        <v-card flat tile color="surface-light">
            <v-container class="d-flex flex-column ga-5 my-5 my-md-13" :max-width="450">
                <div class="reveal-item">
                    <div class="text-center text-headline-small font-weight-medium">Send Email
                    </div>
                    <v-divider :thickness="4" color="primary" class="border-opacity-75 mx-auto my-3" length="50" />
                </div>
                <v-form @submit.prevent="submit" :disabled="loading">
                    <v-row gap="13" class="mt-3 reveal-item">
                        <v-col cols="12">
                            <v-text-field v-model="name" color="primary" variant="solo" flat label="Name" rounded="lg"
                                density="comfortable" clearable :error-messages="errors.name"
                                autocomplete="off"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="email" color="primary" variant="solo" flat label="Email" rounded="lg"
                                density="comfortable" clearable :error-messages="errors.email"
                                autocomplete="off"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="message" color="primary" auto-grow variant="solo" flat label="Message"
                                rounded="lg" density="comfortable" clearable :error-messages="errors.message"
                                autocomplete="off">
                            </v-textarea>
                        </v-col>
                        <v-col cols="12">
                            <v-btn type="submit" color="primary" variant="flat" rounded="pill" size="x-large" block
                                prepend-icon="mdi-send" :loading="loading" :disabled="!meta.dirty || loading">
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
import { ref } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useUnsavedChanges } from "@/composables/useUnsavedChanges";
import { useScrollReveal } from "@/composables/useScrollReveal";
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
useScrollReveal(contactSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
useScrollReveal(contactBodySection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
</script>