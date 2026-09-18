<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount, useSlots } from "vue";
import { useTheme } from "vuetify";
import { useOverlayScrollbars } from "overlayscrollbars-vue";

const slots = useSlots();
const theme = useTheme();
const reservedSlotNames = ["item", "selection", "no-data"];
const forwardedSlotNames = computed(() => Object.keys(slots).filter((name) => !reservedSlotNames.includes(name)));
const props = defineProps({
    modelValue: { type: [Array, String], default: () => [] },
    items: { type: Array, default: () => [] },
    label: { type: String, default: '' },
    hint: { type: String, default: '' },
    persistentHint: { type: Boolean, default: true },
    multiple: { type: Boolean, default: true },
    variant: { type: String },
    flat: { type: Boolean, default: true },
    rounded: { type: [String, Boolean], default: 'lg' },
    density: { type: String, default: 'comfortable' },
    singleLine: { type: Boolean, default: false, },
    errorMessages: { type: [String, Array], default: () => [] },
    itemTitle: { type: String, default: 'title' },
    itemValue: { type: String, default: 'value' },
    reservedForCounter: { type: Number, default: 56 },
    chip: { type: Boolean, default: true },
})
const emit = defineEmits(['update:modelValue']);
const search = ref('');
const selectRef = ref(null);
const mirrorRef = ref(null);
const textMirrorRef = ref(null);
const visibleCount = ref(0);
const menuOpen = ref(false);
const listClass = `os-select-list-${Math.random().toString(36).slice(2, 10)}`;

const [initListScrollbars, getListOsInstance] = useOverlayScrollbars({
    options: {
        scrollbars: {
            autoHide: "move",
            theme: theme.current.value.dark ? "os-theme-light" : "os-theme-dark",
        },
    },
});

watch(menuOpen, (open) => {
    if (!open) return;
    nextTick(() => {
        const listEl = document.querySelector(`.${listClass}`);
        if (listEl && !getListOsInstance()) {
            initListScrollbars(listEl);
        }
    });
});

const safeList = computed(() => Array.isArray(props.modelValue) ? props.modelValue : [props.modelValue].filter(Boolean));
const overflowCount = computed(() => safeList.value.length - visibleCount.value);
const visibleText = computed(() => safeList.value.slice(0, visibleCount.value).map(resolveText).join(', '));

function resolveText(item) {
    if (typeof item === 'string') return item;
    return item?.[props.itemTitle] ?? '';
};
function removeItem(item) {
    const next = safeList.value.filter((value) => resolveText(value) !== resolveText(item));
    emit('update:modelValue', next);
};
function getFieldElement() {
    return selectRef.value?.$el?.querySelector('.v-field__field');
};

async function recalcVisibleCount() {
    await nextTick();
    const list = safeList.value
    const fieldEl = getFieldElement();
    if (!fieldEl || !list.length) {
        visibleCount.value = list.length;
        return;
    };
    const availableWidth = fieldEl.clientWidth - props.reservedForCounter;
    if (props.chip) {
        const mirrorEl = mirrorRef.value;
        if (!mirrorEl) {
            visibleCount.value = list.length;
            return;
        };
        const chipMirrors = mirrorEl.children;
        let used = 0;
        let count = 0;
        for (const chipEl of chipMirrors) {
            const chipWidth = chipEl.offsetWidth + 8;
            if (used + chipWidth > availableWidth && count > 0) {
                break;
            };
            used += chipWidth;
            count++;
        }
        visibleCount.value = count >= list.length ? list.length : Math.max(count, 1);
        return;
    }
    const mirrorEl = textMirrorRef.value;
    if (!mirrorEl) {
        visibleCount.value = list.length;
        return;
    };
    const textMirrors = mirrorEl.children;
    let used = 0;
    let count = 0;
    for (const spanEl of textMirrors) {
        const itemWidth = spanEl.offsetWidth
        if (used + itemWidth > availableWidth && count > 0) {
            break;
        };
        used += itemWidth;
        count++;
    };

    visibleCount.value = count >= list.length ? list.length : Math.max(count, 1);
};

