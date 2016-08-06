module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dev: {
                options: {
                    style: 'expanded',
                },
                files: {
                    'assets/css/basic.css': 'assets/scss/basic.scss',
                    'assets/css/core.css': 'assets/scss/core.scss'
                }
            },
            dist: {
                options: {
                    style: 'compressed',
                },
                files: {
                    'assets/css/basic.css': 'assets/scss/basic.scss',
                    'assets/css/core.css': 'assets/scss/core.scss'
                }
            }
        },
        watch: {
            sass: {
                files: 'assets/scss/{,*/}*.{scss,sass}',
                tasks: ['sass:dev']
            }
        },
        copy: {
            main: {
                files: [
                    {expand: true, src: ['libs/**'], dest: 'build/tmp/'},
                    {expand: true, src: ['assets/css/**'], dest: 'build/tmp/'},
                    {expand: true, src: ['assets/js/**'], dest: 'build/tmp/'},
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
                  // dest: '<%= pkg.name %>.php'
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

    // Default task(s).
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['string-replace', 'markdown', 'clean:build', 'sass:dev', 'copy', 'compress']);

};