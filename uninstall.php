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

if (
	! defined( 'WP_UNINSTALL_PLUGIN' )
||
	! WP_UNINSTALL_PLUGIN
||
	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) )
) {
	if ( ! headers_sent() ) {
		if ( function_exists( 'status_header' ) ) {
			status_header( 404 );
		} else if ( function_exists( 'http_response_code' ) ) {
			http_response_code( 404 );
		} else {
			header( 'HTTP/1.1 404 Not Found', true, 404 );
		}
	}
	exit;
}

// site options
if ( function_exists( 'delete_option' ) ) {
	// Delete all admin options
	$__options = array(
		'twitter_widgets',
		'twitter_site_attribution',
		'twitter_share',
	);
	array_walk( $__options, 'delete_option' );
}

// post meta
if ( function_exists( 'delete_post_meta_by_key' ) ) {
	// delete Twitter customizations stored at the post level
	$__post_meta_keys = array(
		'twitter_share',
		'twitter_card',
	);
	array_walk( $__post_meta_keys, 'delete_post_meta_by_key' );
}

// user meta
if ( function_exists( 'delete_metadata' ) ) {
	// delete Twitter customizations stored for a WordPress user account
	delete_metadata(
		'user',    // meta type
		0,         // user ID (ignored)
		'twitter', // meta key
		'',        // delete all values
		true       // delete all. ignore passed user id
	);
}
