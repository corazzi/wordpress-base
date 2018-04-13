/**
 * List the JavaScript files that you want to include in here, in the order that
 * they should be included.
 */
var scripts = [
    // NPM modules
    'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js',
    'node_modules/jquery-match-height/dist/jquery.matchHeight.js',
    'node_modules/slick-carousel/slick/slick.js',
    'node_modules/lightbox2/dist/lightbox.js',

    // Other modules
    'src/js/vendor/jquery.slicknav.min.js',

    // Custom JavaScript
    'src/js/app.js',
];

/**
 * Require the different gulp dependencies
 */
var gulp         = require('gulp'),
    gulpif       = require('gulp-if'),
    args         = require('yargs').argv,
    autoprefixer = require('gulp-autoprefixer'),
    concat       = require('gulp-concat'),
    sass         = require('gulp-sass'),
    sourcemaps   = require('gulp-sourcemaps'),
    uglify       = require('gulp-uglify'),
    watch        = require('gulp-watch');

/**
 * Decide what mode we're running in
 */
var production = (args.production === undefined) ? false : true,
    output     = (production) ? 'compressed' : 'nested';

/**
 * SASS compilation
 */
gulp.task('sass', () => {
    return gulp.src('src/sass/app.scss')
        .pipe(sourcemaps.init())
            .pipe(sass({
                    outputStyle: output
                }).on('error', sass.logError))
            .pipe(autoprefixer({
                    browsers: ['> 0.5%', 'ie 9']
                }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('assets/css'));
});

/**
 * JavaScript compilation
 */
gulp.task('js', () => {
    return gulp.src(scripts)
        .pipe(sourcemaps.init())
            .pipe(concat('app.js'))
            .pipe(gulpif(production, uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('assets/js'));
});

/**
 * Watch for changes in .scss files and run the Sass compilation if any changes
 * are detected
 */
gulp.task('watch:sass', () => {
    gulp.start('sass');
    return gulp.watch('./src/sass/**/*.scss', ['sass']);
});

/**
 * Watch for changes in .js files and run the JavaScript compilation if any
 * changes are detected
 */
gulp.task('watch:js', () => {
    gulp.start('js');
    return gulp.watch('./src/js/**/*.js', ['js']);
});

/**
 * Watch for any changes with the .scss/.js files and run the appropriate
 * changes
 */
gulp.task('watch', ['watch:sass', 'watch:js']);

/**
 * Copy all of the assets that we have from our NPM modules into the assets
 * folder.
 */
gulp.task('assets', () => {
    gulp.start('assets:fonts');
    gulp.start('assets:images');
});

/**
 * Copy our font assets to the appropriate assets/fonts folder
 */
gulp.task('assets:fonts', () => {
    // Font Awesome
    gulp.src('node_modules/font-awesome/fonts/*.{eot,svg,ttf,woff,woff2,otf}')
        .pipe(gulp.dest('assets/fonts/'));

    // Slick
    gulp.src('node_modules/slick-carousel/slick/fonts/*.{eot,svg,ttf,woff}')
        .pipe(gulp.dest('assets/fonts/slick/'));
});

/**
 * Copy our image assets to the appropriate assets/images folder
 */
gulp.task('assets:images', () => {
    // Slick
    gulp.src('node_modules/slick-carousel/slick/ajax-loader.gif')
        .pipe(gulp.dest('assets/images/slick/'));

    // Lightbox
    gulp.src('node_modules/lightbox2/src/images/*.*')
        .pipe(gulp.dest('assets/images/'));
});