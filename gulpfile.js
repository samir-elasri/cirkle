/* eslint-disable */
// noinspection JSIgnoredPromiseFromCall

const gulp = require('gulp');
const fs = require('fs');
const browserify = require('browserify');
const watchify = require('watchify');
const babelify = require('babelify');
const sass = require('gulp-sass')(require('sass'));
const browserSync = require('browser-sync').create();
const rename = require("gulp-rename");
const uglify = require('gulp-uglify');
const plumber = require('gulp-plumber');
const source = require('vinyl-source-stream');
const autoprefixer = require('gulp-autoprefixer');
const streamify = require('gulp-streamify');
const buffer = require('vinyl-buffer');
const sourcemaps = require('gulp-sourcemaps');
const os = require("os");
const fomanticBuildJs = require('./resources/semantic/tasks/build/javascript');
const fomanticBuildCss = require('./resources/semantic/tasks/build/css');
const fomanticBuildAssets = require('./resources/semantic/tasks/build/css');
const directoryExists = require('directory-exists');
const workboxBuild = require('workbox-build');
const {
	series,
	src,
	dest,
	watch
} = require('gulp');


// Web Site

const cfg = {
	src: {
		dir: "./resources/",
		jsDir: "./resources/js/",
		sassDir: "./resources/sass/",
		bladeFiles: './resources/views/**/*.blade.php',
		jsMainFile: {
			name: "main.js",
			path: "./resources/js/main.js",
		},
		fomanticDir: './public_html/dist/compiled/semantic'
	},
	dist: {
		dir: "./public_html/dist/compiled/",
		jsDir: "./public_html/dist/compiled/",
		cssDir: "./public_html/dist/compiled/",
		jsMainFile: {
			name: "main.min.js",
			path: "./public_html/dist/compiled/main.min.js"
		}
	}
};

// Log
console.log("hostname: [" + os.hostname() + "]");

/**
 * Plumber/Errors catch function
 * @param err
 */
const handleErrors = function (err) {
	const plugin = (err.plugin) ? "[" + err.plugin.toString() + "] " : '';
	console.log(plugin + err.message.toString());
	this.emit('end');
};

/**
 *
 * @param cb
 * @returns {*}
 */
const validateFomantic = (cb) => {
	if (!directoryExists.sync(cfg.src.fomanticDir)) {
		return series('fomanticCss', 'fomanticJs', 'fomanticAssets')(cb);
	}

	return cb();
};

/**
 *
 * @returns {*}
 */
const js = (cb = () => {}) => {
	browserify(cfg.src.jsMainFile.path)
		.transform(babelify, {
			global: false,
			presets: ["@babel/preset-env", "@babel/preset-react"]
		})
		.bundle()
		.on('error', handleErrors)
		.pipe(source(cfg.src.jsMainFile.name))
		.pipe(buffer())
		.pipe(sourcemaps.init())
		.pipe(sourcemaps.write('./'))
		.pipe(dest(cfg.dist.jsDir));

	cb();
};

/**
 *
 * @param cb
 * @returns {Promise<void>}
 */
const bustCache = (cb) => {
	const version = Date.now();
	fs.writeFile('cache-time.txt', version.toString(), cb);
};

/**
 *
 * @param cb
 * @param enableBS
 */
const watchifyJs = (cb = () => {}, enableBS = true) => {
	if (enableBS) {
		browserSync.init({
			files: ['./public_html/dist/compiled'],
			ghostMode: false,
		});
	}

	let bundler = watchify(browserify({
		entries: cfg.src.jsMainFile.path,
		cache: {},
		packageCache: {},
	})).transform(babelify, {
		global: false,
		presets: ["@babel/preset-env", "@babel/preset-react"]
	});

	const updateJs = (ids = null) => {
		if (ids) {
			const path = ids.join(', ');
			console.log(path + " changed");
		}

		bustCache(() => {
			bundler.bundle()
				.on('error', handleErrors)
				.pipe(source(cfg.src.jsMainFile.name))
				.pipe(buffer())
				.pipe(sourcemaps.init())
				.pipe(sourcemaps.write('./'))
				.pipe(dest(cfg.dist.jsDir));
		});
	};

	bundler.on('update', updateJs);

	updateJs();

	cb();
};

