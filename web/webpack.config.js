let assets_dir = './assets';
const path = require('path');

let Encore = require('@symfony/webpack-encore');

Encore
// directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath(Encore.isProduction() ? '/build' : 'https://swapp.local:8080/build/')

    // .setOutputPath()

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

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
        }
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
    .enableEslintLoader()

    .configureDevServerOptions(options => {
        options.client.host = 'swapp.local';
        options.firewall = false;
        options.https = {
            key: '/var/www/certs/swapp.local.key',
            cert: '/var/www/certs/swapp.local.crt',
        };
        options.host = '0.0.0.0';
        options.port = '8080';
    })
;

module.exports = Encore.getWebpackConfig();
