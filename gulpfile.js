var gulp = require('gulp');
var sass = require('gulp-sass');
var plumber = require('gulp-plumber');
var livereload = require('gulp-livereload');

var requireDir = require('require-dir');

// Require all tasks in gulp/tasks, including subfolders
requireDir('./gulp/tasks', {recurse: true});

gulp.task('default', ['watchify'], function () {
    // place code for your default task here
});
