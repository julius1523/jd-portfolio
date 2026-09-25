import { ref, onMounted, onUnmounted } from "vue";

export function useScrollToTop(threshold = 300) {
    const visible = ref(false);
    let ticking = false;

    function handleScroll() {
        if (ticking) return;
        ticking = true;

        window.requestAnimationFrame(() => {
            visible.value = window.scrollY > threshold;
            ticking = false;
        });
    }

    function scrollToTop() {
        const prefersReducedMotion = window.matchMedia(
            "(prefers-reduced-motion: reduce)",
        ).matches;

        window.scrollTo({
            top: 0,
            behavior: prefersReducedMotion ? "auto" : "smooth",
        });
    }

    onMounted(() => {
        window.addEventListener("scroll", handleScroll, { passive: true });
    });

    onUnmounted(() => {
        window.removeEventListener("scroll", handleScroll);
    });

    return { visible, scrollToTop };
}
