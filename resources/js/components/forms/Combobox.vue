<script setup>
import { ref, watch, nextTick, onMounted, onBeforeUnmount } from "vue";
const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    items: { type: Array, default: () => [] },
    label: { type: String, default: '' },
    hint: { type: String, default: '' },
    persistentHint: { type: Boolean, default: true },
    multiple: { type: Boolean, default: true },
    variant: { type: String, default: 'solo' },
    flat: { type: Boolean, default: true },
    rounded: { type: [String, Boolean], default: 'lg' },
    density: { type: String, default: 'comfortable' },
    errorMessages: { type: [String, Array], default: () => [] },
    itemTitle: { type: String, default: 'title' },
    itemValue: { type: String, default: 'value' },
    reservedForCounter: { type: Number, default: 56 },
});
const emit = defineEmits(['update:modelValue']);
const search = ref('');
const comboRef = ref(null);
const mirrorRef = ref(null);
const visibleCount = ref(0);
function resolveText(item) {
    if (typeof item === 'string') return item
    return item?.[props.itemTitle] ?? ''
}
function removeItem(item) {
    const next = props.modelValue.filter((i) => resolveText(i) !== resolveText(item))
    emit('update:modelValue', next)
}
async function recalcVisibleCount() {
    await nextTick();
    const fieldEl = comboRef.value?.$el?.querySelector('.v-field__field');
    const mirrorEl = mirrorRef.value;
    if (!fieldEl || !mirrorEl || props.modelValue.length === 0) {
        visibleCount.value = props.modelValue.length;
        return;
    }
    const availableWidth = fieldEl.clientWidth - props.reservedForCounter;
    const chipMirrors = mirrorEl.querySelectorAll('.chip-mirror__chip');
    let used = 0;
    let count = 0;
    for (const chipEl of chipMirrors) {
        const chipWidth = chipEl.offsetWidth + 8;
        if (used + chipWidth > availableWidth && count > 0) break;
        used += chipWidth;
        count++;
    };
    visibleCount.value = count >= props.modelValue.length ? props.modelValue.length : Math.max(count, 1);
}
let resizeObserver;
onMounted(() => {
    recalcVisibleCount();
    const fieldEl = comboRef.value?.$el?.querySelector('.v-field__field');
    if (fieldEl && 'ResizeObserver' in window) {
        resizeObserver = new ResizeObserver(() => recalcVisibleCount());
        resizeObserver.observe(fieldEl);
    }
});
onBeforeUnmount(() => {
    resizeObserver?.disconnect();
});
watch(() => props.modelValue, recalcVisibleCount, { deep: true });
</script>

<template>
    <div class="position-relative">
        <v-select ref="comboRef" :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)"
            v-model:search="search" :hide-no-data="false" :items="items" :variant="variant" :flat="flat" :label="label"
            :rounded="rounded" :density="density" :hint="hint" :persistent-hint="persistentHint" :multiple="multiple"
            :list-props="{ rounded: 'lg', nav: true, variant: 'plain', density: 'compact', prependGap: 15, activeClass: 'opacity-100' }"
            :error-messages="errorMessages" :item-title="itemTitle" :item-value="itemValue" autocomplete="off">
            <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
                <slot :name="slotName" v-bind="slotProps ?? {}" />
            </template>

            <template v-slot:selection="{ item, index }">
                <v-chip v-if="index < visibleCount" size="small" density="comfortable" closable
                    @click:close="removeItem(item)">
                    {{ resolveText(item) }}
                </v-chip>
                <v-chip v-else-if="index === visibleCount" size="small" density="comfortable" variant="tonal">
                    +{{ modelValue.length - visibleCount }}
                </v-chip>
            </template>

            <template v-slot:no-data>
                <v-list-item>
                    <v-list-item-subtitle>
                        No results matching "<strong>{{ search }}</strong>". Press
                        <kbd>enter</kbd>
                        to create a new one
                    </v-list-item-subtitle>
                </v-list-item>
            </template>
        </v-select>

        <div ref="mirrorRef" class="chip-mirror">
            <span v-for="item in modelValue" :key="resolveText(item)" class="chip-mirror__chip">
                {{ resolveText(item) }}
            </span>
        </div>
    </div>
</template>

<style lang="css" scoped>
.chip-mirror {
    position: absolute;
    visibility: hidden;
    height: 0;
    overflow: hidden;
    white-space: nowrap;
    pointer-events: none;
}

.chip-mirror__chip {
    display: inline-block;
    padding: 0 12px;
    margin-right: 8px;
    font-size: 0.8125rem;
    height: 24px;
    line-height: 24px;
}

:deep(.v-field__input) {
    flex-wrap: nowrap;
}
</style>