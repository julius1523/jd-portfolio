<template>
    <section id="projects" ref="projectsSection">
        <v-container class="my-5 my-md-13">
            <v-row class="my-0 my-md-8">
                <v-col cols="12" md="7">
                    <div class="d-flex flex-column ga-3 ga-md-5">
                        <div class="text-uppercase text-title-large font-weight-semibold text-primary reveal-item">
                            Projects
                        </div>
                        <div class="text-headline-small reveal-item">
                            {{ data.heading }}
                        </div>
                        <div class="text-medium-emphasis whitespace-pre-line reveal-item">
                            {{ data.description }}
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
    </section>
    <section id="projects-body" ref="projectsBodySection">
        <v-container class="my-5 my-md-13 px-7">
            <div v-for="group in groupedProjects" :key="group.category" class="d-flex flex-column ga-2 mb-12 h-[560px]">
                <div class="reveal-item">
                    <div class="text-headline-small text-md-headline-medium font-weight-medium">
                        {{ group.category }}
                    </div>
                </div>
                <SnapCarousel :items="group.items">
                    <template #default="{ item, index }">
                        <v-card width="330" height="440" rounded="xl" flat class="border flex flex-col scale-up-hover"
                            @click="openProject(item)">
                            <div class="h-1/2 flex-shrink-0">
                                <v-img :src="item.image?.url" width="fit-content" position="top"
                                    :alt="`${group.category} Preview ${index}`" />
                            </div>

                            <div class="p-5 flex flex-col flex-1 min-h-0">
                                <div class="text-lg font-medium leading-snug flex-shrink-0 text-truncate">
                                    {{ item.name }}
                                </div>

                                <div class="flex-1 min-h-0 overflow-hidden mt-2">
                                    <div class="text-sm text-medium-emphasis line-clamp-5">
                                        {{ item.description }}
                                    </div>
                                </div>

                                <div class="flex flex-row gap-1 mt-3 flex-shrink-0">
                                    <v-chip v-for="material in item.materials.slice(0, 4)" :key="material"
                                        color="primary" size="small" variant="tonal">
                                        {{ material }}
                                    </v-chip>
                                    <v-chip v-if="item.materials.length > 4" size="small" variant="text"
                                        class="text-medium-emphasis">
                                        +{{ item.materials.length - 4 }} more
                                    </v-chip>
                                </div>
                            </div>
                        </v-card>
                    </template>
                </SnapCarousel>
            </div>
        </v-container>
    </section>
</template>

<script setup>
import { ref, computed, nextTick, watch } from "vue";
import { storeToRefs } from "pinia";
import { useProjectsStore } from "@/stores/resources";
import SnapCarousel from "@/components/ui/SnapCarousel";
import { useScrollReveal } from "@/composables/useScrollReveal";

const projectsStore = useProjectsStore();
const { data: data, loaded } = storeToRefs(projectsStore);
const projectsSection = ref(null);
const projectsBodySection = ref(null);
const groupedProjects = computed(() => {
    const projects = data.value?.projects ?? [];
    const map = new Map();
    for (const item of projects) {
        if (!map.has(item.category)) map.set(item.category, []);
        map.get(item.category).push(item);
    }
    return Array.from(map, ([category, items]) => ({ category, items }));
});
const openProject = (item) => {
    const url = item.linkType === "upload" ? item.linkFile?.url : item.linkUrl;
    if (!url) return;
    window.open(url, "_blank", "noopener,noreferrer");
};
const projectsReveal = useScrollReveal(projectsSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
const projectsBodyReveal = useScrollReveal(projectsBodySection, { selector: '.reveal-item', stagger: 0.15, y: 40 });

watch(
    loaded,
    async (isLoaded) => {
        if (!isLoaded) return;
        await nextTick();
        projectsReveal.refresh();
        projectsBodyReveal.refresh();
    },
    { immediate: true }
);
</script>