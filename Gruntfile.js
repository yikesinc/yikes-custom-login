'use strict';
module.exports = function( grunt ) {

	grunt.initConfig({
		// js minification
		uglify: {
			dist: {
				files: {
					// admin scripts
			    'lib/js/min/yikes-custom-login-options.min.js': [ // Settings page scripts (init select2 etc.)
			        'lib/js/yikes-custom-login-options.js'
			    ],
					// login page script
			    'lib/js/min/yikes-login-page.min.js': [ // Login page script to show/hide preloader etc
			        'lib/js/yikes-login-page.js'
			    ],
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

		appDetails: grunt.file.readJSON( 'package.json' ),
		usebanner: {
			taskName: {
				options: {
	        position: 'top',
					replace: true,
	        banner: '/*\n'+
					' * Plugin: YIKES Inc. Custom Login \n'+
					' * Version: <%= appDetails.version %> \n'+
					' * Author: <%= appDetails.author %> \n'+
					' * Contact: info@yikesinc.com \n'+
					' * License: <%= appDetails.license %> \n'+
					' */',
					linebreak: true
      	},
				files: {
					src: [
						'lib/css/min/yikes-custom-login-admin.min.css',
						'lib/css/min/yikes-custom-login-public.min.css',
						'lib/js/min/yikes-custom-login-options.min.js',
						'lib/js/min/yikes-login-page.min.js'
					]
				}
			}
		},

		// watch our project for changes
		watch: {
			all_css_files: {
			 	// public css
				files: 'lib/css/*.css',
				tasks: ['cssmin','usebanner'],
				options: {
					spawn: false,
					event: ['all']
				},
			},
			all_js_files: {
			 	// public css
				files: 'lib/js/*.js',
				tasks: ['uglify','usebanner'],
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

		// generates POT file
		pot: {
			options: {
				text_domain: 'yikes-inc-custom-login',
				dest: 'languages/', //directory to place the pot file
		        keywords: [ //WordPress localisation functions
		        	'__:1',
		        	'_e:1',
					'_x:1,2c',
					'esc_html__:1',
					'esc_html_e:1',
					'esc_html_x:1,2c',
					'esc_attr__:1', 
					'esc_attr_e:1', 
					'esc_attr_x:1,2c', 
					'_ex:1,2c',
					'_n:1,2', 
					'_nx:1,2,4c',
					'_n_noop:1,2',
					'_nx_noop:1,2,3c'
				],
			},
			files: {
				src:  [ '**/*.php' ], //Parse all php files
				expand: true,
			}
		}
	});

	// load tasks
	grunt.loadNpmTasks('grunt-contrib-uglify'); // uglify our JS files
	grunt.loadNpmTasks('grunt-postcss'); // CSS autoprefixer plugin (cross-browser auto pre-fixes)
	grunt.loadNpmTasks('grunt-contrib-cssmin'); // CSS Minifier
	grunt.loadNpmTasks('grunt-contrib-watch'); // Watch files for changes
	grunt.loadNpmTasks('grunt-banner'); // Banner task
	grunt.loadNpmTasks('grunt-pot'); // POT file

	// register task
	grunt.registerTask('default', [
		'uglify',
		'postcss',
		'cssmin',
		'usebanner',
		'watch',
		'pot'
	]);
};
