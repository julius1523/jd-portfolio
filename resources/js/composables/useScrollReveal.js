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

    onMounted(() => {
        const rootEl = resolveEl(targetRef.value);
        if (!rootEl) return;

        ctx = gsap.context(() => {
            if (selector) {
                const elements = gsap.utils.toArray(
                    rootEl.querySelectorAll(selector),
                );
                if (!elements.length) return;

                gsap.set(elements, { y, opacity });

                ScrollTrigger.batch(elements, {
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
            } else {
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
        }, rootEl);
    });

    onBeforeUnmount(() => {
        ctx && ctx.kill(false);
    });
}
