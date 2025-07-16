const gulp = require('gulp');
const {series} = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const purgecss = require('gulp-purgecss');
const cleancss = require("gulp-clean-css");
const rename = require('gulp-rename');
const through2 = require('through2');

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

// function gdscomponents() {
//   return gulp.src('node_modules/govuk-frontend/dist/govuk/components' + '/**/*', {base: 'node_modules/govuk-frontend/dist/govuk/components'})
//       .pipe(gulp.dest('assets/components'));
// };
// gulp.task('gdscomponents')
//
// function gdsmacros() {
//   return gulp.src('node_modules/govuk-frontend/dist/govuk/macros' + '/**/*', {base: 'node_modules/govuk-frontend/dist/govuk/macros'})
//       .pipe(gulp.dest('assets/macros'));
// };
// // gulp.task('gdsmacros')


// Simple Nunjucks → Twig converter function
function convertNunjucksToTwig(content) {
    return content

        // Inline IF → ternary (with hyphen-aware tags)
        .replace(
            /{%-?\s*if\s+([^{%]+?)\s*-?%}([\s\S]*?){%-?\s*endif\s*-?%}/g,
            (match, condition, body) => {
                const cleanBody = body.trim();
                if (/{[%{]/.test(cleanBody)) return match; // skip if nested logic
                return `{{ ${condition.trim()} ? '${cleanBody}' : '' }}`; // ✅ no 'if' keyword!
            }
        )
        // Convert Nunjucks-style inline if in set statements to Twig ternary
        .replace(/(set\s+\w+\s*=\s*|{{\s*)(.+?)\s+if\s+(.+?)\s+else\s+(.+?)(\s*}}|\s*%})/g,
            (match, prefix, valIfTrue, condition, valIfFalse, suffix) => {
                return `${prefix}${condition.trim()} ? ${valIfTrue.trim()} : ${valIfFalse.trim()}${suffix}`;
            }
        )

        .replace(/\|\s*indent\((\d+)\)/g, (match, n) => {
            const spaces = ' '.repeat(parseInt(n, 10));
            return `|replace({'\\n': '\\n${spaces}'})|raw ~ '${spaces}'`;
        })


        // Block tags
        .replace(/{%-?\s*block\s+(\w+)\s*-?%}/g, '{% block $1 %}')
        .replace(/{%-?\s*endblock\s*-?%}/g, '{% endblock %}')

        // Extends
        .replace(/{%-?\s*extends\s+["'](.+?)\.njk["']\s*-?%}/g, '{% extends "$1.twig" %}')

        // Include
        .replace(/{%-?\s*include\s+["'](.+?)\.njk["']\s*-?%}/g, '{% include "$1.twig" %}')

        // Import
        .replace(/{%-?\s*import\s+["'](.+?)\.njk["']\s+as\s+(\w+)\s*-?%}/g, '{% import "$1.twig" as $2 %}')

        // From-import
        .replace(/{%-?\s*from\s+["'](.+?)\.njk["']\s+import\s+([\w\s,]+)\s*-?%}/g, '{% from "$1.twig" import $2 %}')

        // Macro
        .replace(/{%-?\s*macro\s+(\w+)\((.*?)\)\s*-?%}/g, '{% macro $1($2) %}')
        .replace(/{%-?\s*endmacro\s*-?%}/g, '{% endmacro %}')

        // Set
        .replace(/{%-?\s*set\s+(\w+)\s*=\s*(.*?)\s*-?%}/g, '{% set $1 = $2 %}')

        // Filters
        .replace(/\|\s*safe/g, '|raw')

        // Output tags (preserve {{- and -}})
        .replace(/{{-?\s*(.*?)\s*-?}}/g, '{{ $1 }}')

        .replace(/\belif\b/g, 'elseif')
        .replace(
            /{%-?\s*(if|elseif)\s+(.*?)=+(.*?)\s*-?%}/g,
            (match, keyword, left, right) => `{% ${keyword} ${left.trim()} == ${right.trim()} %}`
        )
        .replace(
            /{%-?\s*from\s+["'](.+?\/)?([^\/"']+\.twig)["']\s+import\s+(.*?)\s*-?%}/g,
            (match, _path, filename, imports) => `{% from "/${filename}" import ${imports.trim()} %}`
        );


}

function gdscomponents() {
    return gulp.src('node_modules/govuk-frontend/dist/govuk/components/**/template.njk')
        .pipe(
            through2.obj(function (file, _, cb) {
                if (file.isBuffer()) {
                    const original = file.contents.toString();
                    const converted = convertNunjucksToTwig(original);
                    file.contents = Buffer.from(converted);
                }
                cb(null, file);
            })
        )
        .pipe(rename({extname: '.twig'}))
        .pipe(gulp.dest('assets/components'));
}

function gdsmacros() {
    return gulp.src('node_modules/govuk-frontend/dist/govuk/macros/*.njk')
        .pipe(
            through2.obj(function (file, _, cb) {
                if (file.isBuffer()) {
                    const original = file.contents.toString();
                    const converted = convertNunjucksToTwig(original);
                    file.contents = Buffer.from(converted);
                }
                cb(null, file);
            })
        )
        .pipe(rename({extname: '.twig'}))
        .pipe(gulp.dest('assets/components'));
}

//build

exports.build = series(buildsass, /*unusedcss,*/ minifycss, gdsprecompiledAssets, gdsprecompiledjs, gdscomponents, gdsmacros);