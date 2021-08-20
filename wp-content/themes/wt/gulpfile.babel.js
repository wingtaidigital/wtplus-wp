'use strict';

import plugins  from 'gulp-load-plugins';
import yargs    from 'yargs';
import browser  from 'browser-sync';
import gulp     from 'gulp';
import yaml     from 'js-yaml';
import fs       from 'fs';

// Load all Gulp plugins into one variable
const $ = plugins();

// Check for --production flag
const PRODUCTION = !!(yargs.argv.production);

// Load settings from settings.yml
const { COMPATIBILITY, UNCSS_OPTIONS, PORT, PATHS } = loadConfig();

function loadConfig() {
	let ymlFile = fs.readFileSync('config.yml', 'utf8');
	return yaml.load(ymlFile);
}

// Build the "dist" folder by running all of the below tasks
gulp.task('build',
	gulp.series(gulp.parallel(sass, javascript, images)));

// Build the site, run the server, and watch for file changes
gulp.task('default',
	gulp.series('build', server, watch));

gulp.task('dev',
	gulp.series(gulp.parallel(sass, javascript, images)));

gulp.task('css',
	gulp.series(sass));

gulp.task('js',
	gulp.series(javascript));

gulp.task('img',
	gulp.series(images));

gulp.task('acf',
	gulp.series(gulp.parallel(acfSass, acfJavascript)));

// Compile Sass into CSS
// In production, the CSS is compressed
function sass() {
	return gulp.src('src/assets/scss/style.scss')
	.pipe($.sourcemaps.init())
	.pipe($.sass({
		includePaths: PATHS.sass
	})
	.on('error', $.sass.logError))
	.pipe($.autoprefixer({
		browsers: COMPATIBILITY
	}))
	// Comment in the pipe below to run UnCSS in production
	.pipe($.if(PRODUCTION, $.uncss(UNCSS_OPTIONS)))
	.pipe($.if(PRODUCTION, $.cssnano({zindex: false})))
	.pipe($.if(!PRODUCTION, $.sourcemaps.write()))
	.pipe(gulp.dest(PATHS.dist))
	.pipe(browser.reload({ stream: true }));
}

// Combine JavaScript into one file
// In production, the file is minified
function javascript() {
	return gulp.src(PATHS.javascript)
	.pipe($.sourcemaps.init())
	.pipe($.babel({ignore: ['what-input.js']}))
	.pipe($.concat('app.js'))
	.pipe($.if(PRODUCTION, $.uglify()
		.on('error', e => { console.log(e); })
	))
	.pipe($.if(!PRODUCTION, $.sourcemaps.write()))
	.pipe(gulp.dest(PATHS.dist + '/assets/js'));
}

// Copy images to the "dist" folder
// In production, the images are compressed
function images() {
	return gulp.src('src/assets/img/**/*')
	.pipe($.if(PRODUCTION, $.imagemin({
		progressive: true
	})))
	.pipe(gulp.dest(PATHS.dist + '/assets/img'));
}

// Start a server with BrowserSync to preview the site in
function server(done) {
	browser.init({
		proxy: "localhost",
		port: PORT
	});
	done();
}

// Reload the browser with BrowserSync
function reload(done) {
	browser.reload();
	done();
}

// Watch for changes to static assets, pages, Sass, and JavaScript
function watch() {
	gulp.watch('src/assets/scss/**/*.scss').on('all', sass);
	gulp.watch('src/assets/js/**/*.js').on('all', gulp.series(javascript, browser.reload));
	gulp.watch('src/assets/img/**/*').on('all', gulp.series(images, browser.reload));
	gulp.watch('**/*.php').on('all', gulp.series(browser.reload));
}

function acfSass()
{
	return gulp.src('src/acf/scss/acf.scss')
	.pipe($.sourcemaps.init())
	.pipe($.sass({
		includePaths: 'src/acf/scss/'
	})
	.on('error', $.sass.logError))
	.pipe($.autoprefixer({
		browsers: COMPATIBILITY
	}))
	.pipe($.if(PRODUCTION, $.cssnano({zindex: false})))
	.pipe($.if(!PRODUCTION, $.sourcemaps.write()))
	.pipe(gulp.dest('assets/css'));
}

function acfJavascript() {
	return gulp.src('src/acf/js/acf.js')
	.pipe($.sourcemaps.init())
	.pipe($.babel())
	.pipe($.concat('acf.js'))
	.pipe($.if(PRODUCTION, $.uglify()
		.on('error', e => { console.log(e); })
	))
	.pipe($.if(!PRODUCTION, $.sourcemaps.write()))
	.pipe(gulp.dest('assets/js'));
}
