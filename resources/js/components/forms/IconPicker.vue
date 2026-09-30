<script setup>
import { ref, nextTick } from "vue";
import { useIconPicker } from "@/composables/useIconPicker";
import { OverlayScrollbarsComponent } from "overlayscrollbars-vue";

const props = defineProps({
    modelValue: { type: String, default: null },
    anchor: { type: [Object, String, Array], default: null },
    location: { type: String, default: "bottom" },
});
const emit = defineEmits(["update:modelValue", "selected"]);
const {
    icons,
    search,
    sets,
    loading,
    total,
    getIcons,
    onSearch: debouncedSearch,
    onSetsChange: changeSets,
    loadMore,
} = useIconPicker();
const menuOpen = ref(false);
const scrollBox = ref(null);
const activeSet = ref(sets.value[0]);

function onMenuToggle(open) {
    if (open && icons.value.length === 0) {
        getIcons(true);
    }
};
function onSearch(val) {
    debouncedSearch(val);
};
function onSetChange(newSet) {
    if (!newSet) return;
    activeSet.value = newSet;
    changeSets([activeSet.value]);
    nextTick(() => {
        if (scrollBox.value) scrollBox.value.scrollTop = 0;
    });
};
function onScroll(instance) {
    const el = instance.elements().viewport;
    const nearBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 80;
    if (nearBottom && !loading.value && icons.value.length < total.value) {
        loadMore();
    }
};
function pick(icon) {
    emit("update:modelValue", icon.name);
    emit("selected", icon);
    menuOpen.value = false;
};
</script>

<template>
    <v-menu v-model="menuOpen" :close-on-content-click="false" :target="anchor ?? undefined" :location="location"
        content-class="shadow-sm border rounded-[10px]" width="360" min-width="360" max-width="360" :offset="[8, 2]"
        @update:model-value="onMenuToggle">
        <template #activator="{ props: activatorProps }">
            <slot name="activator" :props="activatorProps" :selected="modelValue">
                <v-icon v-bind="activatorProps" icon="i-mdi-emoticon" color="surface-variant opacity-65" />
            </slot>
        </template>

        <v-card rounded="lg">
            <div class="px-3 pt-3">
                <v-tabs :model-value="activeSet" inset :inset-padding="4" :inset-radius="4" grow density="compact"
                    bg-color="surface-light" selected-class="shadow-sm text-high-emphasis font-weight-bold border"
                    slider-color="surface" slider-transition="fade" class="shadow-none mb-3"
                    @update:model-value="onSetChange">
                    <v-tab value="mdi" :ripple="false" class="text-medium-emphasis">MDI</v-tab>
                    <v-tab value="ri" :ripple="false" class="text-medium-emphasis">Remix</v-tab>
                </v-tabs>

                <v-text-field :model-value="search" label="Search icons..." variant="solo" flat single-line
                    density="compact" rounded="lg" clearable hide-details class="vfield-outline" autocomplete="off"
                    @update:model-value="onSearch">
                    <template #prepend-inner>
                        <v-icon size="16" icon="i-ri-search-line" />
                    </template>
                </v-text-field>
            </div>

            <OverlayScrollbarsComponent ref="scrollBox" element="div" class="h-[230px] mt-3 pa-3 overflow-y-auto"
                :options="{ scrollbars: { autoHide: 'move', theme: $vuetify.theme.current.dark ? 'os-theme-light' : 'os-theme-dark', } }"
                defer @os-scroll="onScroll">
                <div v-if="!loading && icons.length === 0" class="text-center text-medium-emphasis py-6 text-body-2">
                    No icons found.
                </div>
                <v-row density="compact">
                    <v-col v-for="icon in icons" :key="icon.name" cols="2" class="text-center">
                        <v-icon-btn variant="text" rounded="lg" v-tooltip.top="icon.name"
                            :color="icon.name === modelValue ? 'primary' : undefined" @click="pick(icon)">
                            <v-icon size="large">
                                <span v-html="icon.svg" />
                            </v-icon>
                        </v-icon-btn>
                    </v-col>
                </v-row>

                <div v-if="loading" class="d-flex justify-center py-3">
                    <v-progress-circular indeterminate size="22" color="primary" />
                </div>
            </OverlayScrollbarsComponent>
        </v-card>
    </v-menu>
</template>