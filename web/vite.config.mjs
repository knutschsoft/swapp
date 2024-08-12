import {fileURLToPath, URL} from 'node:url'

import {defineConfig} from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'
import VueDevTools from 'vite-plugin-vue-devtools'
import fs from 'fs'
import vue from '@vitejs/plugin-vue2'
import {VitePWA} from 'vite-plugin-pwa'
import { viteStaticCopy } from 'vite-plugin-static-copy'

// vuetify2 support
import Components from 'unplugin-vue-components/vite'
import { VuetifyResolver } from 'unplugin-vue-components/resolvers'

const VIRTUAL_HOST = process.env.VIRTUAL_HOST
const CERTS_FILENAME = 'swapp.local'
const isProduction = 'production' === process.env.NODE_ENV
const isTest = 'test' === process.env.NODE_ENV

export default defineConfig({
    plugins: [
        vue(),
        VueDevTools(),
        viteStaticCopy({
            targets: [
                {
                    src: 'assets/images/icons/*',
                    dest: 'images/icons',
                }
            ]
        }),
        Components({
            resolvers: [VuetifyResolver()],
        }),
        VitePWA({
            registerType: 'prompt',
            devOptions: {
                type: 'module',
                navigateFallback: '/',
                enabled: true
            },
            strategies: 'generateSW',
            includeAssets: ['favicon.ico', 'apple-touch-icon.png', 'mask-icon.svg'],
            manifest: {
                'name': 'Swapp - Die Streetworkapp',
                'short_name': 'Swapp',
                'display': 'standalone',
                'display_overrides': ['tabbed', 'fullscreen'],
                'description': 'Swapp für Fachkräfte der Streetwork/Mobilen Jugendarbeit.\nDokumentation und Reflexion für unterwegs.\nsince 2015',
                'start_url': '/offline',
                'lang': 'de',
                'dir': 'ltr',
                'id': '/',
                'background_color': '#fff',
                'theme_color': '#b2b3b5',
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
                port: 8874
            },
    build: {
        manifest: true,
        sourcemap: true,
        rollupOptions: {
            input: {
                app: './assets/js/app.js'
            }
        }
    },
    resolve: {
        alias: {
            '@js': fileURLToPath(new URL('./assets/js', import.meta.url)),
            '@css': fileURLToPath(new URL('./assets/css', import.meta.url))
        }
    }
})
