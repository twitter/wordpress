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
class Widgets
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
	 * Proactively resolve Twitter widget JS FQDN asynchronously before later use
	 *
	 * @since 1.0.0
	 *
	 * @link http://dev.chromium.org/developers/design-documents/dns-prefetching Chromium prefetch behavior
	 * @link https://developer.mozilla.org/en-US/docs/Controlling_DNS_prefetching Firefox prefetch behavior
	 *
	 * @return void
	 */
	public static function dnsPrefetch()
	{
		echo '<link rel="dns-prefetch" href="//' . esc_attr( self::FQDN ) . '"';
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		echo '>' . "\n";
	}

	/**
	 * Register Twitter widget JavaScript
	 *
	 * @since 1.0.0
	 *
	 * @return bool true on a successful wp_register_script response
	 */
	public static function register()
	{
		global $wp_scripts;

		$registered = wp_register_script(
			self::QUEUE_HANDLE,
			self::URI, // should be overridden during queue output by asyncScriptLoaderSrc
			array(), // no dependencies
			null, // no not add extra query parameters for cache busting
			true // in footer
		);

		// treat null response as true
		if ( ! is_bool( $registered ) ) {
			$registered = true;
		}

		// replace standard script element with async script element
		add_filter( 'script_loader_src', array( __CLASS__, 'asyncScriptLoaderSrc' ), 1, 2 );

		// initialize the twttr variable to attach ready events before JS loaded
		$script = 'window.twttr=(function(w){t=w.twttr||{};t._e=[];t.ready=function(f){t._e.push(f);};return t;}(window));';
		$data = $wp_scripts->get_data( self::QUEUE_HANDLE, 'data' );
		if ( $data ) {
			// WP 4.3+
			// do not add script data if data was possibly previously added
			if ( $registered ) {
				$script = $data . "\n" . $script;
			}
		}
		$wp_scripts->add_data( self::QUEUE_HANDLE, 'data', $script );

		return $registered;
	}

	/**
	 * Enqueue the widgets JavaScript
	 *
	 * @since 1.0.0
	 *
	 * @uses wp_enqueue_script()
	 *
	 * @return string async JavaScript loading snippet if script queue may not be supported. empty string if enqueued
	 */
	public static function enqueue()
	{
		if ( ! wp_script_is( self::QUEUE_HANDLE, 'registered' ) ) {
			static::register();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return static::asyncScriptLoaderInline();
		}

		wp_enqueue_script( self::QUEUE_HANDLE );

		return '';
	}

	/**
	 * Get the script element HTML markup used to load widgets.js in a browser
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML <script> element
	 */
	public static function getScriptElement()
	{
		// type = text/javascript to match default WP_Scripts output
		// async property to unlock page load, preload scanner discoverable in modern browsers
		// defer property for IE 9 and older
		return '<script type="text/javascript" id="' . esc_attr( self::QUEUE_HANDLE ) . '" async defer src="' . esc_url( self::URI, array( 'http', 'https' ) ) . '" charset="utf-8"></script>' . "\n";
	}

	/**
	 * Create our own script element markup, replacing WordPress default with async loading
	 *
	 * Can be used with `script_loader_tag` filter in WordPress 4.1+
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag    The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 * @param string $src    The script's source URL.
	 *
	 * @return string The `<script>` tag for the enqueued script
	 */
	public static function scriptLoaderTag( $tag, $handle, $src = '' )
	{
		if ( ! ( is_string( $handle ) && $handle === static::QUEUE_HANDLE ) ) {
			return $tag;
		}

		return static::getScriptElement();
	}

	/**
	 * Load Twitter widget JS using async deferred JavaScript properties
	 *
	 * Called from `script_loader_src` filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $src    script URL
	 * @param string $handle WordPress registered script handle
	 * @global WP_Scripts $wp_scripts match concatenation preferences
	 *
	 * @return string empty string if Twitter widget JS, else give back the src variable
	 */
	public static function asyncScriptLoaderSrc( $src, $handle )
	{
		global $wp_scripts;

		if ( ! ( is_string( $handle ) && $handle === self::QUEUE_HANDLE ) ) {
			return $src;
		}

		$html = static::getScriptElement();

		if ( isset( $wp_scripts ) && $wp_scripts->do_concat ) {
			$wp_scripts->print_html .= $html;
		} else {
			echo $html;
		}

		// empty out the src response to avoid extra <script>
		return '';
	}

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
