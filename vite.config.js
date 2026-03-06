import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                // Here we add the coreui bundle files as needed
                "resources/vendor/coreui/css/coreui.min.css",
                "resources/vendor/coreui/js/coreui.bundle.min.js",
                "resources/vendor/coreui/js/color-modes.js",
            ],
            refresh: true,
        }),
    ],
});
