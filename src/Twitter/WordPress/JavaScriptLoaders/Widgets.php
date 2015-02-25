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
	 * Used in WordPress JavaScript queue
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const QUEUE_HANDLE = 'twitter-wjs';

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
		echo '<link rel="dns-prefetch" href="//platform.twitter.com"';
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		echo '>' . "\n";
	}

	/**
	 * Register Twitter widget JavaScript
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register()
	{
		global $wp_scripts;

		wp_register_script(
			self::QUEUE_HANDLE,
			static::getAbsoluteURI(), // should be overridden during queue output by asyncScriptLoaderSrc
			array(), // no dependencies
			null, // no not add extra query parameters for cache busting
			true // in footer
		);

		// initialize the twttr variable to attach ready events before JS loaded
		$script = 'window.twttr=(function(w){t=w.twttr||{};t._e=[];t.ready=function(f){t._e.push(f);};return t;}(window));';
		$data = $wp_scripts->get_data( self::QUEUE_HANDLE, 'data' );
		if ( $data ) {
			$script = $data . "\n" . $script;
		}
		$wp_scripts->add_data( self::QUEUE_HANDLE, 'data', $script );

		// replace standard script element with async script element
		add_filter( 'script_loader_src', array( __CLASS__, 'asyncScriptLoaderSrc' ), 1, 2 );
	}

	/**
	 * Enqueue the widgets JavaScript
	 *
	 * @since 1.0.0
	 *
	 * @uses wp_enqueue_script()
	 *
	 * @return void
	 */
	public static function enqueue()
	{
		if ( ! wp_script_is( self::QUEUE_HANDLE, 'registered' ) ) {
			static::register();
		}

		wp_enqueue_script( self::QUEUE_HANDLE );
	}

	/**
	 * The absolute URI of the Twitter widgets JavaScript file
	 *
	 * Prefer absolute URI over scheme-relative URI
	 *
	 * @since 1.0.0
	 *
	 * @return string absolute URI for the Twitter widgets JavaScript file
	 */
	public static function getAbsoluteURI()
	{
		return 'http' . ( is_ssl() ? 's' : '' ) . '://platform.twitter.com/widgets.js';
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
		return '<script type="text/javascript" id="' . esc_attr( self::QUEUE_HANDLE ) . '" async defer src="' . esc_url( static::getAbsoluteURI(), array( 'http', 'https' ) ) . '" charset="utf-8"></script>' . "\n";
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
}
