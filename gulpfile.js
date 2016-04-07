var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');

gulp.task('sass', function() {
	return gulp.src('library/sass/style.scss')
        .pipe(sourcemaps.init())
         .pipe(sass({
             outputStyle: 'compressed',
             includePaths: [
                 'node_modules/bootstrap-sass/assets/stylesheets/',
                'node_modules/font-awesome/scss/'
             ]
         }) .on('error', sass.logError))
        .pipe(sourcemaps.write())
         .pipe(gulp.dest('library/css/')); 
});

gulp.task('editor-sass', function() {
    return gulp.src('library/sass/editor-style.scss')
        .pipe(sourcemaps.init())
         .pipe(sass({
             outputStyle: 'compressed'
         }) .on('error', sass.logError))
        .pipe(sourcemaps.write())
         .pipe(gulp.dest('library/css/')); 
});

gulp.task('ie-sass', function() {
    return gulp.src('library/sass/ie.scss')
        .pipe(sourcemaps.init())
         .pipe(sass({
             outputStyle: 'compressed'
         }) .on('error', sass.logError))
        .pipe(sourcemaps.write())
         .pipe(gulp.dest('library/css/')); 
});

gulp.task('login-sass', function() {
    return gulp.src('library/sass/login.scss')
        .pipe(sourcemaps.init())
         .pipe(sass({
             outputStyle: 'compressed'
         }) .on('error', sass.logError))
        .pipe(sourcemaps.write())
         .pipe(gulp.dest('library/css/')); 
});

gulp.task('js', function(){
    return gulp.src(['library/js/jquery-1.12.2.js', 'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js'])
        .pipe(concat('scripts.js'))
        .pipe(uglify())
        .pipe(gulp.dest('library/js'));
});

// Copy Font Awesome Fonts
gulp.task('copy-font-awesome', function () {
    return gulp
        .src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest('library/fonts'));
});

// Copy Bootstrap Fonts
gulp.task('copy-bootstrap', function () {
    return gulp
        .src('node_modules/bootstrap-sass/assets/fonts/bootstrap/*')
        .pipe(gulp.dest('library/fonts/bootstrap'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('library/sass/*.scss', ['sass']);
    gulp.watch('library/sass/**/.scss', ['sass']);
    gulp.watch('library/js/*.js', ['js']);
});

gulp.task('default', ['sass', 'editor-sass', 'ie-sass', 'login-sass', 'js', 'copy-font-awesome', 'copy-bootstrap'], function(){});