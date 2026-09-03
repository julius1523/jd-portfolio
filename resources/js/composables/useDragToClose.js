import { ref } from "vue";

export function useDragToClose(
    onClose,
    { threshold = 80, closeDistance = 300, resistance = 0.35 } = {},
) {
    const y = ref(0);
    const dragging = ref(false);
    let startY = 0;

    function onDragMove(e) {
        const delta = e.clientY - startY;
        if (delta <= 0) {
            y.value = 0;
            return;
        }
        y.value =
            delta < threshold
                ? delta
                : threshold + (delta - threshold) * resistance;
    }

    function onDragEnd(e) {
        dragging.value = false;
        window.removeEventListener("pointermove", onDragMove);

        const crossed = e.clientY - startY > threshold;
        y.value = crossed ? closeDistance : 0;

        if (crossed) {
            setTimeout(() => {
                onClose();
                y.value = 0;
            }, 200);
        }
    }

    function onDragStart(e) {
        startY = e.clientY;
        dragging.value = true;
        window.addEventListener("pointermove", onDragMove);
        window.addEventListener("pointerup", onDragEnd, { once: true });
    }

    return { onDragStart, y, dragging };
}
