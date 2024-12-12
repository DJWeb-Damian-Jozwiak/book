import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [vue({
        script: {
            defineModel: true,
            propsDestructure: true
        }
    })],
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            input: 'resources/js/app.js'
        },
        emptyOutDir: true
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js')
        }
    },
    publicDir: false,        // Wyłącz kopiowanie plików z public
    server: {
        hmr: {
            host: 'localhost'
        }
    }
});