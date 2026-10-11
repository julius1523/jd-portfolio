<script setup>
import { computed, ref, useSlots } from "vue";
import Select from "@/components/forms/Select";

const slots = useSlots();
const reservedSlotNames = ["item.action", "item.status", "expanded-row", "no-data", "bottom"];
const forwardedSlotNames = computed(() => Object.keys(slots).filter((name) => !reservedSlotNames.includes(name)));
const props = defineProps({
    items: { type: Array, default: () => [] },
    headers: { type: Array, required: true },
    itemValue: { type: String, default: "id" },
    sortBy: { type: Array, default: () => [] },
    page: { type: Number, default: 1 },
    itemsPerPage: { type: Number, default: 10 },
    loading: { type: Boolean, default: false },
    addable: { type: Boolean, default: true },
    addLabel: { type: String, default: "New Item" },
    editable: { type: Boolean, default: true },
    removable: { type: Boolean, default: true },
    expandable: { type: Boolean, default: true },
    expandKey: { type: String, default: "description" },
    noDataText: { type: String, default: "No items added yet." },
    disabled: { type: Boolean, default: false },
    density: { type: String, default: "default" },
    rowStatus: { type: Function, default: null },
});
const emit = defineEmits(["add", "edit", "remove", "update:sortBy", "update:page", "update:itemsPerPage"]);
const sortByModel = computed({
    get: () => props.sortBy,
    set: (value) => emit("update:sortBy", value),
});
const pageModel = computed({
    get: () => props.page,
    set: (value) => emit("update:page", value),
});
const itemsPerPageModel = computed({
    get: () => props.itemsPerPage,
    set: (value) => emit("update:itemsPerPage", value),
});
const expandedRows = ref([]);
const canEdit = computed(() => props.addable && props.editable);
const canRemove = computed(() => props.addable && props.removable);
const hasActionColumn = computed(() => canEdit.value || canRemove.value || props.expandable);
const total = computed(() => props.items.length);
const STATUS = {
    new: { color: "success" },
    edited: { color: "warning" },
};
const hasStatus = computed(() => !!props.rowStatus);
const getStatusMeta = (item) => STATUS[props.rowStatus?.(item)] ?? null;
const tableHeaders = computed(() => {
    let list = props.headers;
    if (hasStatus.value && !list.some((h) => h.key === "status")) {
        list = [{ title: "", key: "status", align: "center", width: "2%", sortable: false }, ...list];
    }
    const hasAction = list.some((h) => h.key === "action");
    if (hasActionColumn.value && !hasAction) {
        return [...list, { title: "Action", key: "action", align: "center", width: "5%", sortable: false }];
    }
    return list;
});
const itemsPerPageOptions = [
    { title: "5", value: 5 },
    { title: "10", value: 10 },
    { title: "25", value: 25 },
    { title: "50", value: 50 },
];
const pageCount = computed(() =>
    props.itemsPerPage > 0 ? Math.max(1, Math.ceil(total.value / props.itemsPerPage)) : 1
);
const rangeText = computed(() => {
    if (!total.value) return "0 of 0";
    if (props.itemsPerPage < 0) return `1-${total.value} of ${total.value}`;
    const start = (props.page - 1) * props.itemsPerPage + 1;
    const end = Math.min(props.page * props.itemsPerPage, total.value);
    return `${start}-${end} of ${total.value}`;
});
</script>

<template>
    <div>
        <div v-if="addable" class="mb-5">
            <v-btn variant="flat" color="surface-light" prepend-icon="i-mdi-plus" rounded="pill" :text="addLabel"
                :disabled="disabled" class="border" @click="$emit('add')" />
        </div>

        <v-data-table :headers="tableHeaders" :items="items" v-model:sort-by="sortByModel" v-model:page="pageModel"
            v-model:items-per-page="itemsPerPageModel" :item-value="itemValue" v-model:expanded="expandedRows"
            expand-strategy="single" :loading="loading" :mobile="$vuetify.display.smAndDown" :density="density"
            data-shimmer-no-children>

            <template v-for="name in forwardedSlotNames" #[name]="slotProps" :key="name">
                <slot :name="name" v-bind="slotProps" />
            </template>

            <template v-if="hasStatus" #item.status="{ item }">
                <slot name="item.status" :item="item">
                    <v-avatar v-if="getStatusMeta(item)" :color="getStatusMeta(item).color" size="10" />
                </slot>
            </template>

            <template v-if="hasActionColumn" #item.action="{ item, index, internalItem, isExpanded, toggleExpand }">
                <slot name="item.action" :item="item" :index="index" :internal-item="internalItem"
                    :is-expanded="isExpanded" :toggle-expand="toggleExpand">
                    <div class="d-flex ga-2 justify-end justify-md-center">
                        <v-icon v-if="canEdit" icon="i-mdi-pencil-outline opacity-65" size="small" :disabled="disabled"
                            @click="$emit('edit', item, index)" />
                        <v-icon v-if="canRemove" icon="i-mdi-delete-outline opacity-65" size="small"
                            :disabled="disabled" @click="$emit('remove', item)" />
                        <v-icon v-if="expandable"
                            :icon="isExpanded(internalItem) ? 'i-mdi-chevron-up' : 'i-mdi-chevron-down'" size="small"
                            :disabled="disabled" @click.stop="toggleExpand(internalItem)" />
                    </div>
                </slot>
            </template>

            <template v-if="expandable" v-slot:expanded="{ item }">
                <tr>
                    <td :colspan="tableHeaders.length" class="pa-3 font-italic text-medium-emphasis">
                        "{{ item[expandKey] }}"
                    </td>
                </tr>
            </template>

            <template #bottom>
                <slot name="bottom" :page="pageModel" :page-count="pageCount" :items-per-page="itemsPerPageModel">
                    <div class="d-flex flex-column flex-md-row flex-wrap align-center justify-end ga-3 pa-2 mt-5">
                        <div class="d-flex align-center ga-2">
                            <span class="text-body-2">Items:</span>
                            <Select v-model="itemsPerPageModel" :items="itemsPerPageOptions" item-title="title"
                                item-value="value" :return-object="false" variant="solo" flat single-line
                                :density="density" hide-details :disabled="disabled" :multiple="false" :chip="false"
                                class="vfield-outline">
                                <template #selection="{ item }">
                                    <span>{{ item.title }}</span>
                                </template>
                            </Select>
                        </div>

                        <span class="text-body-2">{{ rangeText }}</span>

                        <v-pagination v-model="pageModel" :length="pageCount" :total-visible="0" :density="density"
                            rounded="circle" class="opacity-60" show-first-last-page :disabled="disabled" />
                    </div>
                </slot>
            </template>

            <template #no-data>
                <slot name="no-data">
                    <div class="text-medium-emphasis py-4">{{ noDataText }}</div>
                </slot>
            </template>
        </v-data-table>
    </div>
</template>