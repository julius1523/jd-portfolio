<template>
    <section id="not-found">
        <v-container>
            <v-empty-state headline="Whoops, 404" title="Page not found"
                text="The page you were looking for does not exist" image="/images/not-found.png" class="mt-15 animate">
                <template #actions>
                    <v-btn color="primary" variant="flat" rounded="pill" size="x-large" prepend-icon="mdi-arrow-left"
                        block text="Go back to home" @click="$router.push('/')">
                    </v-btn>
                </template>
            </v-empty-state>
        </v-container>
    </section>
</template>