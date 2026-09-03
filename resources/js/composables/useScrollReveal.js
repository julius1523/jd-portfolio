import { onMounted, onBeforeUnmount } from "vue";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

function resolveEl(refValue) {
    if (refValue && refValue.$el) return refValue.$el;
    return refValue;
}

export function useScrollReveal(targetRef, options = {}) {
    const {
        y = 60,
        opacity = 0,
        duration = 1,
        ease = "power3.out",
        stagger = 0.15,
        selector = null,
        start = "top 100%",
        once = true,
    } = options;

    let ctx;
    const processed = new WeakSet();

    function batchElements(rootEl, elements) {
        const newEls = elements.filter((el) => !processed.has(el));
        if (!newEls.length) return;
        newEls.forEach((el) => processed.add(el));

        gsap.set(newEls, { y, opacity });

        ScrollTrigger.batch(newEls, {
            start,
            onEnter: (batch) =>
                gsap.to(batch, {
                    y: 0,
                    opacity: 1,
                    duration,
                    ease,
                    stagger,
                    overwrite: true,
                }),
            onEnterBack: once
                ? undefined
                : (batch) =>
                      gsap.to(batch, {
                          y: 0,
                          opacity: 1,
                          duration,
                          ease,
                          stagger,
                          overwrite: true,
                      }),
            onLeave: once
                ? undefined
                : (batch) => gsap.set(batch, { y, opacity }),
            onLeaveBack: once
                ? undefined
                : (batch) => gsap.set(batch, { y, opacity }),
        });
    }

    function setupRoot(rootEl) {
        gsap.set(rootEl, { y, opacity });

        gsap.to(rootEl, {
            y: 0,
            opacity: 1,
            duration,
            ease,
            scrollTrigger: {
                trigger: rootEl,
                start,
                toggleActions: once
                    ? "play none none none"
                    : "play none none reverse",
            },
        });
    }

    function init() {
        const rootEl = resolveEl(targetRef.value);
        if (!rootEl) return;

        ctx = gsap.context(() => {
            if (selector) {
                const elements = gsap.utils.toArray(
                    rootEl.querySelectorAll(selector),
                );
                if (elements.length) batchElements(rootEl, elements);
            } else {
                setupRoot(rootEl);
            }
        }, rootEl);
    }

    function refresh() {
        if (!selector) return;
        const rootEl = resolveEl(targetRef.value);
        if (!rootEl || !ctx) return;

        ctx.add(() => {
            const elements = gsap.utils.toArray(
                rootEl.querySelectorAll(selector),
            );
            batchElements(rootEl, elements);
        });

        ScrollTrigger.refresh();
    }

    onMounted(init);

    onBeforeUnmount(() => {
        ctx && ctx.kill(false);
    });

    return { refresh };
}
