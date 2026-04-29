import {fileURLToPath, URL} from 'node:url'

import {defineConfig} from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'
import VueDevTools from 'vite-plugin-vue-devtools'
import fs from 'fs'
import vue from '@vitejs/plugin-vue'
import {VitePWA} from 'vite-plugin-pwa'
import { viteStaticCopy } from 'vite-plugin-static-copy'
import vuetify from 'vite-plugin-vuetify'

import Components from 'unplugin-vue-components/vite'

const VIRTUAL_HOST = process.env.VIRTUAL_HOST
const CERTS_FILENAME = 'swapp.local'
const isProduction = 'production' === process.env.NODE_ENV
const isTest = 'test' === process.env.NODE_ENV

export default defineConfig({
    plugins: [
        vue(),
        vuetify(),
        // VueDevTools-Browser-Inspector ist ein Dev-Tool und hat im Prod-Build nichts
        // verloren - falsy Werte werden von Vite automatisch aus der Plugin-Liste gefiltert.
        !isProduction && VueDevTools(),
        viteStaticCopy({
            targets: [
                {
                    src: 'assets/images/icons/*',
                    dest: 'images/icons',
                }
            ]
        }),
        Components({
            dts: 'assets/js/components.d.ts',
            dirs: 'assets/js/components',
            directoryAsNamespace: true,
        }),
        VitePWA({
            registerType: 'prompt',
            devOptions: {
                type: 'module',
                navigateFallback: '/',
                enabled: false
            },
            workbox: {
                maximumFileSizeToCacheInBytes: 5000000
            },
            strategies: 'generateSW',
            includeAssets: ['favicon.ico', 'apple-touch-icon.png', 'mask-icon.svg'],
            manifest: {
                'name': 'Swapp - Die Streetworkapp',
                'short_name': 'Swapp',
                'display': 'standalone',
                'display_overrides': ['tabbed', 'fullscreen'],
                'description': 'Swapp für Fachkräfte der Streetwork/Mobilen Jugendarbeit.\nDokumentation und Reflexion für unterwegs.\nsince 2015',
                'start_url': '/',
                'lang': 'de',
                'dir': 'ltr',
                'id': '/',
                scope: '/',
                'background_color': '#fff',
                theme_color: '#b2b3b5',
                'icons': [
                    {
                        "src": "/build/images/icons/android-chrome-72x72.png",
                        "sizes": "72x72",
                        "type": "image/png",
                        "purpose": "maskable"
                    }
                ],
            },
        }),
        symfonyPlugin({
            // as we set `server.host` to 0.0.0.0
            // we must explicitly set the server host name
            viteDevServerHostname: VIRTUAL_HOST
        })
    ],
    server:
        isProduction || isTest
            ? {}
            : {
                host: '0.0.0.0',
                https: {
                    key: fs.readFileSync(`/var/www/certs/${CERTS_FILENAME}.key`),
                    cert: fs.readFileSync(`/var/www/certs/${CERTS_FILENAME}.crt`)
                },
                port: 8899,
                hmr: {
                    clientPort: 8899,
                },
                origin: 'https://'+VIRTUAL_HOST+':8899',
                cors: true
            },
    build: {
        outDir: 'public/build',
        manifest: true,
        // In Dev/Test sourcemap fürs Debugging, in Prod nicht: vorher wurde eine 9-MB-Map
        // in public/build ausgeliefert. Falls später Error-Tracking (z.B. Sentry) angebunden
        // werden soll, auf 'hidden' wechseln - Map wird dann generiert, aber nicht via
        // Comment im JS referenziert.
        sourcemap: !isProduction,
        rollupOptions: {
            input: {
                app: './assets/js/app.ts'
            },
            output: {
                // Vendor-Code in eigene Chunks ziehen, damit er zwischen Releases gecached
                // bleibt (Folge-Visits laden nur den schmalen App-Chunk neu, statt den
                // kompletten 4-MB-Bundle). Funktions-Form deckt auch Sub-Pfade wie
                // `dayjs/plugin/calendar` zuverlaessig ab.
                manualChunks(id) {
                    if (!id.includes('node_modules')) {
                        return
                    }
                    if (/[\\/]node_modules[\\/](vue|vue-router|pinia|@vue)[\\/]/.test(id)) {
                        return 'vue-core'
                    }
                    if (/[\\/]node_modules[\\/]vuetify[\\/]/.test(id)) {
                        return 'vuetify'
                    }
                    if (/[\\/]node_modules[\\/](dayjs|@vueuse|axios|vue-axios|deepmerge)[\\/]/.test(id)) {
                        return 'vendor-utils'
                    }
                },
            },
        }
    },
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./assets', import.meta.url))
        }
    }
})
