const uuid = () =>
    "10000000-1000-4000-8000-100000000000".replace(/[018]/g, (c) =>
        (
            c ^
            (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
        ).toString(16),
    );

const getId = (storage, key) => {
    try {
        let id = storage.getItem(key);
        if (!id) {
            id = uuid();
            storage.setItem(key, id);
        }
        return id;
    } catch {
        return uuid();
    }
};

export function setupTracker(router) {
    router.afterEach((to) => {
        try {
            if (to.meta?.layout !== "public") return;

            fetch("/api/track", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    visitor_id: getId(localStorage, "vid"),
                    session_id: getId(sessionStorage, "sid"),
                    path: to.path,
                    referrer: document.referrer || null,
                    utm_source: to.query.utm_source ?? null,
                    utm_medium: to.query.utm_medium ?? null,
                    utm_campaign: to.query.utm_campaign ?? null,
                }),
                keepalive: true,
            }).catch(() => {});
        } catch (e) {
            console.warn("tracker error:", e);
        }
    });
}
