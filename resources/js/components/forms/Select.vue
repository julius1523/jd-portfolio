<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from "vue";
const props = defineProps({
    modelValue: { type: [Array, String], default: () => [] },
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
    chip: { type: Boolean, default: true },
});
const emit = defineEmits(['update:modelValue']);
const search = ref('');
const selectRef = ref(null);
const mirrorRef = ref(null);
const textMirrorRef = ref(null);
const visibleCount = ref(0);
const safeList = computed(() =>
    Array.isArray(props.modelValue) ? props.modelValue : [props.modelValue].filter(Boolean)
);
const overflowCount = computed(() => safeList.value.length - visibleCount.value);
const visibleText = computed(() =>
    safeList.value.slice(0, visibleCount.value).map(resolveText).join(', ')
);
function resolveText(item) {
    if (typeof item === 'string') return item;
    return item?.[props.itemTitle] ?? '';
};
function removeItem(item) {
    const next = safeList.value.filter((i) => resolveText(i) !== resolveText(item));
    emit('update:modelValue', next);
};
async function recalcVisibleCount() {
    await nextTick();
    const fieldEl = selectRef.value?.$el?.querySelector('.v-field__field');
    const list = safeList.value;
    if (!fieldEl || list.length === 0) {
        visibleCount.value = list.length;
        return;
    }
    const availableWidth = fieldEl.clientWidth - props.reservedForCounter;
    if (props.chip) {
        const mirrorEl = mirrorRef.value;
        if (!mirrorEl) { visibleCount.value = list.length; return; }
        const chipMirrors = mirrorEl.querySelectorAll('.chip-mirror__chip');
        let used = 0, count = 0;
        for (const chipEl of chipMirrors) {
            const chipWidth = chipEl.offsetWidth + 8;
            if (used + chipWidth > availableWidth && count > 0) break;
            used += chipWidth;
            count++;
        }
        visibleCount.value = count >= list.length ? list.length : Math.max(count, 1);
    } else {
        const mirrorEl = textMirrorRef.value;
        if (!mirrorEl) { visibleCount.value = list.length; return; }
        const textMirrors = mirrorEl.querySelectorAll('.text-mirror__item');
        let used = 0, count = 0;
        for (const spanEl of textMirrors) {
            const itemWidth = spanEl.offsetWidth;
            if (used + itemWidth > availableWidth && count > 0) break;
            used += itemWidth;
            count++;
        }
        visibleCount.value = count >= list.length ? list.length : Math.max(count, 1);
    };
};
let resizeObserver;
onMounted(() => {
    recalcVisibleCount();
    const fieldEl = selectRef.value?.$el?.querySelector('.v-field__field');
    if (fieldEl && 'ResizeObserver' in window) {
        resizeObserver = new ResizeObserver(() => recalcVisibleCount());
        resizeObserver.observe(fieldEl);
    }
});
onBeforeUnmount(() => {
    resizeObserver?.disconnect();
});
watch(() => [props.modelValue, props.chip], recalcVisibleCount, { deep: true });
</script>

<template>
    <div class="position-relative">
        <v-select ref="selectRef" :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)"
            no-auto-scroll v-model:search="search" :hide-no-data="false" :items="items" :variant="variant" :flat="flat"
            :label="label" :rounded="rounded" :density="density" :hint="hint" :persistent-hint="persistentHint"
            :multiple="multiple" :list-props="{ density: 'comfortable', nav: true, prependGap: 15, class: 'pt-1' }"
            :error-messages="errorMessages" :item-title="itemTitle" :item-value="itemValue"
            :menu-props="{ maxWidth: '100', width: 'auto', contentClass: 'rounded-lg' }" autocomplete="off">
            <template v-for="(_, slot) in $slots" #[slot]="scope">
                <slot :name="slot" v-bind="scope" />
            </template>

            <template v-slot:item="{ item, props: itemProps }">
                <v-list-item v-bind="itemProps" :title="undefined">
                    <template v-slot:prepend="{ isSelected }">
                        <v-checkbox-btn color="primary" :model-value="isSelected" density="compact" :ripple="false"
                            @click.stop="itemProps.onClick" />
                    </template>

                    <v-list-item-title class="text-label-medium">
                        {{ resolveText(item) }}
                    </v-list-item-title>
                </v-list-item>
            </template>

            <template v-slot:selection="{ item, index }">
                <template v-if="chip">
                    <v-chip v-if="index < visibleCount" size="small" density="comfortable" closable
                        @click:close="removeItem(item)">
                        {{ resolveText(item) }}
                    </v-chip>
                    <v-chip v-else-if="index === visibleCount" size="small" density="comfortable" variant="tonal">
                        +{{ safeList.length - visibleCount }}
                    </v-chip>
                </template>
                <template v-else-if="index === 0">
                    {{ visibleText }}
                    <span v-if="overflowCount > 0" class="text-medium-emphasis">
                        &nbsp;+{{ overflowCount }}
                    </span>
                </template>
            </template>

            <template v-slot:no-data>
                <v-list-item>
                    <v-list-item-subtitle class="text-center">
                        No data available
                    </v-list-item-subtitle>
                </v-list-item>
            </template>
        </v-select>

        <div ref="mirrorRef" class="chip-mirror">
            <span v-for="item in safeList" :key="resolveText(item)" class="chip-mirror__chip">
                {{ resolveText(item) }}
            </span>
        </div>

        <div ref="textMirrorRef" class="chip-mirror">
            <span v-for="(item, i) in safeList" :key="resolveText(item)" class="text-mirror__item">
                {{ resolveText(item) }}<template v-if="i < safeList.length - 1">, </template>
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