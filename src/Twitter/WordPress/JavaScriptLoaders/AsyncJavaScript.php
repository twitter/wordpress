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
 * Improve BackPress JavaScript handlers for asynchronous use
 *
 * @since 1.3.0
 */
abstract class AsyncJavaScript
{
	/**
	 * Proactively resolve FQDN asynchronously before later use
	 *
	 * @since 1.3.0
	 *
	 * @link https://dev.chromium.org/developers/design-documents/dns-prefetching Chromium prefetch behavior
	 * @link https://developer.mozilla.org/en-US/docs/Controlling_DNS_prefetching Firefox prefetch behavior
	 *
	 * @return void
	 */
	public static function dnsPrefetch()
	{
		$classname = get_called_class();
		if ( ! ( defined( $classname . '::FQDN' ) && $classname::FQDN ) ) {
			return;
		}

		// Output a resource hint link element at a priority later than wp_resource_hints
		add_action( 'wp_head', array( $classname, 'printDNSPrefetchElement' ) );

		// Use wp_resource_hints if available
		add_filter( 'wp_resource_hints',
			(static function ( $urls, $relation_type ) use ( $classname ) {
				if ( 'dns-prefetch' === $relation_type ) {
					$urls[] = $classname::FQDN;

					// WordPress will output the resource hint link
					remove_action( 'wp_head', array( $classname, 'printDNSPrefetchElement' ) );
				}
				return $urls;
			}),
			10,
		2 );
	}

	/**
	 * Proactively resolve FQDN DNS using a resource hint link
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public static function printDNSPrefetchElement()
	{
		echo '<link rel="dns-prefetch" href="//' . esc_attr( static::FQDN ) . '"';
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		echo '>' . "\n";
	}

	/**
	 * Register JavaScript to be later referenced by queue handle
	 *
	 * Set up related inline JavaScript to be loaded if SCRIPT_EXTRA exists.
	 *
	 * @since 1.3.0
	 *
	 * @return bool true on a successful wp_register_script response
	 */
	public static function register()
	{
		// match global use in Core
		// @codingStandardsIgnoreLine Squiz.PHP.GlobalKeyword.NotAllowed
		global $wp_scripts;

		$classname = get_called_class();
		if ( ! ( defined( $classname . '::QUEUE_HANDLE' ) && $classname::QUEUE_HANDLE && defined( $classname . '::URI' ) && $classname::URI ) ) {
			return false;
		}

		$registered = wp_register_script(
			static::QUEUE_HANDLE,
			static::URI, // should be overridden during queue output by asyncScriptLoaderSrc
			array(), // no dependencies
			null, // do not add extra query parameters for cache busting
			true // in footer
		);

		// treat null response as true
		if ( ! is_bool( $registered ) ) {
			$registered = true;
		}

		// replace standard script element with async script element
		add_filter( 'script_loader_src', array( $classname, 'asyncScriptLoaderSrc' ), 1, 2 );

		if ( defined( $classname . '::SCRIPT_EXTRA' ) && is_string( static::SCRIPT_EXTRA ) && static::SCRIPT_EXTRA ) {
			$script = static::SCRIPT_EXTRA;
			$data = $wp_scripts->get_data( static::QUEUE_HANDLE, 'data' );
			if ( $data ) {
				// WP 4.3+
				// do not add script data if data was possibly previously added
				if ( $registered ) {
					$script = $data . "\n" . $script;
				}
			}
			$wp_scripts->add_data( static::QUEUE_HANDLE, 'data', $script );
		}

		return $registered;
	}

	/**
	 * Enqueue the JavaScript
	 *
	 * @since 1.3.0
	 *
	 * @uses wp_enqueue_script()
	 *
	 * @return string async JavaScript loading snippet if script queue may not be supported. empty string if enqueued
	 */
	public static function enqueue()
	{
		if ( ! ( defined( get_called_class() . '::QUEUE_HANDLE' ) && static::QUEUE_HANDLE ) ) {
			return '';
		}

		if ( ! wp_script_is( static::QUEUE_HANDLE, 'registered' ) ) {
			if ( ! static::register() ) {
				return '';
			}
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return static::asyncScriptLoaderInline();
		}

		wp_enqueue_script( static::QUEUE_HANDLE );

		return '';
	}

