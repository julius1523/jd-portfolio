import mdiIcons from "@iconify-json/mdi/icons.json";
import riIcons from "@iconify-json/ri/icons.json";

const collections = { mdi: mdiIcons, ri: riIcons };

export function toIconifyName(rawIcon) {
    if (!rawIcon) return null;
    return rawIcon.replace(/^i-/, "").replace(/^([a-z]+)-/, "$1:");
}

export function getIconSvg(rawIcon) {
    const normalized = toIconifyName(rawIcon);
    if (!normalized) return null;

    const [prefix, name] = normalized.split(":");
    const set = collections[prefix];
    const icon = set?.icons?.[name];
    if (!icon) return null;

    const width = icon.width ?? set.width ?? 24;
    const height = icon.height ?? set.height ?? 24;

    return `
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 ${width} ${height}"
            width="1em"
            height="1em"
            fill="currentColor"
            preserveAspectRatio="xMidYMid meet"
            style="display:block; margin:auto;"
        >
            ${icon.body}
        </svg>
    `.trim();
}
