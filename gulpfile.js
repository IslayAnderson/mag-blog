const gulp = require('gulp');
const { series } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const purgecss = require('gulp-purgecss');
const cleancss = require("gulp-clean-css");

//compile 
function buildsass() {
  return gulp.src('assets/sass/main.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('assets/css'));
};
gulp.task('buildsass')

//remove unused styles
function unusedcss() {
  return gulp.src('assets/css/*.css')
    .pipe(purgecss({
      content: ['*.php']
    }))
    .pipe(gulp.dest('assets/css/'))
};
gulp.task('unusedcss');

//minify styles
function minifycss() {
  return (
    gulp
      .src("assets/css/*.css")
      .pipe(cleancss())
      .pipe(gulp.dest("assets/css/"))
  );
};
gulp.task('minifycss');

//move precompiled files from gds
function gdsprecompiledAssets() {
  return gulp.src('node_modules/govuk-frontend/dist/govuk/assets' + '/**/*', {base: 'node_modules/govuk-frontend/dist/govuk/assets'})
    .pipe(gulp.dest('assets'));
};
gulp.task('gdsprecompiledAssets')

function gdsprecompiledjs() {
  return gulp.src('node_modules/govuk-frontend/dist/govuk' + '/**/*.js', {base: 'node_modules/govuk-frontend/dist/govuk'})
    .pipe(gulp.dest('assets/js'));
};
gulp.task('gdsprecompiledjs')

//build

exports.build = series(buildsass, /*unusedcss,*/ minifycss, gdsprecompiledAssets, gdsprecompiledjs);