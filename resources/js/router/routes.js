const pages = import.meta.glob("../pages/**/*.vue");

const page = (name) => pages[`../pages/${name}.vue`];

export default [
    {
        path: "/",
        redirect: { name: "home" },
    },
    {
        path: "/home",
        name: "home",
        component: page("Home"),
        meta: {
            title: "Home",
            middleware: "guest",
            layout: "public",
        },
    },
    {
        path: "/about",
        name: "about",
        component: page("About"),
        meta: {
            title: "About",
            middleware: "guest",
            layout: "public",
        },
    },
    {
        path: "/projects",
        name: "projects",
        component: page("Projects"),
        meta: {
            title: "Projects",
            middleware: "guest",
            layout: "public",
        },
    },
    {
        path: "/contact",
        name: "contact",
        component: page("Contact"),
        meta: {
            title: "Contact",
            middleware: "guest",
            layout: "public",
        },
    },
    {
        path: "/login",
        name: "login",
        component: page("admin/Login"),
        meta: {
            title: "Login",
            middleware: "guest",
            layout: "login",
        },
    },
    {
        path: "/manage-content/:tab?",
        name: "manage-content",
        component: page("admin/ManageContent"),
        meta: {
            title: "Manage Content",
            middleware: "auth",
            layout: "app",
        },
    },
    {
        path: "/system-settings",
        name: "system-settings",
        component: page("admin/SystemSettings"),
        meta: {
            title: "System Settings",
            middleware: "auth",
            layout: "app",
        },
    },
    {
        path: "/:pathMatch(.*)*",
        name: "not-found",
        component: page("errors/NotFound"),
        meta: {
            title: "Page Not Found",
        },
    },
];
