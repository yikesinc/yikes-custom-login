'use strict';
module.exports = function( grunt ) {

	grunt.initConfig({

		// js minification
		uglify: {
			dist: {
				files: {
					// admin scripts
					/*
			    'admin/js/min/yikes-inc-easy-mailchimp-dashboard-widget.min.js': [ // widget specific script
			        'admin/js/yikes-inc-easy-mailchimp-dashboard-widget.js'
			    ],
					*/
				}
			}
		},

		// css minify all contents of our directory and add .min.css extension
		cssmin: {
			target: {
				files: [
					// public css
					{
						expand: true,
						cwd: 'lib/css',
						src: [
							'yikes-custom-login-public.css',
						],
						dest: 'lib/css/min',
						ext: '.min.css'
					},
					// admin css
					{
						expand: true,
						cwd: 'lib/css',
						src: [
							'yikes-custom-login-admin.css',
						],
						dest: 'lib/css/min',
						ext: '.min.css'
					}
				]
			}
		},

		// watch our project for changes
		watch: {
			all_css_files: {
			 	// public css
				files: 'lib/css/*.css',
				tasks: ['cssmin'],
				options: {
					spawn: false,
					event: ['all']
				},
			},
		},

		// Autoprefixer for our CSS files
		postcss: {
			options: {
				map: true,
					processors: [
						require('autoprefixer-core') ({
							browsers: ['last 2 versions']
						})
					]
				},
			dist: {
			  src: [ 'lib/css/*.css' ]
			}
		},

	});

	// load tasks
	grunt.loadNpmTasks('grunt-contrib-uglify'); // uglify our JS files
	grunt.loadNpmTasks('grunt-postcss'); // CSS autoprefixer plugin (cross-browser auto pre-fixes)
	grunt.loadNpmTasks('grunt-contrib-cssmin'); // CSS Minifier
	grunt.loadNpmTasks('grunt-contrib-watch'); // Watch files for changes

	// register task
	grunt.registerTask('default', [
		'uglify',
		'postcss',
		'cssmin',
		'watch',
	]);
};
