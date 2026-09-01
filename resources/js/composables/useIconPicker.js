import { ref } from "vue";
import axios from "@/plugins/axios";

function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

export function useIconPicker(perPage = 24, initialSets = ["mdi"]) {
    const icons = ref([]);
    const search = ref("");
    const sets = ref([...initialSets]);
    const page = ref(1);
    const total = ref(0);
    const loading = ref(false);
    const cache = new Map();

    async function getIcons(reset = false) {
        loading.value = true;
        try {
            const { data } = await axios.get(`/api/icons/getIcons`, {
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
        getIcons(true);
    }, 300);

    function onSetsChange(newSets) {
        sets.value = newSets?.length ? newSets : [...initialSets];
        page.value = 1;
        getIcons(true);
    }

    function loadMore() {
        if (loading.value || icons.value.length >= total.value) return;
        page.value++;
        getIcons();
    }

    return {
        icons,
        search,
        sets,
        loading,
        total,
        getIcons,
        onSearch,
        onSetsChange,
        loadMore,
    };
}
