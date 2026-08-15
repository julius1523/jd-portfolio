<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { mdiArrowLeft, mdiArrowRight } from "@mdi/js";
const carousel = ref(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(true);
const updateButtons = () => {
    if (!carousel.value) return;
    const el = carousel.value;
    canScrollLeft.value = el.scrollLeft > 0;
    canScrollRight.value =
        el.scrollLeft + el.clientWidth < el.scrollWidth - 1;
};
const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});
const scroll = (direction) => {
    if (!carousel.value) return;
    const el = carousel.value;
    const cards = Array.from(el.querySelectorAll('.card-item'));
    if (!cards.length) return;
    const edge = parseFloat(getComputedStyle(el).paddingLeft) || 0;
    const current = el.scrollLeft;
    const buffer = 1;
    let targetCard;
    if (direction > 0) {
        targetCard = cards.find(card => card.offsetLeft - edge > current + buffer);
        targetCard ??= cards[cards.length - 1];
    } else {
        targetCard = [...cards].reverse()
            .find(card => card.offsetLeft - edge < current - buffer);
        targetCard ??= cards[0];
    }
    el.scrollTo({
        left: targetCard.offsetLeft - edge,
        behavior: 'smooth',
    });
};
let resizeObserver;
onMounted(() => {
    requestAnimationFrame(updateButtons);
    const el = carousel.value;
    el.addEventListener('scroll', updateButtons);
    resizeObserver = new ResizeObserver(() => {
        requestAnimationFrame(updateButtons);
    });
    resizeObserver.observe(el);
});
onBeforeUnmount(() => {
    const el = carousel.value;
    el?.removeEventListener('scroll', updateButtons);
    resizeObserver?.disconnect();
});
</script>

<template>
    <div class="card-carousel-wrapper">
        <div ref="carousel" class="card-carousel reveal-item ga-4 ga-md-8">
            <div v-for="(item, i) in items" :key="i" class="card-item">
                <slot :item="item" :index="i" />
            </div>
        </div>
    </div>

    <div v-if="($vuetify.display.mdAndDown || items.length > 4) && (canScrollLeft || canScrollRight)"
        class="d-flex ga-4 justify-end reveal-item">
        <v-btn :icon="mdiArrowLeft" variant="tonal" size="small" :disabled="!canScrollLeft" @click="scroll(-1)" />
        <v-btn :icon="mdiArrowRight" variant="tonal" size="small" :disabled="!canScrollRight" @click="scroll(1)" />
    </div>
</template>