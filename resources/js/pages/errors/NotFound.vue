<script setup>
import { ref } from "vue";
import { useScrollReveal } from '@/composables/useScrollReveal';
const notFoundSection = ref(null);
useScrollReveal(notFoundSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
</script>

<template>
    <section id="not-found" ref="notFoundSection">
        <v-container class="reveal-item">
            <v-empty-state headline="Whoops, 404" title="Page not found"
                text="The page you were looking for does not exist" image="/images/not-found.png" class="mt-15 animate">
                <template #actions>
                    <v-btn color="primary" variant="flat" height="50" size="large" block text="Go back to home"
                        class="rounded-2xl" @click="$router.replace('/')">
                    </v-btn>
                </template>
            </v-empty-state>
        </v-container>
    </section>
</template>