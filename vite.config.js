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
                // Body face. latin-ext is kept so accented client and
                // person names render in the correct face rather than
                // falling back glyph-by-glyph.
                //
                // Only weight 400 is preloaded: each preloaded weight costs
                // one request per subset, and preloads compete for bandwidth
                // with the LCP text itself. The rest arrive via `swap`.
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                    subsets: ['latin', 'latin-ext'],
                    preload: [{ weight: 400 }],
                    optimizedFallbacks: true,
                }),
                // Display face for headings and the logotype. The h1 is
                // usually the LCP element, so weight 700 is preloaded.
                bunny('Space Grotesk', {
                    weights: [500, 700],
                    subsets: ['latin'],
                    preload: [{ weight: 700 }],
                    optimizedFallbacks: true,
                }),
                // Arabic face — deferred along with the Arabic locale.
                // Uncomment together with the `ar` entry in config/site.php.
                // Not preloaded even when active: it is only used on /ar, and
                // preloading it globally would waste bandwidth on /en.
                //
                // bunny('IBM Plex Sans Arabic', {
                //     weights: [400, 500, 600, 700],
                //     subsets: ['arabic', 'latin'],
                //     preload: false,
                //     optimizedFallbacks: true,
                // }),
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
