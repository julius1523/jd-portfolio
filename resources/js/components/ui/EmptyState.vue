<template>
    <v-empty-state :icon="image ? undefined : icon" :image="image" :headline="headline" :title="title" :text="text"
        :action-text="actionText" :size="size" :color="color" class="py-10" @click:action="emit('action')">
        <template v-for="(_, name) in $slots" #[name]="slotProps">
            <slot :name="name" v-bind="slotProps ?? {}" />
        </template>
    </v-empty-state>
</template>

<script setup>
defineProps({
    icon: { type: String, default: "i-mdi-magnify-outline" },
    image: { type: String, default: undefined },
    headline: { type: String, default: undefined },
    title: { type: String, default: "Nothing found" },
    text: { type: String, default: undefined },
    actionText: { type: String, default: undefined },
    size: { type: [String, Number], default: 64 },
    color: { type: String, default: undefined },
});

const emit = defineEmits(["action"]);
</script>