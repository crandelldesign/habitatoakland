/*
  Gulpfile for compiling and minimizing assets
  Authors: Eric Dum, Matt Crandell
  Version: 2.0
*/

// Defining requirements
var gulp = require('gulp');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var rename = require('gulp-rename');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var imagemin = require('gulp-imagemin');
var merge = require('gulp-merge');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var browserSync = require('browser-sync').create();
var del = require('del');
var php = require('gulp-connect-php');
var cleanCSS = require('gulp-clean-css');

// Defining base pathes
// For some frameworks, you will need place them in a public folder
var basePaths = {
  node: './node_modules/',
  vendor: './assets/vendor/',

  styles: {
    src: './assets/src/sass/',
    dest: './assets/css/'
  },
  scripts: {
    src: './assets/src/js/',
    dest: './assets/js/'
  }
};

// browser-sync watched files
// automatically reloads the page when files changed
var browserSyncWatchFiles = [
  basePaths.styles.dest + '*.min.css',
  basePaths.scripts.dest + '*.min.js',
  './**/*.php'
];

// browser-sync options
// see: https://www.browsersync.io/docs/options/
var browserSyncOptions = {
  proxy: '127.0.0.1:8010',
  port: 8080,
  open: true,
  notify: false
};

// Run:
// gulp sass
// Compiles SCSS files in CSS
gulp.task('sass', ['minify-css'], function () {
    var stream = gulp.src(basePaths.styles.src + '/*.scss')
        .pipe(plumber())
        .pipe(sass())
        .pipe(gulp.dest(basePaths.styles.dest))
        .pipe(rename('custom-editor-style.css'))
    return stream;
});
gulp.task('minify-css', function() {
  return gulp.src(basePaths.styles.dest + '/theme.css')
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(plumber())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(basePaths.styles.dest));
});


// Run:
// gulp scripts.
// Uglifies and concat all JS files into one
gulp.task('scripts', function() {
  var scripts = [
    // Vendor scripts
    //basePaths.vendor + '/jquery/jquery.js',
    basePaths.vendor + '/bootstrap/assets/javascripts/bootstrap.js',
    // Site scripts
    basePaths.scripts.src + '**/*.js'
  ];
  gulp.src(scripts)
    .pipe(plumber({
      errorHandler: function (err) {
        console.log(err);
        this.emit('end');
      }
    }))
    .pipe(concat('theme.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest( basePaths.scripts.dest ));
  gulp.src(scripts)
    .pipe(plumber({
        errorHandler: function (err) {
          console.log(err);
          this.emit('end');
        }
      }))
    .pipe(concat('theme.js'))
    .pipe(gulp.dest( basePaths.scripts.dest ));
});

// Run:
// gulp watch
// Starts watcher. Watcher runs gulp less task on changes
gulp.task('watch', function () {
  gulp.watch( basePaths.styles.src + '**/*.scss', ['sass']);
  gulp.watch([ basePaths.vendor + 'js/**/*.js', basePaths.scripts.src + '**/*.js'], ['scripts' ]);

  //Inside the watch task.
  gulp.watch('./img/**', ['imagemin'])
});

// TABLED UNTIL WE FIGURE OUT USE
// Run:
// gulp imagemin
// Running image optimizing task
gulp.task('imagemin', function(){
    gulp.src('img/src/**')
    .pipe(imagemin())
    .pipe(gulp.dest('img'))
});

// Run:
// gulp php
// Starts PHP server
gulp.task('php', function() {
  php.server({ port: 8010, keepalive: true});
  // If using a framework, you may need to use the following code
  // php.server({ base: 'public', port: 8010, keepalive: true});
});

// Run:
// gulp browser-sync
// Starts browser-sync task for starting the server.
gulp.task('browser-sync',['php'], function() {
  browserSync.init(browserSyncWatchFiles, browserSyncOptions);
});

// Run:
// gulp watch-bs
// Starts watcher with browser-sync. Browser-sync reloads page automatically on your browser
gulp.task('watch-bs', ['browser-sync', 'watch', 'sass', 'scripts']);

// Run:
// gulp copy-assets.
// Copy all needed dependency assets files from bower_component assets to themes /js, /less and /fonts folder. Run this task after bower install or bower update
gulp.task('copy-assets', function() {

  // Copy all Bootstrap files
  var stream = gulp.src( basePaths.node + 'bootstrap-sass/**/*' )
    .pipe(gulp.dest( basePaths.vendor + '/bootstrap' ));

  // Copy all Font Awesome Fonts
  gulp.src(basePaths.node + 'font-awesome/fonts/**/*.{ttf,woff,woff2,eof,svg}')
    .pipe(gulp.dest( './assets/fonts/font-awesome' ));

  // Copy all Font Awesome LESS files
  gulp.src( basePaths.node + 'font-awesome/*/*' )
    .pipe(gulp.dest( basePaths.vendor + '/font-awesome' ));

  // Copy jQuery
  gulp.src( basePaths.node + 'jquery/dist/*.js' )
    .pipe(gulp.dest( basePaths.vendor + '/jquery' ));

  return stream;
});

// The default task (called when you run `gulp` from cli)
gulp.task('default', ['sass', 'scripts', 'minify-css']);
