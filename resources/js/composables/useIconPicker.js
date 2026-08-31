// @/composables/useIconPicker.js
import { ref } from "vue";
import axios from "@/plugins/axios";

function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

export function useIconPicker(perPage = 48) {
    const icons = ref([]);
    const search = ref("");
    const sets = ref(["mdi", "ri"]);
    const page = ref(1);
    const total = ref(0);
    const loading = ref(false);
    const cache = new Map();

    async function fetchPage(reset = false) {
        loading.value = true;
        try {
            const { data } = await axios.get(`/api/icons`, {
                params: {
                    sets: sets.value.join(","),
                    search: search.value,
                    page: page.value,
                    per_page: perPage,
                },
            });
            data.data.forEach((i) => cache.set(i.name, i.svg));
            icons.value = reset ? data.data : [...icons.value, ...data.data];
            total.value = data.total;
        } catch (e) {
            console.error("Failed to fetch icons:", e);
            if (reset) icons.value = [];
        } finally {
            loading.value = false;
        }
    }

    const onSearch = debounce((val) => {
        search.value = val ?? "";
        page.value = 1;
        fetchPage(true);
    }, 300);

    function onSetsChange(newSets) {
        sets.value = newSets?.length ? newSets : ["mdi", "ri"];
        page.value = 1;
        fetchPage(true);
    }

    function loadMore() {
        if (loading.value || icons.value.length >= total.value) return;
        page.value++;
        fetchPage();
    }

    async function resolveSvg(name) {
        if (!name) return "";
        if (cache.has(name)) return cache.get(name);

        // Normalize "i-ri-github-fill" -> "ri:github-fill"
        let normalized = name;
        if (!name.includes(":") && name.startsWith("i-")) {
            const parts = name.slice(2).split("-");
            const set = parts.shift();
            normalized = `${set}:${parts.join("-")}`;
        }

        const { data } = await axios.get(`/api/icons/lookup`, {
            params: { name: normalized },
        });
        cache.set(name, data.svg); // cache under original key too
        return data.svg;
    }

    return {
        icons,
        search,
        sets,
        loading,
        total,
        fetchPage,
        onSearch,
        onSetsChange,
        loadMore,
        resolveSvg,
    };
}
