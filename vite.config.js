import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
//import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            //input: ['resources/css/app.css', 'resources/js/app.js'],
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        //tailwindcss(),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true // Suppress deprecation warnings from dependencies
            }
        }
    }
});
