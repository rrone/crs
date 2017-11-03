const path   = require('path');
const gulp   = require('gulp');
const concat = require('gulp-concat');

const appResourceDir   = path.join(__dirname, 'src/resources/public');
const nodeModulesDir = path.join(__dirname,'node_modules');
const vendorDir = path.join(__dirname,'vendor');
const appWebDir   = path.join(__dirname, 'public');

const appTask = function() {

    // Control the order
    gulp.src([
            appResourceDir + '/css/style.css',
            appResourceDir + '/css/refsched.css'
        ])
        .pipe(concat("app.css"))
        .pipe(gulp.dest('public/css'));

     //Java scripts
    gulp.src([
            appResourceDir + '/js/app.js'
        ])
        .pipe(concat("ext.js"))
        .pipe(gulp.dest(appWebDir +'/js'));
        
    // images
    gulp.src([
            appResourceDir + '/images/*.png',
            appResourceDir + '/images/*.ico'
            
        ])
        .pipe(gulp.dest(appWebDir +'/images'));
};
gulp.task('app',appTask);

const nodeModulesTask = function() {

    gulp.src([
            path.join(nodeModulesDir,'normalize.css/normalize.css'),
            path.join(nodeModulesDir,'bootstrap/dist/css/bootstrap.min.css'),
            path.join(nodeModulesDir,'purecss/build/base-min.css'),
            path.join(nodeModulesDir,'purecss/build/grids-responsive-min.css'),
            path.join(nodeModulesDir,'purecss/build/buttons-min.css'),
            path.join(nodeModulesDir,'purecss/build/pure-nr-min.css'),
            path.join(nodeModulesDir,'jquery-datetimepicker/build/jquery.datetimepicker.min.css')
        ])
        .pipe(gulp.dest(appWebDir + '/css'));
    //
    gulp.src([
            path.join(nodeModulesDir,'jquery/dist/jquery.min.js'),
            path.join(nodeModulesDir,'bootstrap/dist/js/bootstrap.min.js'),
            path.join(nodeModulesDir,'jquery-datetimepicker/build/jquery.datetimepicker.full.js')
        ])
        .pipe(gulp.dest(appWebDir +'/js'));
    //
    gulp.src([
        path.join(vendorDir,'components/bootstrap/fonts/glyphicons-halflings-regular.ttf'),
        path.join(vendorDir,'components/bootstrap/fonts/glyphicons-halflings-regular.woff'),
        path.join(vendorDir,'components/bootstrap/fonts/glyphicons-halflings-regular.woff2')
    ])
        .pipe(gulp.dest(appWebDir +'/fonts'));

};
gulp.task('node_modules',nodeModulesTask);

const buildTask = function()
{
    appTask();
    nodeModulesTask();
};
gulp.task('build',buildTask);

const watchTask = function()
{
    buildTask();

    // Why the warnings, seems to work fine
    gulp.watch([
        appResourceDir + '/css/*.css',
        appResourceDir + '/js/*.js',
        appResourceDir + '/images/*.png',
        appResourceDir + '/images/*.ico'
    ],  ['app']);
};
gulp.task('watch',watchTask);

// The default task (called when you run `gulp` from cli)
gulp.task('default', ['build']);
