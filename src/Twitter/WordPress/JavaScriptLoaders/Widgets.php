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

namespace Twitter\WordPress\JavaScriptLoaders;

/**
 * Load the remotely hosted Twitter widget JavaScript
 *
 * @since 1.0.0
 */
class Widgets extends AsyncJavaScript
{
	/**
	 * Twitter widget JavaScript handle
	 *
	 * Used in WordPress JavaScript queue
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const QUEUE_HANDLE = 'twitter-wjs';

	/**
	 * Twitter widget JavaScript fully-qualified domain name
	 *
	 * Used to prefetch DNS lookup
	 *
	 * @since 1.1.0
	 *
	 * @type string
	 */
	const FQDN = 'platform.twitter.com';

	/**
	 * Twitter widgets JavaScript absolute URI
	 *
	 * @since 1.1.0
	 *
	 * @type string
	 */
	const URI = 'https://platform.twitter.com/widgets.js';

	/**
	 * Extra JavaScript to be loaded with external JS
	 *
	 * Initialize the twttr variable to attach ready events before JS loaded
	 *
	 * @see WP_Scripts::print_extra_script()
	 * @link https://dev.twitter.com/web/javascript/loading Twitter Widgets JS async loading snippet
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const SCRIPT_EXTRA = 'window.twttr=(function(w){t=w.twttr||{};t._e=[];t.ready=function(f){t._e.push(f);};return t;}(window));';

	/**
	 * Load Twitter widget JS using an inline script block
	 *
	 * Suitable for unknown render environments where a script block may not be included in a standard enqueue output such as the wp_print_footer_scripts action.
	 *
	 * @since 1.1.0
	 *
	 * @param bool $include_script_element_wrapper wrap the returned JavaScript string in a script element wrapper
	 *
	 * @return string HTML script element containing loader script
	 */
	public static function asyncScriptLoaderInline( $include_script_element_wrapper = true ) {
		$script = 'window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src=' . json_encode( self::URI ) . ';fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script",' . json_encode( self::QUEUE_HANDLE ) . '));';

		if ( $include_script_element_wrapper ) {
			return '<script>' . $script . '</script>';
		}
		return $script;
	}
}
