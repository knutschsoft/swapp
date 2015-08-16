var gulp         = require('gulp');
var browserSync  = require('browser-sync');
var handleErrors = require('../util/handleErrors');
var config       = require('../config').twig;

gulp.task('twig', function () {
    return gulp.src(config.src)
        .on('error', handleErrors)
        .pipe(browserSync.reload({stream:true}));
});
