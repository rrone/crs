/*jslint node: true */
"use strict";

let gulp = require("gulp");
let {src, dest, series, parallel} = gulp;
let concat = require("gulp-concat");
let minifyCSS = require("gulp-csso");
let path = require("path");
let del = require("del");
let uglify = require("gulp-uglify");
let fs = require("fs-extra");

let appResourceDir = path.join(__dirname, "src/Resources/public");
let nodeModulesDir = path.join(__dirname, "node_modules");
let appWebDir = path.join(__dirname, "public");

// Set the browser that you want to support
function css() {
    // Control the order
    return src([
        appResourceDir + "/css/style.css",
        appResourceDir + "/css/crs.css"
    ]).pipe(
        concat("app.css")
    ).pipe(
        minifyCSS()
    ).pipe(dest(appWebDir + "/css"));
}

function js() {
    //Java scripts
    return src([
        appResourceDir + "/js/jquery.filedownload.js"
    ]).pipe(
        concat("filedownload.js")
    ).pipe(
        uglify()
    ).pipe(dest(appWebDir + "/js"));
}

function image() {
    // images
    return src([
        appResourceDir + "/images/*.png",
        appResourceDir + "/images/*.ico",
        appResourceDir + "/images/*.gif"

    ]).pipe(dest(appWebDir + "/images"));
}

function node() {
    //vendor assets
    src([
        path.join(nodeModulesDir, "normalize.css/normalize.css"),
        path.join(nodeModulesDir, "purecss/build/base-min.css"),
        path.join(nodeModulesDir, "purecss/build/grids-responsive-min.css"),
        path.join(nodeModulesDir, "purecss/build/buttons-min.css"),
        path.join(nodeModulesDir, "purecss/build/pure-nr-min.css")
    ]).pipe(dest(appWebDir + "/css"));

    return src([
        path.join(nodeModulesDir, "jquery/dist/jquery.min.js")
    ]).pipe(dest(appWebDir + "/js"));

}

function cleanPath(path) {
    del(path);
    return fs.ensureDir(path);
}

function clean() {
    cleanPath(appWebDir + "/css/*");
    cleanPath(appWebDir + "/js/*");
    return cleanPath(appWebDir + "/images/*");
}

function build(done) {
    series(clean, parallel(css, js, image, node))(done);
}

function watch() {
    gulp.watch(appResourceDir + "/css/**/*.css", css);
    gulp.watch(appResourceDir + "/js/**/*.js", js);
    gulp.watch(appResourceDir + "/images/**/*.(?:ico|png)$", image);
}

// The default task (called when you run `gulp` from cli)
exports.clean = clean;
exports.build = build;
exports.watch = series(build, watch);
exports.default = build;