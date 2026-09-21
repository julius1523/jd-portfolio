let isOpen = false;
let waiting = [];

export const scrollGate = {
    reset() {
        isOpen = false;
    },
    open() {
        isOpen = true;
        waiting.forEach((resolve) => resolve());
        waiting = [];
    },
    wait(ms = 800) {
        if (isOpen) return Promise.resolve();
        return new Promise((resolve) => {
            waiting.push(resolve);
            setTimeout(resolve, ms);
        });
    },
};

const KEY = "app:scroll";

export function trackScroll(router) {
    const save = () => {
        try {
            sessionStorage.setItem(
                KEY,
                JSON.stringify({
                    path: router.currentRoute.value.fullPath,
                    y: window.scrollY,
                }),
            );
        } catch {}
    };

    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "hidden") save();
    });
    window.addEventListener("pagehide", save);
}

export function readReloadScroll(to) {
    try {
        const nav = performance.getEntriesByType("navigation")[0];
        if (nav?.type !== "reload") return null;
        const saved = JSON.parse(sessionStorage.getItem(KEY));
        return saved?.path === to.fullPath ? { left: 0, top: saved.y } : null;
    } catch {
        return null;
    }
}
