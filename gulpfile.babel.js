// -----------------------------------------------------------------------------
//    ______   __    __  __        _______
//   /      \ /  |  /  |/  |      /       \
//  /$$$$$$  |$$ |  $$ |$$ |      $$$$$$$  |
//  $$ | _$$/ $$ |  $$ |$$ |      $$ |__$$ |
//  $$ |/    |$$ |  $$ |$$ |      $$    $$/
//  $$ |$$$$ |$$ |  $$ |$$ |      $$$$$$$/
//  $$ \__$$ |$$ \__$$ |$$ |_____ $$ |
//  $$    $$/ $$    $$/ $$       |$$ |
//   $$$$$$/   $$$$$$/  $$$$$$$$/ $$/
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// ES2015
//

"use strict";

// GULP AND TOOLS
import ngulp from "gulp";
import gulphelp from "gulp-help";
import gutil from "gulp-util";
import notifier from "terminal-notifier";
import chalk from "chalk";
import gulpSequence from "gulp-sequence";
import browserSync from "browser-sync";
import sourcemaps from "gulp-sourcemaps";
import concat from "gulp-concat";

// SASS
import sass from "gulp-sass";
import sassGlob from "gulp-sass-glob";
import prefix from "gulp-autoprefixer";

// LOAD CONFIGURATION FILES
const config = require("./gulp.config.json");
const gulp = gulphelp(ngulp);

browserSync.create();

// -----------------------------------------------------------------------------
// BROWSERSYNC SERVE -- http://www.browsersync.io/docs/gulp/
// -----------------------------------------------------------------------------
gulp.task("serve", "Serve files from the " + chalk.yellow("./dist/html") + " directory.",() => {
  browserSync.init({
    proxy: "warm-huis.test"
  });
});

// -----------------------------------------------------------------------------
// SASS -- https://www.npmjs.com/package/gulp-sass
// -----------------------------------------------------------------------------
gulp.task("sass", "Compile sass files from the " + chalk.yellow("./src/sass") + " into css files in the " + chalk.yellow("./dist/css") + " directory.", () => {
 return gulp.src(config.path.src + "/scss/*.scss")
  .pipe(sourcemaps.init())
  .pipe(sassGlob())
  .pipe(sass().on("error", sass.logError))
  .pipe(prefix(config.autoprefixer))
  .pipe(sourcemaps.write())
  .pipe(gulp.dest(config.path.dist + "/css"))
  .pipe(browserSync.stream());
});
// -----------------------------------------------------------------------------
// WATCH
// -----------------------------------------------------------------------------
gulp.task("watch", "Watches the: html, scss and js files in " + chalk.yellow("./src"), () => {
  gulp.watch(config.path.src + "/scss/**/*.scss", ["sass"]);
});

// -----------------------------------------------------------------------------
// DEFAULT TASK
// -----------------------------------------------------------------------------
gulp.task("default", chalk.green("GROUP") + " executes: " + chalk.blue("help, sass, watch & serve") + " in sequence.", gulpSequence(
    "help",
    "sass",
    "watch",
    "serve"
  )
);
