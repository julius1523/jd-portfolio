<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    selector: {
        type: String,
        default: '.animate',
    },
    threshold: {
        type: Number,
        default: 0,
    },
    stagger: {
        type: Number,
        default: 120,
    },
    once: {
        type: Boolean,
        default: true,
    },
});

const target = ref(null);
let observer;

onMounted(() => {
    const items = [...target.value.querySelectorAll(props.selector)];

    observer = new IntersectionObserver((entries) => {
        const visibleEntries = entries.filter(entry => entry.isIntersecting);

        visibleEntries.forEach((entry, index) => {
            const item = entry.target;

            item.style.transitionDelay = `${index * props.stagger}ms`;

            item.classList.remove('lazy-hidden');
            item.classList.add('lazy-visible');

            if (props.once) {
                observer.unobserve(item);
            }
        });

        if (!props.once) {
            entries
                .filter(entry => !entry.isIntersecting)
                .forEach(entry => {
                    entry.target.style.transitionDelay = '0ms';
                    entry.target.classList.remove('lazy-visible');
                    entry.target.classList.add('lazy-hidden');
                });
        }
    }, {
        threshold: props.threshold
    });

    items.forEach((item, index) => {
        item.dataset.stagger = index;
        item.classList.add('lazy-hidden');
        observer.observe(item);
    });
});

onBeforeUnmount(() => {
    observer?.disconnect();
});
</script>

<template>
    <div ref="target">
        <slot />
    </div>
</template>