<?php
/*
The MIT License (MIT)

Copyright (c) 2015 Twitter Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
/**
 * @package twitter
 * @version 1.0.1
 */
/*
Plugin Name: Twitter
Plugin URI:  http://wordpress.org/plugins/twitter/
Description: Official Twitter plugin for WordPress. Embed Twitter content and grow your audience on Twitter. Requires PHP 5.4 or greater.
Version:     1.0.1
Author:      Twitter
Author URI:  https://dev.twitter.com/
License:     MIT
Text Domain: twitter
Domain Path: /languages/
*/

// make sure the plugin does not expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	if ( ! headers_sent() ) {
		if ( function_exists( 'http_response_code' ) ) {
			http_response_code( 403 );
		} else {
			header( 'HTTP/1.1 403 Forbidden', true, 403 );
		}
	}
	exit( 'Hi there! I am a WordPress plugin requiring functions included with WordPress. I am not meant to be addressed directly.' );
}

// plugin requires PHP 5.4 or greater
if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
	if ( ! class_exists( 'Twitter_CompatibilityNotice' ) ) {
		require_once( dirname(__FILE__) . '/compatibility-notice.php' );
	}

	// possibly display a notice, trigger error
	add_action( 'admin_init', array( 'Twitter_CompatibilityNotice', 'adminInit' ) );

	// stop execution of this file
	return;
}

// PHP namespace autoloader
require_once( dirname( __FILE__ ) . '/autoload.php' );

// initialize on plugins loaded
add_action(
	'plugins_loaded',
	array( '\Twitter\WordPress\PluginLoader', 'init' ),
	0, // priority
	0 // expected arguments
);
