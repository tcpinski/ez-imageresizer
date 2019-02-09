/**
 * @author Jordan Pinski
 * @summary Builds SCSS and JS and distributes both to their respective
 * directories in dist/
 * 
 * 02/08/2019
 */

// Imports
const { src, dest, series, task, watch } = require('gulp');
const babel = require('gulp-babel');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const browsersync = require('browser-sync');

// Settings
const names = {
  dest: {
    scss: 'styles.css',
    js: 'scripts.js'
  }
}

// Paths
const paths = {
  src: {
    scss: 'src/scss/**/*.scss',   // The path to all scss files. This is only used for watching.
    js: 'src/js/**/*.js',            // The path to all javascript files. This is only used for watching.
  },
  dest: {
    scss: 'dist/css',
    js: 'dist/js'
  }
};

// Tasks
task('scss', scss);

function scss() {
  return src(paths.src.scss)
    .pipe(rename({ extname: '.min.css' }))
    .pipe(dest(paths.dest.scss));
}

task('js', js);

function js() {
  return src(paths.src.js)
    .pipe(sourcemaps.init())
    .pipe(babel())
    .pipe(uglify())
    .pipe(concat(names.dest.js))
    .pipe(rename({ extname: '.min.js' }))
    .pipe(sourcemaps.write('/'))
    .pipe(dest(paths.dest.js));
}

task('watch-browsersync', watch_browsersync);

function watch_browsersync() {
  browsersync.init({
    server: './dist/'
  });

  // Watchers
  watch(paths.src.scss, scss);
  watch(paths.src.js, js);
  watch('./dist').on('change', browsersync.reload);
  watch('./src').on('change', browsersync.reload);
}

// Exports
exports.watch = watch;
exports.scss = scss;
exports.js = js;
exports.default = series(scss, js);