const CHARSET_SOURCES = {
    alpha: "A-Za-z",
    alphaSpace: "A-Za-z\\s",
    alphanumeric: "A-Za-z0-9",
    numeric: "0-9",
    decimal: "0-9.",
    name: "\\p{L}\\s.'’-",
    username: "A-Za-z0-9_.",
    phone: "0-9+()\\s-",
    email: "A-Za-z0-9@._+-",
    address: "\\p{L}0-9\\s.,#'’/-",
    text: "\\p{L}0-9\\s.,;:!?'’\"()/&%@#+-",
};

export const CHARS = Object.fromEntries(
    Object.entries(CHARSET_SOURCES).map(([key, src]) => [
        key,
        new RegExp(`[${src}]`, "u"),
    ]),
);

export const PATTERNS = Object.fromEntries(
    Object.entries(CHARSET_SOURCES).map(([key, src]) => [
        key,
        new RegExp(`^[${src}]*$`, "u"),
    ]),
);

export const PATTERN_MESSAGES = {
    alpha: "Letters only",
    alphaSpace: "Letters and spaces only",
    alphanumeric: "Letters and numbers only",
    numeric: "Numbers only",
    decimal: "Numbers and decimal point only",
    name: "Letters, spaces, and . ' - only",
    username: "Letters, numbers, _ and . only",
    phone: "Numbers, +, -, ( ) and spaces only",
    email: "Contains invalid characters",
    address: "Contains invalid characters",
    text: "Contains invalid characters",
};
