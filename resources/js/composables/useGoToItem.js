import { nextTick } from "vue";

export function useGoToItem(getList, options) {
    return async function goToItem(id) {
        await nextTick();
        const [s] = options.sortBy ?? [];
        const items = getList();
        const list = s
            ? [...items].sort(
                  (a, b) =>
                      String(a[s.key] ?? "").localeCompare(
                          String(b[s.key] ?? ""),
                      ) * (s.order === "desc" ? -1 : 1),
              )
            : items;
        const i = list.findIndex((p) => p.id === id);
        if (i > -1 && options.itemsPerPage > 0) {
            options.page = Math.floor(i / options.itemsPerPage) + 1;
        }
    };
}
