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
                            <div class="font-weight-light whitespace-pre-line reveal-item">
                                {{ data.description }}
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="5">
                        <v-img :src="data.profileImage?.url" :position="$vuetify.display.mdAndUp ? 'right' : 'center'"
                            :aspect-ratio="1" alt="Project Character Image"
                            class="clamped-img [--img-min-h:180px] [--img-max-h:220px] lg:[--img-max-h:285px] fade-bottom reveal-item" />
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <section id="projects-body" ref="projectsBodySection">
            <v-container>
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3 reveal-item">
                    <div class="flex flex-wrap items-center gap-4">
                        <v-btn variant="flat" text="Filters" color="surface-light" rounded="pill"
                            prepend-icon="i-mdi-filter-variant" class="border" @click="openFilters" />
                    </div>

                    <div class="text-sm text-medium-emphasis">
                        {{ filteredProjects.length }} {{ filteredProjects.length === 1 ? "project" : "projects" }}
                    </div>
                </div>

                <v-slide-x-transition group tag="div" class="d-flex flex-wrap ga-2 h-[35px]">
                    <v-chip v-if="activeCategory !== ALL" variant="flat" color="surface-light" closable class="border"
                        @click:close="activeCategory = ALL">
                        {{ activeCategory }}
                    </v-chip>
                    <v-chip v-if="search?.trim()" variant="flat" color="surface-light" closable class="border"
                        @click:close="search = ''">
                        "{{ search }}"
                    </v-chip>
                </v-slide-x-transition>

                <FormDialog v-model="filterDialog" add-title="Filter projects" save-text="Apply Filter"
                    :disable-save="false" :max-width="480" @save="applyFilters">
                    <div class="flex flex-col gap-4">
                        <v-text-field v-model="draftSearch" label="Search projects" prepend-inner-icon="i-mdi-magnify"
                            variant="solo" flat single-line density="compact" hide-details clearable autocomplete="off"
                            class="vfield-outline" @keyup.enter="applyFilters" />

                        <Select v-model="draftCategory" :items="categories" label="Category" variant="solo" flat
                            single-line density="compact" :multiple="false" :chip="false" :persistent-hint="false"
                            hide-details rounded="pill" />
                    </div>
                </FormDialog>

                <v-data-iterator :items="filteredProjects" :items-per-page="itemsPerPage" v-model:page="page"
                    class="mt-3">
                    <template #default="{ items }">
                        <v-row :gap="35">
                            <v-col v-for="{ raw: project } in items" :key="project.id" cols="12" sm="6" lg="4"
                                class="reveal-item">
                                <v-card flat border class="rounded-[15px] flex flex-col cursor-pointer"
                                    @click="openProject(project)">
                                    <v-img :src="project.image?.url" :alt="`${project.name} preview`"
                                        class="w-full aspect-[4/3]" cover loading="lazy">
                                        <template #placeholder>
                                            <v-skeleton-loader type="image" class="h-full" />
                                        </template>
                                    </v-img>

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
                        <EmptyState :size="130" title="No projects found"
                            text="Try a different keyword or pick another category."
                            :action-text="hasFilters ? 'Clear filters' : undefined" @action="resetFilters">
                            <template #actions>
                                <v-btn color="primary" text="Clear filters" rounded="pill" size="large"
                                    @click="resetFilters" />
                            </template>
                        </EmptyState>
                    </template>
                </v-data-iterator>

                <div v-if="loaded && pageCount > 1" class="mt-10 reveal-item">
                    <v-pagination v-model="page" :length="pageCount" :total-visible="5" rounded="circle"
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
import FormDialog from "@/components/forms/FormDialog";
import Select from "@/components/forms/Select";
import EmptyState from "@/components/ui/EmptyState";

const ALL = "All Projects";
const itemsPerPage = 6;
const projectsStore = useProjectsStore();
const { data, loaded } = storeToRefs(projectsStore);
const projectsSection = ref(null);
const projectsBodySection = ref(null);
const page = ref(1);
const search = ref("");
const activeCategory = ref(ALL);
const filterDialog = ref(false);
const draftSearch = ref("");
const draftCategory = ref(ALL);
const projects = computed(() => data.value?.projects ?? []);
const hasFilters = computed(() => !!search.value?.trim() || activeCategory.value !== ALL);
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
const openProject = (item) => {
    const url = item.linkType === "upload" ? item.linkFile?.url : item.linkUrl;
    if (!url) return;
    window.open(url, "_blank", "noopener,noreferrer");
};
const openFilters = () => {
    draftSearch.value = search.value;
    draftCategory.value = activeCategory.value;
    filterDialog.value = true;
};
const applyFilters = () => {
    search.value = draftSearch.value ?? "";
    activeCategory.value = draftCategory.value;
    filterDialog.value = false;
};
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
    const el = projectsBodySection.value?.$el ?? projectsBodySection.value;
    if (!el) return;

    const y = el.getBoundingClientRect().top + window.scrollY - 60;
    window.scrollTo({ top: y, behavior: "smooth" });
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