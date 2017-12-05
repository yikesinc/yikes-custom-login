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

		// generates POT file
		pot: {
			options: {
				text_domain: 'custom-wp-login',
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
	grunt.loadNpmTasks('grunt-contrib-cssmin'); // CSS Minifier
	grunt.loadNpmTasks('grunt-pot'); // POT file

	// register task
	grunt.registerTask('default', [
		'uglify',
		'cssmin',
		'pot'
	]);
};