	/**
	 * Get the script element HTML markup used to load JavaScript in a browser
	 *
	 * @since 1.3.0
	 *
	 * @return string HTML <script> element
	 */
	public static function getScriptElement()
	{
		$classname = get_called_class();
		if ( ! ( defined( $classname . '::QUEUE_HANDLE' ) && static::QUEUE_HANDLE && defined( $classname . '::URI' ) && static::URI ) ) {
			return false;
		}

		// type = text/javascript to match default WP_Scripts output
		// id to match on DOM getElementByID in async inline loading snippet
		// async property to unlock page load, preload scanner discoverable in modern browsers
		// defer property for IE 9 and older
		return '<script type="text/javascript" id="' . esc_attr( static::QUEUE_HANDLE ) . '" async defer src="' . esc_url( static::URI, array( 'http', 'https' ) ) . '" charset="utf-8"></script>' . "\n";
	}

	/**
	 * Create our own script element markup, replacing WordPress default with async loading
	 *
	 * Can be used with `script_loader_tag` filter in WordPress 4.1+.
	 *
	 * @since 1.3.0
	 *
	 * @param string $tag    The `<script>` tag for the enqueued script
	 * @param string $handle The script's registered handle
	 * @param string $src    The script's source URL
	 *
	 * @return string The `<script>` tag for the enqueued script
	 */
	public static function scriptLoaderTag( $tag, $handle, $src = '' )
	{
		if ( ! ( defined( get_called_class() . '::QUEUE_HANDLE' ) && static::QUEUE_HANDLE ) ) {
			return '';
		}

		if ( ! ( is_string( $handle ) && static::QUEUE_HANDLE === $handle ) ) {
			return $tag;
		}

		return static::getScriptElement();
	}

	/**
	 * Load enqueued JavaScript using async deferred JavaScript properties
	 *
	 * Called from `script_loader_src` filter.
	 *
	 * @since 1.3.0
	 *
	 * @param string $src    script URL
	 * @param string $handle WordPress registered script handle
	 * @global \WP_Scripts $wp_scripts match concatenation preferences
	 *
	 * @return string empty string if our queue handle requested, else give back the src variable
	 */
	public static function asyncScriptLoaderSrc( $src, $handle )
	{
		// match global use in Core
		// @codingStandardsIgnoreLine Squiz.PHP.GlobalKeyword.NotAllowed
		global $wp_scripts;

		if ( ! ( is_string( $handle ) && defined( get_called_class() . '::QUEUE_HANDLE' ) && static::QUEUE_HANDLE === $handle ) ) {
			return $src;
		}

		$html = static::getScriptElement();

		if ( isset( $wp_scripts ) && $wp_scripts->do_concat ) {
			$wp_scripts->print_html .= $html;
		} else {
			// escaped when building element
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		// empty out the src response to avoid extra <script>
		return '';
	}

	/**
	 * Load JavaScript using an inline script block
	 *
	 * Suitable for unknown render environments where a script block may not be included in a standard enqueue output such as the wp_print_footer_scripts action. Dynamically-inserted JavaScript src is async by default.
	 *
	 * @since 1.3.0
	 *
	 * @param bool $include_script_element_wrapper wrap the returned JavaScript string in a script element wrapper
	 *
	 * @return string HTML script element containing loader script
	 */
	public static function asyncScriptLoaderInline( $include_script_element_wrapper = true )
	{
		$classname = get_called_class();
		if ( ! ( defined( $classname . '::QUEUE_HANDLE' ) && static::QUEUE_HANDLE && defined( $classname . '::URI' ) && static::URI ) ) {
			return '';
		}

		$script = '(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src=' . wp_json_encode( static::URI ) . ';fjs.parentNode.insertBefore(js,fjs);}(document,"script",' . wp_json_encode( static::QUEUE_HANDLE ) . '));';

		if ( $include_script_element_wrapper ) {
			return '<script>' . $script . '</script>';
		}
		return $script;
	}
}
