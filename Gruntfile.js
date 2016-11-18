module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        /**
         * Project banner
         * Dynamically appended to CSS/JS files
         * Inherits text from package.json
         */
        tag: {
            banner: '/*!\n' +
            ' * <%= pkg.name %>\n' +
            ' * <%= pkg.title %>\n' +
            ' * <%= pkg.url %>\n' +
            ' * @author <%= pkg.author %>\n' +
            ' * @version <%= pkg.version %>\n' +
            ' * Copyright <%= pkg.copyright %>. <%= pkg.license %> licensed.\n' +
            ' */\n'
        },

        /**
         * Compile sass files
         * https://github.com/sass/node-sass
         */
        sass: {
            dev: {
                options: {
                    outputStyle: 'expanded',
                },
                files: [
                    {
                        expand: true,
                        cwd: 'assets/admin/css/',
                        src: ['**/*.scss'],
                        dest: 'assets/admin/css/',
                        ext: ['.css']
                    },
                    {
                        expand: true,
                        cwd: 'assets/public/css/',
                        src: ['**/*.scss'],
                        dest: 'assets/public/css/',
                        ext: ['.css']
                    }
                ]
            },
            dist: {
                options: {
                    outputStyle: 'compressed',
                    banner: "<%= tag.banner %>"
                },
                files: [
                    {
                        expand: true,
                        cwd: 'assets/admin/css/',
                        src: ['**/*.scss'],
                        dest: 'assets/admin/css/',
                        ext: ['.min.css']
                    },
                    {
                        expand: true,
                        cwd: 'assets/public/css/',
                        src: ['**/*.scss'],
                        dest: 'assets/public/css/',
                        ext: ['.min.css']
                    }
                ]
            }
        },

        /**
         * Uglify (minify) JavaScript files
         * https://github.com/gruntjs/grunt-contrib-uglify
         * Compresses and minifies all JavaScript files into one
         */
        uglify: {
            options: {
                banner: "<%= tag.banner %>"
            },
            dist: {
                files: [
                    {
                        expand: true,
                        cwd: 'assets/admin/js/',
                        src: ['**/*.js'],
                        dest: 'assets/admin/js/',
                        ext: ['.min.js']
                    },
                    {
                        expand: true,
                        cwd: 'assets/public/js/',
                        src: ['**/*.js'],
                        dest: 'assets/public/js/',
                        ext: ['.min.js']
                    }
                ]
            }
        },

        watch: {
            sass: {
                files: ['assets/admin/css/{,*/}*.{scss,sass}', 'assets/public/css/{,*/}*.{scss,sass}'],
                tasks: ['sass:dev', 'sass:dist']
            },
            uglify: {
                files: ['assets/admin/js/{,*/}*.js', 'assets/public/js/{,*/}*.js'],
                tasks: ['uglify']
            }
        },
        copy: {
            main: {
                files: [
                    {expand: true, src: ['libs/**'], dest: 'build/tmp/'},
                    {expand: true, src: ['templates/**'], dest: 'build/tmp/'},
                    {expand: true, src: ['assets/**'], dest: 'build/tmp/'},
                    {expand: true, src: ['docs/**'], dest: 'build/tmp/'},
                    {expand: false, src: ['README.md'], dest: 'build/tmp/README.md'},
                    {expand: false, src: ['README.html'], dest: 'build/tmp/README.html'},
                    {expand: false, src: ['<%= pkg.name %>.php'], dest: 'build/tmp/<%= pkg.name %>.php'}
                ]
            }
        },
        clean: {
            build: ["build/tmp/*", "!build/.svn/*"],
        },
        compress: {
            main: {
                options: {
                    archive: 'build/<%= pkg.name %>-v<%= pkg.version %>.zip'
                },
                files: [
                    {cwd: 'build/tmp/', src: ['**'], dest: '<%= pkg.name %>/'},
                ]
            }
        },
        markdown: {
            all: {
                files: [
                    {
                        expand: true,
                        src: 'README.md',
                        dest: './',
                        ext: '.html'
                    }
                ],
                options: {
                    template: 'markdown-template.jst',
                }
            }
        },
        'string-replace': {
            dist:{
                files:[{
                  expand: true,
                  cwd: './',
                  src: ['<%= pkg.name %>.php', 'README.md']
                }],
                options:{
                    replacements: [
                        {
                            pattern: /Version: ([0-9a-zA-Z\-\.]+)/m,
                            replacement: 'Version: <%= pkg.version %>'
                        },
                        {
                            pattern: /\$version = '([0-9a-zA-Z\-\.]+)';/m,
                            replacement: '$version = \'<%= pkg.version %>\';'
                        }
                    ]
                }
            }
        }
    });

    // grunt modules
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-markdown');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Default task(s).
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('css', ['sass:dev', 'sass:dist']);
    grunt.registerTask('build', ['string-replace', 'markdown', 'clean:build', 'sass:dev', 'copy', 'compress']);

};