let assets_dir = './assets';
const path = require('path');

let Encore = require('@symfony/webpack-encore');
const WorkboxPlugin = require('workbox-webpack-plugin');

Encore
    // directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath(!Encore.isDevServer() ? '/build' : 'https://swapp.local:8874')

    // .setOutputPath()

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    .copyFiles(
        {
            from: './assets/images/icons',
            to: 'images/icons/[path][name].[ext]',
            pattern: /\.(png|jpg|jpeg|xml|svg|ico)$/,
        },
    )

    .configureManifestPlugin(options => {
        // options.fileName = 'webpack-manifest.json';
        options.seed = {
            'name': 'Swapp - Die Streetworkapp',
            'short_name': 'Swapp',
            'display': 'standalone',
            'display_overrides': ['tabbed', 'fullscreen'],
            'description': 'Swapp für Fachkräfte der Streetwork/Mobilen Jugendarbeit.\nDokumentation und Reflexion für unterwegs.\nsince 2015',
            'start_url': '../',
            'lang': 'de',
            'dir': 'ltr',
            'id': '/',
            'background_color': '#fff',
            'theme_color': '#1c97b0',
            'icons': [
                {
                    "src": "/build/images/icons/android-chrome-72x72.png",
                    "sizes": "72x72",
                    "type": "image/png",
                    "purpose": "maskable"
                }
            ],
        };
    })

    // will output as web/build/app.js
    .addEntry('app', assets_dir + '/js/app.js')

    .addAliases(
        {
            'api': path.resolve(__dirname, 'assets/js/api'),
            'components': path.resolve(__dirname, 'assets/js/components'),
            'pages': path.resolve(__dirname, 'assets/js/pages'),
            'router': path.resolve(__dirname, 'assets/js/router'),
            'store': path.resolve(__dirname, 'assets/js/store'),
            'css': path.resolve(__dirname, 'assets/css'),
        },
    )

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    // .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    .setManifestKeyPrefix('build/')

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enable vue-load
    .enableVueLoader(() => {}, { runtimeCompilerBuild: false })

    .enablePostCssLoader()
    .enableEslintPlugin()

    .configureDevServerOptions(options => {
        options.allowedHosts = 'all';
        options.server = {
            type: 'https',
            options: {
                key: '/var/www/certs/swapp.local.key',
                cert: '/var/www/certs/swapp.local.crt',
            },
        };
        options.host = '0.0.0.0';
        options.port = '8874';
    })
;

if (!Encore.isDevServer()) {
    Encore.addPlugin(
        // new WorkboxPlugin.InjectManifest({
        new WorkboxPlugin.GenerateSW({
            // swSrc: "./assets/js/service-worker.js",
            swDest: '../service-worker.js',
            maximumFileSizeToCacheInBytes: 50000000,
        })
    );
}

module.exports = Encore.getWebpackConfig();
