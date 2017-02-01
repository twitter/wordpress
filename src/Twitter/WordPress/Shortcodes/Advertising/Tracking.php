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

namespace Twitter\WordPress\Shortcodes\Advertising;

/**
 * Track a Twitter conversion and/or remarketing audience
 *
 * @since 2.0.0
 */
class Tracking implements \Twitter\WordPress\Shortcodes\ShortcodeInterface
{

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_tracking';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '' );

	/**
	 * Stored tracking ids
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	protected static $tracking_ids = array();

	/**
	 * Register shortcode macro and handler
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		$class = get_called_class();
		add_shortcode( static::SHORTCODE_TAG, array( $class, 'shortcodeHandler' ) );

		// Shortcode UI, if supported
		add_action(
			'register_shortcode_ui',
			array( $class, 'shortcodeUI' ),
			5,
			0
		);
	}

	/**
	 * Reference the feature by name
	 *
	 * @since 2.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return __( 'Twitter Advertising Tracker', 'twitter' );
	}

	/**
	 * Describe shortcode for Shortcake UI
	 *
	 * @since 1.1.0
	 *
	 * @link https://github.com/fusioneng/Shortcake Shortcake UI
	 *
	 * @return void
	 */
	public static function shortcodeUI()
	{
		// Shortcake required
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}

		shortcode_ui_register_for_shortcode(
			static::SHORTCODE_TAG,
			array(
				'label'         => esc_html( static::featureName() ),
				'listItemImage' => 'dashicons-twitter',
				'attrs'         => array(
					array(
						'attr'  => 'id',
						'label' => 'ID',
						'description' => esc_html( __( 'Twitter conversion or remarketing audience tracking identifier', 'twitter' ) ),
						'type'  => 'text',
						'meta'  => array(
							'required' => true,
						),
					),
				),
			)
		);
	}

	/**
	 * Shortcode allowed attributes and default values
	 *
	 * @since 2.0.0
	 *
	 * @return array shortcode allowed attributes and default values
	 */
	public static function getShortcodeDefaults()
	{
		return static::$SHORTCODE_DEFAULTS;
	}

	/**
	 * Process shortcode attributes received from the shortcode API
	 *
	 * @since 2.0.0
	 *
	 * @link https://codex.wordpress.org/Shortcode_API Shortcode API
	 *
	 * @param array $attributes associative array of shortcode attributes, usually from the Shortcode API
	 *
	 * @return array array processed by shortcode_atts, prepped for Tweet object
	 */
	public static function getShortcodeAttributes( $attributes )
	{
		$options = shortcode_atts(
			static::getShortcodeDefaults(),
			$attributes,
			static::SHORTCODE_TAG
		);

		if ( ! is_array( $options ) || empty( $options ) ) {
			return array();
		}

		if ( isset( $options['id'] ) ) {
			$options['id'] = \Twitter\Helpers\Validators\WebsiteTag::sanitize( $options['id'] );
			if ( ! $options['id'] ) {
				unset( $options['id'] );
			}
		}

		return $options;
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 2.0.0
	 *
	 * @param array  $attributes shortcode attributes
	 * @param string $content    shortcode content. no effect
	 *
	 * @return string empty string. markup is queued for inclusion in wp_footer output
	 */
	public static function shortcodeHandler( $attributes, $content = '' )
	{
		$options = static::getShortcodeAttributes( $attributes );

		if ( ! isset( $options['id'] ) ) {
		    return '';
		}

		\Twitter\WordPress\JavaScriptLoaders\Tracking::enqueue();
		static::$tracking_ids[ $options['id'] ] = true;
		$class = get_called_class();

		if ( false === has_action( 'wp_footer', array( $class, 'trackerJavaScript' ) ) ) {
			// execute script after wp_print_footer_scripts action completes at priority 20
			// async function queue should be inserted with footer scripts
			add_action( 'wp_footer', array( $class, 'trackerJavaScript' ), 25 );
		}

		// execute all trackers just before </body>
		return '';
	}

	/**
	 * Track a Twitter advertising event as a UWT PageView
	 *
	 * @since 2.0.0
	 *
	 * @param string $tracking_id Twitter ads tracking ID
	 *
	 * @return void
	 */
	protected static function trackEventUsingJavaScript( $tracking_id )
	{
		echo 'twq("init",' . wp_json_encode( $tracking_id ) . ');twq("track","PageView");';
	}

	/**
	 * Track a Twitter advertising event using 1x1 images
	 *
	 * @since 2.0.0
	 *
	 * @param string $tracking_id Twitter ads tracking ID
	 *
	 * @return void
	 */
	protected static function trackEventUsingFallbackImages( $tracking_id )
	{
		$query_parameters = http_build_query( array( 'txn_id' => $tracking_id, 'p_id' => 'Twitter' ), '', '&' );

		echo '<img height="1" width="1" alt=" " src="' . esc_url( 'https://analytics.twitter.com/i/adsct?' . $query_parameters, array( 'https', 'http' ) ) . '"';
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();

		echo '><img height="1" width="1" alt=" " src="' . esc_url( 'https://t.co/i/adsct?' . $query_parameters, array( 'https', 'http' ) ) . '"';
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();

		echo '>';
	}

	/**
	 * Record tracking ID actions
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function trackerJavaScript()
	{
		if ( empty( static::$tracking_ids ) ) {
			return;
		}

		$tracking_ids = array_keys( static::$tracking_ids );
		$class = get_called_class();

		// JavaScript available
		echo '<script type="text/javascript">if(typeof window.twq !== "undefined"){';
		array_walk( $tracking_ids, array( $class, 'trackEventUsingJavaScript' ) );
		echo '}</script>';

		// JavaScript unavailable
		echo '<noscript>';
		array_walk( $tracking_ids, array( $class, 'trackEventUsingFallbackImages' ) );
		echo '</noscript>';
	}
}
