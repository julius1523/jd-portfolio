const getInput = (el) => el.querySelector("input, textarea") || el;

const insertText = (input, text) => {
    const max = input.maxLength > 0 ? input.maxLength : Infinity;
    const start = input.selectionStart ?? input.value.length;
    const end = input.selectionEnd ?? input.value.length;
    const room = max - (input.value.length - (end - start));
    text = text.slice(0, Math.max(room, 0));
    if (!text) return;

    input.setRangeText(text, start, end, "end");
    input.dispatchEvent(new Event("input", { bubbles: true }));
};

export default {
    mounted(el, binding) {
        const input = getInput(el);
        const regex =
            binding.value instanceof RegExp
                ? binding.value
                : new RegExp(binding.value, "u");
        const strict = !!binding.modifiers.strict;
        const isAllowed = (c) => regex.test(c);
        const filter = (s) => [...s].filter(isAllowed).join("");

        const onBeforeInput = (e) => {
            if (
                e.inputType?.startsWith("insert") &&
                e.data &&
                ![...e.data].every(isAllowed)
            ) {
                e.preventDefault();
            }
        };

        const onPaste = (e) => {
            const text = (e.clipboardData || window.Clipboard).getData("text");
            e.preventDefault();
            if (strict && ![...text].every(isAllowed)) return;
            insertText(input, filter(text));
        };

        const onDrop = (e) => {
            const text = e.dataTransfer?.getData("text") || "";
            e.preventDefault();
            if (strict && ![...text].every(isAllowed)) return;
            insertText(input, filter(text));
        };

        const onInput = () => {
            const cleaned = filter(input.value);
            if (cleaned !== input.value) {
                input.value = cleaned;
                input.dispatchEvent(new Event("input", { bubbles: true }));
            }
        };

        input.addEventListener("beforeinput", onBeforeInput);
        input.addEventListener("paste", onPaste);
        input.addEventListener("drop", onDrop);
        input.addEventListener("input", onInput);

        input._allowCharsCleanup = () => {
            input.removeEventListener("beforeinput", onBeforeInput);
            input.removeEventListener("paste", onPaste);
            input.removeEventListener("drop", onDrop);
            input.removeEventListener("input", onInput);
        };
    },
    unmounted(el) {
        getInput(el)._allowCharsCleanup?.();
    },
};
