<script setup>
const currentYear = new Date().getFullYear();
</script>

<template>
    <v-footer app absolute>
        <v-container class="d-flex justify-center justify-md-space-between text-title-small">
            <div class="text-medium-emphasis">
                © {{ currentYear }} Julius Dolana
            </div>

            <div v-if="$vuetify.display.mdAndUp" class="d-flex ga-6">
                <router-link to="/home" class="text-decoration-none text-medium-emphasis">
                    Home
                </router-link>
                <router-link to="/about" class="text-decoration-none text-medium-emphasis">
                    About
                </router-link>
                <router-link to="/projects" class="text-decoration-none text-medium-emphasis">
                    Projects
                </router-link>
                <router-link to="/contact" class="text-decoration-none text-medium-emphasis">
                    Contact
                </router-link>
            </div>
        </v-container>
    </v-footer>
</template>