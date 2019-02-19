var fs = require('fs'),
    https = require('https'),
    exec = require('child_process').exec,
    eventStream = require('event-stream'),
    gulp = require('gulp'),
    chmod = require('gulp-chmod'),
    zip = require('gulp-zip'),
    tar = require('gulp-tar'),
    gzip = require('gulp-gzip'),
    rename = require('gulp-rename');

    var config = {
        getComposerUrl: 'https://getcomposer.org/installer',
        phpBin: 'php -d "suhosin.executor.include.whitelist = phar" -d "memory_limit=512M"'
    }

// Get and install PHP Composer
gulp.task('get-composer', function(callback) {
    // Check if Composer already in place
    if (fs.existsSync('./composer.phar')) {
        callback(null);

        return;
    }

    // Get installer from the internet
    https.get(config.getComposerUrl, function(response) {
        // Run PHP to install Composer
        var php = exec(config.phpBin, function(error, stdout, stderr) {
            callback(error ? stderr : null);
        });
        // Pass installer code to PHP via STDIN
        response.pipe(php.stdin);
    });
});

// Install Composer dependencies
gulp.task('composer-install', ['get-composer'], function(callback) {
    exec(config.phpBin + ' composer.phar install --no-dev', function(error, stdout, stderr) {
        callback(error ? stderr : null);
    });
});

gulp.task('prepare-release', ['composer-install'], function() {
    var version = require('./package.json').version;

    return eventStream.merge(
        getSources()
            .pipe(zip('telegram-' + version + '.zip')),
        getSources()
            .pipe(tar('telegram-' + version + '.tar'))
            .pipe(gzip())
    )
    .pipe(chmod(644))
    .pipe(gulp.dest('release'));
});

// Builds and packs plugins sources
gulp.task('default', ['prepare-release'], function() {
    // The "default" task is just an alias for "prepare-release" task.
});

/**
 * Returns files stream with the plugin sources.
 *
 * @returns {Object} Stream with VinylFS files.
 */
var getSources = function() {
    return gulp.src([
            'Plugin.php',
            'README.md',
            'LICENSE',
            'vendor/**/*.*'
        ],
        {base: './'}
    )
    .pipe(rename(function(path) {
        path.dirname = 'Mibew/Mibew/Plugin/Telegram/' + path.dirname;
    }));
}