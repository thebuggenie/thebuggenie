module.exports = function (grunt) {
    require('load-grunt-tasks')(grunt, {scope: 'devDependencies'});
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        // Before generating any new files, remove any previously-created file(s).
        clean: {
            options: {
                force: true
            },
            src: [
                'public/js/**/*.min.js',
                'public/js/**/*.min.js.map',
                'modules/*/public/js/**/*.min.js',
                'modules/*/public/js/**/*.min.js.map',
                'modules/css/**/*.min.css',
                'public/css/**/*.min.css',
                'modules/*/fixtures/**/*.min.css',
                'modules/*/public/css/**/*.min.css',
                'themes/css/**/*.min.css'
            ]
        },
        // Copy over static (re)source file(s).
        copy: {
            dist: {
                files: [
                    { expand: true, cwd: 'public/js', src: ['**/*.js', '!*.min.js'], dest: 'public/js/tbg-<%= pkg.version %>' },
                    { expand: true, cwd: 'public/css', src: ['**/*.css', '!*.min.css'], dest: 'public/css/tbg-<%= pkg.version %>' }
                ]
            }
        },
        // Minify JS file(s).
        uglify: {
            options: {
                sourceMap: true,
                // Don't translate $super variable, as this would break prototype.js
                mangle: {
                    except: ["$super"]
                }
            },
            dist: {
                files: [
                    { expand: true, cwd: 'public/js', src: ['**/*.js', '!*.min.js'], dest: 'public/js', ext: '.min.js', extDot: 'last' },
                    { expand: true, cwd: 'modules', src: ['**/*.js', '!*.min.js', '!vendor/**/*.js'], dest: 'modules', ext: '.min.js', extDot: 'last' }
                ]
            }
        },
        // Minify CSS file(s).
        cssmin: {
            options: {
                compatibility: 'ie8',
                keepSpecialComments: 0
            },
            dist: {
                files: [
                    { expand: true, cwd: 'public/css', src: ['**/*.css', '!*.min.css'], dest: 'public/css', ext: '.min.css', extDot: 'last' },
                    { expand: true, cwd: 'themes', src: ['**/*.css', '!*.min.css'], dest: 'themes', ext: '.min.css', extDot: 'last' },
                    { expand: true, cwd: 'modules', src: ['**/*.css', '!*.min.css', '!vendor/**/*.css'], dest: 'modules', ext: '.min.css', extDot: 'last' }
                ]
            }
        }
    });
    grunt.registerTask('default', ['clean', 'uglify', 'cssmin']);
};
