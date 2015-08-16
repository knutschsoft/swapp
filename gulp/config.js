var dest = "./web";
var src = './src';

module.exports = {
    browserSync: {
        proxy: 'swapp/app_dev.php'
        //,
        //server: {
            // Serve up our build folder
            //baseDir: dest
            //,
            //index: '/app.php'
        //}
    },
    twig: {
        src: [
            './app/Resources/**/views/**/*.html.twig'
        ]
    },
    sass: {
        src: [
            'app/Resources/public/css/**/*.scss'
        ],
        dest: dest + '/css',
        settings: {
            indentedSyntax: false, // Enable .sass syntax!
            imagePath: 'images' // Used by the image-url helper
        }
    },
    images: {
        src: src + "/images/**",
        dest: dest + "/images"
    },
    //iconFonts: {
    //    name: 'Gulp Starter Icons',
    //    src: src + '/icons/*.svg',
    //    dest: dest + '/fonts',
    //    sassDest: src + '/sass',
    //    template: './gulp/tasks/iconFont/template.sass.swig',
    //    sassOutputName: '_icons.sass',
    //    fontPath: 'fonts',
    //    className: 'icon',
    //    options: {
    //        fontName: 'Post-Creator-Icons',
    //        appendCodepoints: true,
    //        normalize: false
    //    }
    //},
    browserify: {
        // A separate bundle will be generated for each
        // bundle config in the list below
        bundleConfigs: [{
            entries: 'app/Resources/public/js/bundle.js',
            dest: dest + '/js',
            outputName: 'global.js',
            // Additional file extentions to make optional '.coffee', '.hbs'
            extensions: [],
            // list of modules to make require-able externally
            require: ['jquery', '$']
        }
        //    , {
        //    entries: 'app/Resources/public/js/main.js',
        //    dest: dest + '/js',
        //    outputName: 'page.js',
        //    // list of externally available modules to exclude from the bundle
        //    external: ['jquery']
        //}
        ]
    },
    production: {
        cssSrc: dest + '/*.css',
        jsSrc: dest + '/*.js',
        dest: dest
    }
};
