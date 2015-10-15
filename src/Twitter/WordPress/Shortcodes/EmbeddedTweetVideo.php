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
 * Display a video attached to a Tweet
 *
 * @since 1.0.0
 */
class EmbeddedTweetVideo extends EmbeddedTweet
{

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_video';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '', 'status' => true );

	/**
	 * Attach handlers for Twitter embedded video
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		// register our shortcode and its handler
		add_shortcode( self::SHORTCODE_TAG, array( __CLASS__, 'shortcodeHandler' ) );

		// Shortcode UI, if supported
		add_action(
			'register_shortcode_ui',
			array( __CLASS__, 'shortcodeUI' ),
			5,
			0
		);
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

		// id only
		// avoids an unchecked Shortcake input checkbox requiring a shortcode output
		shortcode_ui_register_for_shortcode(
			self::SHORTCODE_TAG,
			array(
				'label'         => __( 'Embedded Tweet Video', 'twitter' ),
				'listItemImage' => 'dashicons-twitter',
				'attrs'         => array(
					array(
						'attr'  => 'id',
						'label' => 'ID',
						'type'  => 'text',
						'meta'  => array(
							'required'    => true,
							'pattern'     => '[0-9]+',
							'placeholder' => '560070183650213889',
						),
					),
				),
			)
		);
	}

	/**
	 * Convert shortcode parameters into a clean set of Twitter embedded video options parameters
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes set of shortcode attribute-value pairs matching the WordPress shortcode regex {
	 *   @type string attribute name
	 *   @type mixed shortcode value
	 * }
	 *
	 * @return array cleaned up options ready for comparison {
	 *   @type string option name
	 *   @type string|bool option value
	 * }
	 */
	public static function sanitizeShortcodeParameters( $attributes = array() )
	{
		if ( ! is_array( $attributes ) ) {
			return array();
		}

		$options = array();

		// clean up Tweet ID
		if ( isset( $attributes['id'] ) ) {
			$tweet_id = static::sanitizeTweetID( (string) $attributes['id'] );
			if ( $tweet_id ) {
				$options['id'] = $tweet_id;
			}
			unset( $tweet_id );
		}

		// allow option style or oEmbed style parameter
		if ( isset( $attributes['status'] ) ) {
			if ( false === $attributes['status'] || '0' == $attributes['status'] || ( is_string( $attributes['status'] ) && in_array( strtolower( $attributes['status'] ), array( 'false', 'no', 'off' ) ) ) ) {
				$options['status'] = false;
			}
		} else if ( isset( $attributes['hide_tweet'] ) ) {
			if ( true === $attributes['hide_tweet'] || '1' == $attributes['hide_tweet'] || ( is_string( $attributes['hide_tweet'] ) && in_array( strtolower( $attributes['hide_tweet'] ), array( 'true', 'yes', 'on' ) ) ) ) {
				$options['status'] = false;
			}
		}

		return $options;
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attributes shortcode attributes
	 * @param string $content    shortcode content. no effect
	 *
	 * @return string HTML markup
	 */
	public static function shortcodeHandler( $attributes, $content = '' )
	{
		// clean up attribute to shortcode option mappings before passing to filter
		// apply the same filter as shortcode_atts
		/** This filter is documented in wp-includes/shortcodes.php */
		$options = apply_filters(
			'shortcode_atts_' . self::SHORTCODE_TAG,
			array_merge(
				static::$SHORTCODE_DEFAULTS,
				static::sanitizeShortcodeParameters( (array) $attributes )
			),
			static::$SHORTCODE_DEFAULTS,
			$attributes
		);

		if ( ! $options['id'] ) {
			return '';
		}
		$tweet_id = $options['id'];
		unset( $options['id'] );

		$oembed_params = static::shortcodeParamsToOEmbedParams( $tweet_id, $options );
		if ( empty( $oembed_params ) ) {
			return '';
		}

		// fetch HTML markup from Twitter oEmbed endpoint for the given parameters
		$html = trim( static::getOEmbedMarkup( $oembed_params ) );
		if ( ! $html ) {
			return '';
		}

		$html = '<div class="twitter-video">' . $html . '</div>';

		$inline_js = \Twitter\WordPress\JavaScriptLoaders\Widgets::enqueue();
		if ( $inline_js ) {
			return $html . $inline_js;
		}

		return $html;
	}

	/**
	 * Convert shortcode parameters into query parameters supported by the Twitter oEmbed endpoint
	 *
	 * @since 1.0.0
	 *
	 * @param string $tweet_id          Tweet identifier
	 * @param array  $shortcode_options customizations specified in the shortcode
	 *
	 * @return array associative array of query parameters ready for http_build_query
	 */
	public static function shortcodeParamsToOEmbedParams( $tweet_id, $shortcode_options = array() )
	{
		$query_parameters = static::getBaseOEmbedParams( $tweet_id );
		if ( empty( $query_parameters ) ) {
			return array();
		}
		$query_parameters['widget_type'] = 'video';

		if ( isset( $shortcode_options['status'] ) && false === $shortcode_options['status'] ) {
			$query_parameters['hide_tweet'] = true;
		}

		return $query_parameters;
	}

	/**
	 * Generate a unique string representing oEmbed result customizations set by shortcode parameters
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_parameters associative array of query parameters sent to the oEmbed endpoint {
	 *   @type string query parameter name
	 *   @type string|bool query parameter value
	 * }
	 *
	 * @return string cache key component
	 */
	public static function getOEmbedCacheKeyCustomParameters( array $query_parameters )
	{
		$customizations = '';

		if ( isset( $query_parameters['hide_tweet'] ) && $query_parameters['hide_tweet'] ) {
			$customizations .= 'h';
		}

		return $customizations;
	}
}
