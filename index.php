<?php
/**
 * @package twitter
 * @version 1.0.0
 */
/*
Plugin Name: Twitter
Plugin URI: http://wordpress.org/plugins/twitter/
Description: Official Twitter plugin for WordPress. Embed Twitter content and grow your audience on Twitter.
Version: 1.0.0
Author: Twitter
Author URI: https://dev.twitter.com/
License: MIT
Text Domain: twitter
Domain Path: /languages/
*/

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

// plugin requires PHP 5.4 or newer
if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {

    if ( is_admin() ) {

        trigger_error( 'The Twitter plugin for WordPress requires PHP version 5.4 or higher.' );

        function twitter_wp_plugin_requirements_not_met()
        {
            echo '<div class="error"><p>The Twitter plugin for WordPress requires PHP version 5.4 or higher. You have <strong>PHP ', PHP_VERSION, '</strong> installed. The plugin has been deactivated.</p></div>';
            deactivate_plugins( plugin_basename( __FILE__ ) );
            unset( $_GET['activate'] );
        }

        add_action( 'admin_notices', 'twitter_wp_plugin_requirements_not_met' );

    }

    return;
}

// PHP checks out. Load the plugin.
require_once( dirname( __FILE__ ) . '/twitter.php' );
