<template>
    <div>
        <div v-if="addable" class="d-flex justify-end mb-2">
            <v-btn variant="flat" color="revert-color" rounded="lg" prepend-icon="i-mdi-plus" :text="addLabel"
                @click="$emit('add')" />
        </div>

        <v-data-table :headers="tableHeaders" :items="items" :item-value="itemValue" v-model:expanded="expandedRows"
            :show-expand="expandable" :mobile="$vuetify.display.smAndDown" class="border rounded-lg"
            data-shimmer-no-children>

            <template v-for="name in forwardedSlotNames" #[name]="slotProps" :key="name">
                <slot :name="name" v-bind="slotProps" />
            </template>

            <template v-if="canEdit || canRemove" #item.action="{ item, index }">
                <slot name="item.action" :item="item" :index="index">
                    <div class="d-flex ga-2 justify-end">
                        <v-icon v-if="canEdit" icon="i-mdi-pencil-outline opacity-70" size="small"
                            @click="$emit('edit', item, index)" />
                        <v-icon v-if="canRemove" icon="i-mdi-delete-outline opacity-70" size="small"
                            @click="$emit('remove', index)" />
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
        </v-data-table>
    </div>
</template>

<script setup>
import { computed, ref, useSlots } from "vue";
const props = defineProps({
    items: { type: Array, default: () => [] },
    headers: { type: Array, required: true },
    itemValue: { type: String, default: "id" },
    addable: { type: Boolean, default: true },
    addLabel: { type: String, default: "New Item" },
    editable: { type: Boolean, default: true },
    removable: { type: Boolean, default: true },
    expandable: { type: Boolean, default: true },
    expandKey: { type: String, default: "description" },
    noDataText: { type: String, default: "No items added yet." },
});
defineEmits(["add", "edit", "remove"]);
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
const slots = useSlots();
const reservedSlotNames = ["item.action", "expanded-row", "no-data"];
const forwardedSlotNames = computed(() =>
    Object.keys(slots).filter((name) => !reservedSlotNames.includes(name))
);
</script>