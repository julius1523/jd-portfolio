<script setup>
import { ref } from "vue";

defineOptions({ inheritAttrs: false });

const props = defineProps({
    modelValue: { type: String, default: null },
});
const emit = defineEmits(["update:modelValue", "change"]);

const menuOpen = ref(false);

function onUpdate(color) {
    emit("update:modelValue", color);
    emit("change", color);
};
</script>

<template>
    <v-menu v-model="menuOpen" :close-on-content-click="false" :offset="[8, 0]">
        <template #activator="{ props: activatorProps }">
            <slot name="activator" :props="activatorProps" :selected="modelValue">
                <v-icon-btn v-bind="activatorProps" variant="flat" rounded="circle" icon="i-mdi-palette"
                    :color="modelValue || 'primary'" size="30" />
            </slot>
        </template>

        <v-card rounded="lg" class="overflow-hidden">
            <v-color-picker :model-value="modelValue" mode="hex" :modes="['hex', 'rgb', 'hsl']" elevation="0"
                v-bind="$attrs" @update:model-value="onUpdate" />
        </v-card>
    </v-menu>
</template>