var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create public/build/app.js and public/build/app.css
    .addEntry('app', [
        './assets/js/app.js',
        './assets/css/app.scss'
    ])

    .addEntry('first_app', [
        './assets/css/first_app.scss'
    ])

    .addEntry('second_app', [
        './assets/css/second_app.scss'
    ])

    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()
    // show OS notifications when builds finish/fail
    // .enableBuildNotifications()
    .enableSassLoader(function (sassOptions) {}, {
        resolveUrlLoader: false
    })

// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();