let resizeObserver;

onMounted(() => {
    recalcVisibleCount();
    const fieldEl = getFieldElement();
    if (fieldEl && 'ResizeObserver' in window) {
        resizeObserver = new ResizeObserver(recalcVisibleCount)
        resizeObserver.observe(fieldEl)
    }
});

onBeforeUnmount(() => {
    resizeObserver?.disconnect();
});

watch(
    () => [props.modelValue, props.chip],
    recalcVisibleCount,
    { deep: true }
);
</script>

<template>
    <div class="relative [&_.v-field__input]:flex-nowrap">
        <v-select ref="selectRef" v-model:search="search" v-model:menu="menuOpen" :model-value="modelValue"
            :items="items" :variant="variant" :flat="flat" :label="label" :rounded="rounded" :density="density"
            :single-line="singleLine" :hint="hint" :persistent-hint="persistentHint" :multiple="multiple"
            :error-messages="errorMessages" :item-title="itemTitle" :item-value="itemValue" :hide-no-data="false"
            :no-auto-scroll="true" autocomplete="off" :list-props="{
                density: 'comfortable',
                nav: true,
                prependGap: 15,
                maxHeight: 300,
                class: `pt-1 ${listClass}`,
            }" :menu-props="{
                maxWidth: '100',
                width: 'auto',
                contentClass: 'rounded-[10px]',
            }" @update:model-value="emit('update:modelValue', $event)">

            <template v-for="name in forwardedSlotNames" #[name]="scope" :key="name">
                <slot :name="name" v-bind="scope" />
            </template>

            <template #item="scope">
                <slot v-if="slots.item" name="item" v-bind="scope" />
                <v-list-item v-else v-bind="scope.props" :title="undefined">
                    <template #prepend="{ isSelected }">
                        <v-checkbox-btn color="primary" :model-value="isSelected" density="compact" :ripple="false"
                            @click.stop="scope.props.onClick" />
                    </template>

                    <v-list-item-title class="text-label-medium">
                        {{ resolveText(scope.item) }}
                    </v-list-item-title>
                </v-list-item>
            </template>

            <template #selection="scope">
                <slot v-if="slots.selection" name="selection" v-bind="scope" />
                <template v-else-if="chip">
                    <v-chip v-if="scope.index < visibleCount" size="small" closable
                        @click:close="removeItem(scope.item)">
                        {{ resolveText(scope.item) }}
                    </v-chip>

                    <v-chip v-else-if="scope.index === visibleCount" size="small" density="comfortable" variant="tonal">
                        +{{ safeList.length - visibleCount }}
                    </v-chip>
                </template>

                <template v-else-if="scope.index === 0">
                    {{ visibleText }}
                    <span v-if="overflowCount > 0" class="text-medium-emphasis">
                        &nbsp;+{{ overflowCount }}
                    </span>
                </template>
            </template>

            <template #no-data>
                <slot v-if="slots['no-data']" name="no-data" />
                <v-list-item v-else>
                    <v-list-item-subtitle class="text-center">
                        No data available
                    </v-list-item-subtitle>
                </v-list-item>
            </template>
        </v-select>

        <div ref="mirrorRef" class="pointer-events-none absolute h-0 invisible overflow-hidden whitespace-nowrap"
            aria-hidden="true">
            <span v-for="item in safeList" :key="resolveText(item)"
                class="mr-2 inline-block h-6 px-3 text-[0.8125rem] leading-6">
                {{ resolveText(item) }}
            </span>
        </div>

        <div ref="textMirrorRef" class="pointer-events-none absolute h-0 invisible overflow-hidden whitespace-nowrap"
            aria-hidden="true">
            <span v-for="(item, index) in safeList" :key="resolveText(item)">
                {{ resolveText(item) }}<template v-if="index < safeList.length - 1">, </template>
            </span>
        </div>
    </div>
</template>