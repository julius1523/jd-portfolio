<template>
    <div class="d-flex flex-column gap-15 gap-md-30 mt-5 mt-md-15">
        <section id="projects" ref="projectsSection">
            <v-container>
                <v-row class="my-0 md:my-8">
                    <v-col cols="12" md="7">
                        <div class="flex flex-col gap-5">
                            <div class="uppercase text-title-large font-semibold text-primary reveal-item">
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
                            :aspect-ratio="1" alt="Project Character Image"
                            class="clamped-img [--img-min-h:180px] [--img-max-h:285px] fade-bottom reveal-item" />
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <section id="projects-body" ref="projectsBodySection">
            <v-container>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 reveal-item">
                    <v-chip-group v-model="activeCategory" mandatory active-color="primary">
                        <v-chip v-for="category in categories" :key="category" :value="category" color="primary"
                            variant="tonal" class="rounded-[10px]">
                            {{ category === ALL ? "All Projects" : category }}
                        </v-chip>
                    </v-chip-group>

                    <v-text-field v-model="search" label="Search projects" prepend-inner-icon="i-mdi-magnify"
                        variant="solo" flat single-line density="compact" hide-details clearable autocomplete="off"
                        class="vfield-outline w-full md:w-[360px] grow-0" />
                </div>

                <v-row v-if="!loaded">
                    <v-col v-for="n in 3" :key="n" cols="12" sm="6" lg="4">
                        <v-skeleton-loader type="image, article" class="rounded-3xl border" />
                    </v-col>
                </v-row>

                <v-data-iterator v-else :items="filteredProjects" :items-per-page="itemsPerPage" v-model:page="page">
                    <template #default="{ items }">
                        <v-row>
                            <v-col v-for="{ raw: project } in items" :key="project.id" cols="12" sm="6" lg="4"
                                class="reveal-item">
                                <v-card flat border class="shadow-sm rounded-[18px] flex flex-col"
                                    @click="openProject(project)">
                                    <v-img :src="project.image?.url" :alt="`${project.name} preview`"
                                        :aspect-ratio="16 / 9" cover position="top" />

                                    <div class="flex flex-col gap-3 pa-5">
                                        <div class="text-title-large font-medium truncate">
                                            {{ project.name }}
                                        </div>

                                        <div class="text-title-small font-weight-regular">
                                            {{ project.category }}
                                        </div>

                                        <div class="text-sm leading-5 text-medium-emphasis line-clamp-3 h-[60px]">
                                            {{ project.description }}
                                        </div>

                                        <div class="d-flex ga-1">
                                            <v-chip v-for="material in project.materials" :key="material"
                                                color="primary" size="small" variant="tonal">
                                                {{ material }}
                                            </v-chip>
                                        </div>
                                    </div>
                                </v-card>
                            </v-col>
                        </v-row>
                    </template>

                    <template #no-data>
                        <div class="text-center text-medium-emphasis py-16">
                            <v-icon icon="i-mdi-folder-search-outline" size="48" class="mb-3" />
                            <div class="text-title-large">No projects found</div>
                            <div class="mt-1">Try a different keyword or pick another category.</div>
                            <v-btn v-if="search || activeCategory !== ALL" variant="tonal" color="primary" class="mt-5"
                                @click="resetFilters">
                                Clear filters
                            </v-btn>
                        </div>
                    </template>
                </v-data-iterator>

                <div v-if="loaded && pageCount > 1" class="mt-10">
                    <v-pagination v-model="page" :length="pageCount" :total-visible="5" rounded="circle" variant="tonal"
                        active-color="primary" density="comfortable" />
                </div>
            </v-container>
        </section>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, watch } from "vue";
import { storeToRefs } from "pinia";
import { useProjectsStore } from "@/stores/resources";
import { useScrollReveal } from "@/composables/useScrollReveal";

const ALL = "__all__";
const itemsPerPage = 6;
const projectsStore = useProjectsStore();
const { data, loaded } = storeToRefs(projectsStore);
const openProject = (item) => {
    console.log("project: ", item)
    const url = item.linkType === "upload" ? item.linkFile?.url : item.linkUrl;
    if (!url) return;
    window.open(url, "_blank", "noopener,noreferrer");
};
const projectsSection = ref(null);
const projectsBodySection = ref(null);
const page = ref(1);
const search = ref("");
const activeCategory = ref(ALL);
const projects = computed(() => data.value?.projects ?? []);
const categories = computed(() => [ALL, ...new Set(projects.value.map((p) => p.category).filter(Boolean))]);
const filteredProjects = computed(() => {
    const query = (search.value ?? "").trim().toLowerCase();
    return projects.value.filter((project) => {
        if (activeCategory.value !== ALL && project.category !== activeCategory.value) return false;
        if (!query) return true;

        const haystack = [project.name, project.description, ...(project.materials ?? [])]
            .filter(Boolean)
            .join(" ")
            .toLowerCase();

        return haystack.includes(query);
    });
});
const pageCount = computed(() => Math.ceil(filteredProjects.value.length / itemsPerPage));
const resetFilters = () => {
    search.value = "";
    activeCategory.value = ALL;
};
const projectsReveal = useScrollReveal(projectsSection, { selector: ".reveal-item", stagger: 0.15, y: 40 });
const projectsBodyReveal = useScrollReveal(projectsBodySection, { selector: ".reveal-item", stagger: 0.15, y: 40 });

watch([activeCategory, search], () => {
    page.value = 1;
});

watch(page, () => {
    projectsBodySection.value?.scrollIntoView({ behavior: "smooth", block: "start" });
});

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