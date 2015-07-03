var gulp = require('gulp');
var sass = require('gulp-sass');
var plumber = require('gulp-plumber');
var livereload = require('gulp-livereload');

gulp.task('default', function() {
    // place code for your default task here
});

gulp.task('watch', function () {
    var onChange = function (event) {
        console.log('File ' + event.path + ' has been ' + event.type);
        // Tell LiveReload to reload the window
        //livereload.changed(event.path);
    };
    // Starts the server
    livereload.listen();

    gulp.watch('./app/Resources/public/css/*.scss', ['sass'])
        .on('change', onChange);

    gulp.watch('./app/Resources/public/js/*.js', ['js'])
        .on('change', onChange);

    gulp.watch('./app/Resources/views/**/**.html.twig', ['twig'])
        .on('change', onChange);
});

gulp.task('sass', function() {
    gulp.src('app/Resources/public/css/main.scss')
        .pipe(plumber())
        .pipe(sass())
        .pipe(gulp.dest('web/css'))
        .pipe(livereload());
});

gulp.task('js', function() {
    console.log('js called');
});

gulp.task('twig', function(event) {
    console.log('twig');
    gulp.src('./app/Resources/views/**/**.html.twig')
        .pipe(livereload.reload());
});
