<template>
    <v-menu v-model="menuOpen" :close-on-content-click="false" :offset="[8, 0]" location="bottom end" width="360"
        @update:model-value="onMenuToggle">
        <template #activator="{ props: activatorProps }">
            <slot name="activator" :props="activatorProps" :selected="modelValue" :selected-svg="selectedSvg">
                <v-icon-btn v-bind="activatorProps" variant="tonal" rounded="circle" icon="i-mdi-emoticon"
                    color="warning" />
            </slot>
        </template>

        <v-card rounded="lg">
            <div class="pa-3">
                <v-tabs :model-value="activeSet" color="primary" density="compact" grow inset class="mb-3"
                    @update:model-value="onSetChange">
                    <v-tab value="mdi">MDI</v-tab>
                    <v-tab value="ri">Remix</v-tab>
                </v-tabs>

                <v-text-field :model-value="search" placeholder="Search icons..." variant="solo" flat density="compact"
                    rounded="lg" clearable hide-details autocomplete="off" @update:model-value="onSearch" />
            </div>

            <v-card-text ref="scrollBox" class="icon-grid overflow-y-auto pa-3" @scroll="onScroll">
                <div v-if="!loading && icons.length === 0" class="text-center text-medium-emphasis py-6 text-body-2">
                    No icons found.
                </div>

                <v-row density="comfortable">
                    <v-col v-for="icon in icons" :key="icon.name" cols="2" class="text-center">
                        <v-icon-btn variant="text" rounded="lg" class="border" v-tooltip.top="icon.name"
                            :color="icon.name === modelValue?.name ? 'primary' : undefined" @click="pick(icon)">
                            <span v-html="icon.svg" class="icon-sm" />
                        </v-icon-btn>
                    </v-col>
                </v-row>

                <div v-if="loading" class="d-flex justify-center py-3">
                    <v-progress-circular indeterminate size="22" color="primary" />
                </div>
            </v-card-text>
        </v-card>
    </v-menu>
</template>

<script setup>
import { ref, watch, nextTick } from "vue";
import { useIconPicker } from "@/composables/useIconPicker";
const props = defineProps({
    modelValue: { type: Object, default: null },
});
const emit = defineEmits(["update:modelValue"]);
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
const selectedSvg = ref("");
const activeSet = ref(sets.value[0]);
function syncSelectedSvg(value) {
    selectedSvg.value = value?.svg ?? "";
}
watch(() => props.modelValue, syncSelectedSvg, { immediate: true });
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
function onScroll(e) {
    const el = e.target;
    const nearBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 80;
    if (nearBottom && !loading.value && icons.value.length < total.value) {
        loadMore();
    }
};
function pick(icon) {
    emit("update:modelValue", { name: icon.name, svg: icon.svg });
    selectedSvg.value = icon.svg;
    menuOpen.value = false;
};
</script>

<style lang="css" scoped>
.icon-grid {
    height: 230px;
}
</style>