/*
npm i --save-dev gulp gulp-sass gulp-concat gulp-uglify gulp-cssnano browser-sync gulp-autoprefixer gulp-twig gulp-rename flatpickr font-awesome
*/

var gulp         = require('gulp'), // Подключаем Gulp
    sass         = require('gulp-sass'), //Подключаем Sass пакет,
    uglify       = require('gulp-uglify'), // Подключаем gulp-uglifyjs (для сжатия JS)
    cssnano      = require('gulp-cssnano'), // Подключаем пакет для минификации CSS
    rename       = require('gulp-rename'), // Подключаем пакет для переименования
    concat       = require('gulp-concat'), // Объединение js файлов
    cssnano      = require('gulp-cssnano'), // Подключаем пакет для минификации CSS
	browserSync  = require('browser-sync').create(), // Отслеживание изменений online
    autoprefixer = require('gulp-autoprefixer');// Библиотека для автоматического добавления префиксов

gulp.task('watch', function() {
	browserSync.init({
		proxy: "sodomania/"
	});

	gulp.watch('assets/scss/*.scss', ['sass']);
	gulp.watch('assets/js/*.js', ['scripts']);
	gulp.watch('templates/**/*.html.twig').on('change', browserSync.reload);
	gulp.watch('templates/base.html.twig').on('change', browserSync.reload);
	gulp.watch('src/Application.php').on('change', browserSync.reload);
	gulp.watch('src/**/*.php').on('change', browserSync.reload);
});

gulp.task('fonts', function(){
	return gulp.src('./node_modules/font-awesome/fonts/*')
	.pipe(gulp.dest('./public/fonts/'));
});

gulp.task('jquery', function() {
	return gulp.src('./node_modules/jquery/dist/jquery.min.js')
	.pipe(gulp.dest('./public/js/'));
});

gulp.task('sass', function() {
	return gulp.src('assets/scss/*.scss')
		   .pipe(sass())
		   .pipe(autoprefixer(['last 25 versions', 'ie 6-9'], { cascade: true}))
		   .pipe(cssnano())
		   .pipe(gulp.dest('public/css'))
		   .pipe(browserSync.reload({stream: true}))
});

gulp.task('scripts', function() {
	return gulp.src('assets/js/*.js')
		   .pipe(concat('libs.js'))
		   .pipe(uglify())
		   .pipe(gulp.dest('public/js'))
		   .pipe(browserSync.reload({stream: true}))
});