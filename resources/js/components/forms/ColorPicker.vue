<script setup>
import { ref } from "vue";

defineOptions({ inheritAttrs: false });

const props = defineProps({
    modelValue: { type: String, default: null },
    anchor: { type: [Object, String, Array], default: null },
    location: { type: String, default: "bottom" },
});
const emit = defineEmits(["update:modelValue", "change"]);

const menuOpen = ref(false);

function onUpdate(color) {
    emit("update:modelValue", color);
    emit("change", color);
};
</script>

<template>
    <v-menu v-model="menuOpen" :close-on-content-click="false" :target="anchor ?? undefined" :location="location"
        content-class="shadow-sm border rounded-[10px]" width="300" min-width="300" max-width="300" :offset="[8, 2]">
        <template #activator="{ props: activatorProps }">
            <slot name="activator" :props="activatorProps" :selected="modelValue">
                <v-icon v-bind="activatorProps" icon="i-mdi-palette" color="surface-variant opacity-65" />
            </slot>
        </template>

        <v-card rounded="lg" class="overflow-hidden">
            <v-color-picker :model-value="modelValue" mode="hex" :modes="['hex', 'rgb', 'hsl']" elevation="0"
                v-bind="$attrs" @update:model-value="onUpdate" />
        </v-card>
    </v-menu>
</template>