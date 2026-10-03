<template>
    <v-container>
        <div class="inline-flex text-headline-medium font-semibold mb-4">
            {{ route.meta.title }}
        </div>
        <Shimmer :loading="pageLoading">
            <v-card class="p-5 shadow-sm rounded-[10px] border">
                <div class="d-flex flex-wrap ga-3 align-center mb-2">
                    <div>
                        <div class="text-label-large font-medium mb-1">Page Views</div>
                        <div class="text-body-small text-medium-emphasis">Last {{ periods[period] }} days</div>
                    </div>
                    <v-spacer />
                    <v-btn-toggle v-model="period" density="compact" rounded="lg" variant="outlined" mandatory divided
                        data-shimmer-no-children>
                        <v-btn value="weekly">Weekly</v-btn>
                        <v-btn value="monthly">Monthly</v-btn>
                        <v-btn value="quarterly">Quarterly</v-btn>
                    </v-btn-toggle>
                </div>
                <v-sparkline :model-value="series" auto-draw="once" auto-draw-duration="800" color="primary"
                    line-width="2" smooth="4" stroke-linecap="round" animation />
            </v-card>
        </Shimmer>
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "@/plugins/axios";
import { useRoute } from "vue-router";

const route = useRoute();
const periods = { weekly: 7, monthly: 30, quarterly: 90 };
const pageLoading = ref(true);
const period = ref("weekly");
const byDay = ref({});
const series = computed(() =>
    Array.from({ length: periods[period.value] }, (_, i) => {
        const d = new Date();
        d.setDate(d.getDate() - (periods[period.value] - 1) + i);
        return byDay.value[d.toLocaleDateString("en-CA")] ?? 0;
    }),
);

onMounted(async () => {
    try {
        const { data } = await axios.get("/getDashboard");
        byDay.value = Object.fromEntries((data.daily ?? []).map((d) => [d.day, Number(d.views)]));
    } finally {
        pageLoading.value = false;
    }
});
</script>