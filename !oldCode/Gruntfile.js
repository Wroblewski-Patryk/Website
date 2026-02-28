module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        less: {
            desktop: {
                files: {
                    './css/styles.css': './less/styles.less'
                }
            }
        },
        autoprefixer:{
            desktop: {
                files: {
                    './css/styles.css': './css/styles.css'
                }
            }
        },
        cssmin: {
            desktop: {
                files: {
                    './css/styles.min.css': './css/styles.css'
                }
            }
        },

        uglify: {
            desktop: {
                files: {
                    './js/scripts.min.js': './js/scripts.js'
                }
            }
        },
        concat: {
            desktop: {
                src: [
                    './js/scripts/app.js',
                    './js/scripts/route.js',
                    './js/scripts/helper.js',

                    './js/scripts/background/border.js',
                    './js/scripts/background/color.js',
                    './js/scripts/background/image.js',
                    './js/scripts/background/title.js',

                    './js/scripts/common/info.js',
                    './js/scripts/common/logotype.js',
                    './js/scripts/common/nav.js',

                    './js/scripts/sections.js',
                    './js/scripts/sections/welcome.js',
                    './js/scripts/sections/about.js',
                    './js/scripts/sections/work.js',
                    './js/scripts/sections/contact.js'
                ],
                dest: './js/scripts.js'
            }
        },

        //WATCHERS
        watch: {
            styles: {
                files: [
                    './less/*.less',
                    './less/common/*.less',
                    './less/sections/*.less'
                ],
                tasks: [
                    'less',
                    'autoprefixer',
                    'cssmin'
                ]
            },
            concat: {
                files: [
                    './js/scripts/*.js',
                    './js/scripts/background/*.js',
                    './js/scripts/common/*.js',
                    './js/scripts/sections/*.js'
                ],
                tasks: [
                    'concat'
                ]
            },
            scripts:{
                files: [
                    './js/scripts.js'
                ],
                tasks: [
                    'uglify'
                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-contrib-concat');

    grunt.registerTask('default', ['watch']);
    grunt.registerTask('watch-styles', ['watch:styles']);
    grunt.registerTask('watch-concat', ['watch:concat']);
    grunt.registerTask('watch-scripts', ['watch:scripts']);
};