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

namespace Twitter\WordPress\Shortcodes\Embeds\Tweet;

/**
 * Display a video attached to a Tweet
 *
 * @since 2.0.0
 */
class Video extends \Twitter\WordPress\Shortcodes\Embeds\Tweet
{

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_video';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '' );

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-video';

	/**
	 * Tweet object class
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const TWEET_CLASS = '\Twitter\Widgets\Embeds\Tweet\Video';

	/**
	 * Attach handlers for Twitter embedded video
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		$classname = get_called_class();

		// register our shortcode and its handler
		add_shortcode( self::SHORTCODE_TAG, array( $classname, 'shortcodeHandler' ) );

		// Shortcode UI, if supported
		add_action(
			'register_shortcode_ui',
			array( $classname, 'shortcodeUI' ),
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
		return _x( 'Twitter Video', 'A single embedded Tweet shown with a video-specific template', 'twitter' );
	}

	/**
	 * Describe shortcode for Shortcake UI
	 *
	 * @since 1.1.0
	 *
	 * @link https://github.com/wp-shortcake/shortcake Shortcake UI
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
				'label'         => esc_html( static::featureName() ),
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
	 * @since 2.0.0
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

		return $options;
	}

	/**
	 * Convert shortcode parameters, attributes, and defaults into a clean set of Tweet parameters
	 *
	 * @since 2.0.0
	 *
	 * @param array $attributes set of shortcode attribute-value pairs or positional content matching the WordPress shortcode regex {
	 *   @type string|int attribute name or positional int
	 *   @type mixed shortcode value
	 * }
	 *
	 * @return array cleaned up options ready to be passed into a Tweet Video object {
	 *   @type string option name
	 *   @type string|bool option value
	 * }
	 */
	public static function shortcodeAttributesToTweetKeys( $attributes )
	{
		if ( ! is_array( $attributes ) || empty( $attributes ) ) {
			return array();
		}

		if ( isset( $attributes['id'] ) ) {
			$attributes['id'] = static::sanitizeTweetID( (string) $attributes['id'] );
		} else {
			return array();
		}
		if ( ! $attributes['id'] ) {
			return array();
		}

		return $attributes;
	}

	/**
	 * Generate a unique string representing oEmbed result customizations set by shortcode parameters
	 *
	 * @since 2.0.0
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
		return '';
	}
}
