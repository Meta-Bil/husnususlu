import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Cormorant Garamond', {
                    weights: [500, 600],
                    styles: ['normal', 'italic'],
                    subsets: ['latin', 'latin-ext', 'cyrillic'],
                    preload: [{ weight: 500, style: 'normal' }],
                    fallbacks: ['Georgia', 'Times New Roman', 'serif'],
                    optimizedFallbacks: false,
                }),
                bunny('Manrope', {
                    weights: [400, 500, 600, 700],
                    subsets: ['latin', 'latin-ext', 'cyrillic'],
                    preload: [{ weight: 400 }, { weight: 600 }],
                    fallbacks: ['Segoe UI', 'Helvetica', 'Arial', 'sans-serif'],
                    optimizedFallbacks: false,
                }),
                bunny('Noto Naskh Arabic', {
                    weights: [500, 600, 700],
                    subsets: ['arabic'],
                    preload: false,
                    fallbacks: ['serif'],
                    optimizedFallbacks: false,
                }),
                bunny('IBM Plex Sans Arabic', {
                    weights: [400, 500, 600, 700],
                    subsets: ['arabic'],
                    preload: false,
                    fallbacks: ['sans-serif'],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
