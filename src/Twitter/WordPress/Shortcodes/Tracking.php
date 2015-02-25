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

namespace Twitter\WordPress\Shortcodes;

/**
 * Track a Twitter conversion and/or remarketing audience
 *
 * @since 1.0.0
 */
class Tracking
{

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_tracking';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '' );

	/**
	 * Stored tracking ids
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	protected static $tracking_ids = array();

	/**
	 * Register shortcode macro and handler
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		add_shortcode( static::SHORTCODE_TAG, array( __CLASS__, 'shortcodeHandler' ) );
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attributes shortcode attributes
	 * @param string $content    shortcode content. no effect
	 *
	 * @return string empty string. markup is queued for inclusion in wp_footer output
	 */
	public static function shortcodeHandler( $attributes, $content = null )
	{
		$options = shortcode_atts(
			static::$SHORTCODE_DEFAULTS,
			$attributes,
			static::SHORTCODE_TAG
		);

		$tracking_id = trim( $options['id'] );
		if ( ! $tracking_id ) {
			return '';
		}

		\Twitter\WordPress\JavaScriptLoaders\Tracking::enqueue();
		static::$tracking_ids[ $tracking_id ] = true;

		if ( false === has_action( 'wp_footer', array( __CLASS__, 'trackerJavaScript' ) ) ) {
			// execute script after wp_print_footer_scripts action completes at priority 20
			add_action( 'wp_footer', array( __CLASS__, 'trackerJavaScript' ), 25 );
		}

		// execute all trackers just before </body>
		return '';
	}

	/**
	 * Track a Twitter advertising event using the twttr.conversion.trackPid JavaScript function
	 *
	 * @since 1.0.0
	 *
	 * @param string $tracking_id Twitter ads tracking ID
	 *
	 * @return void
	 */
	protected static function trackEventUsingJavaScript( $tracking_id )
	{
		echo 'twttr.conversion.trackPid(' . ( function_exists( 'wp_json_encode' ) ? wp_json_encode( $tracking_id ) : json_encode( $tracking_id ) ) . ');';
	}

	/**
	 * Track a Twitter advertising event using 1x1 images
	 *
	 * @since 1.0.0
	 *
	 * @param string $tracking_id Twitter ads tracking ID
	 *
	 * @return void
	 */
	protected static function trackEventUsingFallbackImages( $tracking_id )
	{
		$query_parameters = http_build_query( array( 'txn_id' => $tracking_id, 'p_id' => 'Twitter' ), '', '&' );

		echo '<img height="1" width="1" alt=" " src="' . esc_url( 'https://analytics.twitter.com/i/adsct?' . $query_parameters, array( 'https', 'http' ) )  . '"' . \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';
		echo '<img height="1" width="1" alt=" " src="' . esc_url( 'https://t.co/i/adsct?' . $query_parameters, array( 'https', 'http' ) ) . '"' . \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';
	}

	/**
	 * Record tracking ID actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function trackerJavaScript()
	{
		if ( empty( static::$tracking_ids ) ) {
			return;
		}

		$tracking_ids = array_keys( static::$tracking_ids );

		// JavaScript available
		echo '<script type="text/javascript">';
		array_walk( $tracking_ids, array( __CLASS__, 'trackEventUsingJavaScript' ) );
		echo '</script>';

		// JavaScript unavailable
		echo '<noscript>';
		array_walk( $tracking_ids, array( __CLASS__, 'trackEventUsingFallbackImages' ) );
		echo '</noscript>';
	}
}
