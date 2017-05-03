/**
 * Created by Hrach on 3/31/2016.
 */
var gulp = require('gulp');
var uglify = require('gulp-uglify');
var cssmin = require('gulp-cssmin');
var rename = require('gulp-rename');

gulp.task('default', function() {
    console.log('sdsdfdf');
}),

gulp.task('compress', function() {
    return gulp.src('**/**/*.js')
        .pipe(uglify())
        .pipe(gulp.dest('js/*.min.js'));
}),

gulp.task('minify', function () {
    gulp.src('**/**/*.css')
        .pipe(cssmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('dest'));
});