const css = (cb = () => {}) => {
	src(cfg.src.sassDir + '/**/*.scss')
		.pipe(plumber({
			errorHandler: handleErrors
		}))
		.pipe(sass({
			outputStyle: 'compressed'
		}).on('error', sass.logError))
		.pipe(autoprefixer({
			cascade: false
		}))
		.pipe(rename({
			extname: ".min.css"
		}))
		.pipe(dest(cfg.dist.cssDir));

	if (cb) {
		cb();
	}
};

const watchifyTask = (cb) => {
	// Serve files from the root of this project
	browserSync.init({
		files: ['./public_html/dist/compiled', './ressources/views/**/*.blade.php'],
		ghostMode: false
	});

	console.log(cfg.src.jsDir + '**/*.js');

	const cssWatcher = watch([cfg.src.sassDir + '**/*.*']);

	const bladeWatcher = watch([cfg.src.bladeFiles]);

	cssWatcher.on('change', (path) => {
		// console.log(vinyl.extname.blue + ' File ' + vinyl.relative.green + ' was ' + vinyl.event.bgGreen.bold);
		console.log(path + " changed");
		bustCache(css);
	});

	bladeWatcher.on('change', (path) => {
		// console.log(vinyl.extname.blue + ' File ' + vinyl.relative.green + ' was ' + vinyl.event.bgGreen.bold);
		console.log(path + " changed");
		browserSync.reload();
	});

	watchifyJs(() => {}, false);

	cb();
};


function prod(cb) {
	process.env.NODE_ENV = 'production';
	cb();
}

const serviceWorker = () => {
	const swSrc = './resources/js/serviceWorker/sw-nomanifest.js';

	const swDest = './public_html/sw.js';
	const globDirectory = './public_html/';
	const compiledSwDest = './public_html/';
	const swName = "sw.js";

	return workboxBuild.copyWorkboxLibraries(compiledSwDest)
		.then((workboxDirectoryName) => {
			console.info('Workbox libraries copied to: ' + workboxDirectoryName);
			return workboxBuild.injectManifest({
				swSrc,
				swDest,
				maximumFileSizeToCacheInBytes: 50000000,
				globDirectory,
				globPatterns: [
					'dist/compiled/*.{html,json,js,css}',
					'*.ico'
				],
			}).then((result) => {
				result.warnings.forEach(console.warn);
				console.info(`${result.count} files will be precached, totaling ${result.size} bytes.`);
				console.info(`import ${compiledSwDest}${workboxDirectoryName}/workbox-sw.js to service-worker-script`);

				console.info('Service worker generation completed.');
			});
		}).catch((error) => {
			console.warn('Workbox libraries copying failed: ' + error);
		});
}


const jsProd = () => browserify(cfg.src.jsMainFile.path)
	.transform(babelify, {
		global: true,
		presets: ["@babel/preset-env", "@babel/preset-react"]
	})
	.bundle()
	.on('error', handleErrors)
	.pipe(source(cfg.src.jsMainFile.name))
	.pipe(buffer())
	.pipe(streamify(uglify()))
	.pipe(rename({
		extname: ".prod.js"
	}))
	.pipe(gulp.dest(cfg.dist.jsDir));

exports.default = series(css, js);
exports.css = css;
exports.js = js;
exports.watchJs = watchifyJs;
exports.jsProd = jsProd;
exports.prod = series(prod, validateFomantic, jsProd, css, bustCache, serviceWorker);
exports.watch = series(validateFomantic, css, watchifyTask);
exports.build = series(validateFomantic, css, js, bustCache, serviceWorker);
exports.bustCache = bustCache;
exports.serviceWorker = serviceWorker;

gulp.task('fomanticCss', fomanticBuildCss);
gulp.task('fomanticJs', fomanticBuildJs);
gulp.task('fomanticAssets', fomanticBuildAssets);
