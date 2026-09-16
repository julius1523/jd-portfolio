<script setup>
import { computed, ref, useSlots } from "vue";

const slots = useSlots();
const reservedSlotNames = ["item.action", "expanded-row", "no-data"];
const forwardedSlotNames = computed(() => Object.keys(slots).filter((name) => !reservedSlotNames.includes(name)));
const props = defineProps({
    items: { type: Array, default: () => [] },
    headers: { type: Array, required: true },
    itemValue: { type: String, default: "id" },
    itemsLength: { type: Number, default: 0 },
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
const tableHeaders = computed(() => {
    const hasAction = props.headers.some((h) => h.key === "action");
    if ((canEdit.value || canRemove.value) && !hasAction) {
        return [...props.headers, { title: "Action", key: "action", align: "end", sortable: false }];
    }
    return props.headers;
});
</script>

<template>
    <div>
        <div v-if="addable" class="mb-2">
            <v-btn variant="tonal" prepend-icon="i-mdi-plus" :text="addLabel" :disabled="disabled"
                class="rounded-[10px]" @click="$emit('add')" />
        </div>

        <v-data-table-server :headers="tableHeaders" :items="items" :items-length="itemsLength"
            v-model:sort-by="sortByModel" v-model:page="pageModel" v-model:items-per-page="itemsPerPageModel"
            :item-value="itemValue" v-model:expanded="expandedRows" :show-expand="expandable" :loading="loading"
            :mobile="$vuetify.display.smAndDown" :density="density" class="border rounded-[10px]"
            data-shimmer-no-children>

            <template v-for="name in forwardedSlotNames" #[name]="slotProps" :key="name">
                <slot :name="name" v-bind="slotProps" />
            </template>

            <template v-if="canEdit || canRemove" #item.action="{ item, index }">
                <slot name="item.action" :item="item" :index="index">
                    <div class="d-flex ga-2 justify-end">
                        <v-icon v-if="canEdit" icon="i-mdi-pencil-outline opacity-70" size="small" :disabled="disabled"
                            @click="$emit('edit', item, index)" />
                        <v-icon v-if="canRemove" icon="i-mdi-delete-outline opacity-70" size="small"
                            :disabled="disabled" @click="$emit('remove', item)" />
                    </div>
                </slot>
            </template>

            <template v-if="expandable" #expanded-row="{ columns, item }">
                <slot name="expanded-row" :columns="columns" :item="item">
                    <tr>
                        <td :colspan="columns.length" class="py-3 font-italic text-medium-emphasis">
                            "{{ item[expandKey] }}"
                        </td>
                    </tr>
                </slot>
            </template>

            <template #no-data>
                <slot name="no-data">
                    <div class="text-medium-emphasis py-4">{{ noDataText }}</div>
                </slot>
            </template>
        </v-data-table-server>
    </div>
</template>