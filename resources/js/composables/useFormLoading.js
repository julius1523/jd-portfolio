import { ref } from "vue";

export function useFormLoading() {
    const loading = ref(false);

    const wrap = async (callback) => {
        loading.value = true;
        try {
            return await callback();
        } finally {
            loading.value = false;
        }
    };

    return { loading, wrap };
}
