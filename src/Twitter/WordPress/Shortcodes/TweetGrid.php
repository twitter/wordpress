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
 * Display a grid of Tweets
 *
 * @since 1.3.0
 */
class TweetGrid implements ShortcodeInterface
{
	use OEmbedTrait;

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_grid';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-grid';

	/**
	 * Regex used to match a Collection in text
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const URL_REGEX = '#^https://twitter\.com/[a-z0-9_]{1,20}/timelines/([0-9]+)#i';

	/**
	 * Base URL used to reconstruct a Collection URL
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const BASE_URL = 'https://twitter.com/_/timelines/';

	/**
	 * PHP class to use for fetching oEmbed data
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const OEMBED_API_CLASS = '\Twitter\WordPress\Helpers\TwitterOEmbed';

	/**
	 * Relative path for the oEmbed API relative to Twitter publishers base path
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const OEMBED_API_ENDPOINT = 'oembed';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.3.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '', 'limit' => 20 );

	/**
	 * Attach handlers for Twitter Grid
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public static function init()
	{
		$classname = get_called_class();

		// register our shortcode and its handler
		add_shortcode( static::SHORTCODE_TAG, array( $classname, 'shortcodeHandler' ) );

		// convert a URL into the shortcode equivalent
		wp_embed_register_handler(
			static::SHORTCODE_TAG,
			static::URL_REGEX,
			array( $classname, 'linkHandler' ),
			1
		);

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
	 * @since 1.3.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return _x( 'Tweet Grid', 'Tweets displayed in a grid layout', 'twitter' );
	}

	/**
	 * Describe shortcode for Shortcake UI
	 *
	 * @since 1.3.0
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
						'type'  => 'text',
						'meta'  => array(
							'required'    => true,
							'pattern'     => '[0-9]+',
						),
					),
					array(
						'attr'  => 'limit',
						'label' => _x( 'Limit', 'Maximum number of items to include', 'twitter' ),
						'type'  => 'number',
						'value' => static::$SHORTCODE_DEFAULTS['limit'],
						'meta'  => array(
							'min'  => 1,
							'max'  => 20,
							'step' => 1,
						),
					),
				),
			)
		);
	}

	/**
	 * Handle a URL matched by a embed handler
	 *
	 * @since 1.3.0
	 *
	 * @param array  $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 * @param array  $attr    Embed attributes. Not used.
	 * @param string $url     The original URL that was matched by the regex. Not used.
	 * @param array  $rawattr The original unmodified attributes. Not used.
	 *
	 * @return string HTML markup for the Tweet grid or an empty string if requirements not met
	 */
	public static function linkHandler( $matches, $attr, $url, $rawattr )
	{
		if ( ! ( is_array( $matches ) && isset( $matches[1] ) && $matches[1] ) ) {
			return '';
		}

		return static::shortcodeHandler( array( 'id' => $matches[1] ) );
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 1.3.0
	 *
	 * @param array  $attributes set of shortcode attribute-value pairs or positional content matching the WordPress shortcode regex {
	 *   @type string|int attribute name or positional int
	 *   @type mixed      shortcode value
	 * }
	 * @param string $content    content inside a shortcode macro. no effect on this shortcode
	 *
	 * @return string HTML markup. empty string if parameter requirement not met or no collection info found
	 */
	public static function shortcodeHandler( $attributes, $content = '' )
	{
		$options = shortcode_atts(
			static::$SHORTCODE_DEFAULTS,
			$attributes,
			static::SHORTCODE_TAG
		);

		$snowflake_id = trim( $options['id'] );
		if ( ! $snowflake_id ) {
			return '';
		}

		$query_parameters = static::getBaseOEmbedParams( $snowflake_id );
		if ( isset( $options['limit'] ) ) {
			$limit = 0;
			try {
				$limit = intval( trim( $options['limit'] ), 10 );
			} catch( Exception $e ) {}
			if ( $limit > 0 && $limit < 20 ) {
				$query_parameters['limit'] = $limit;
			}
		}

		// fetch HTML markup from Twitter oEmbed endpoint for the given parameters
		$html = trim( static::getOEmbedMarkup( $query_parameters ) );
		if ( ! $html ) {
			return '';
		}

		$html = '<div class="' . sanitize_html_class( static::HTML_CLASS ) . '">' . $html . '</div>';

		$inline_js = \Twitter\WordPress\JavaScriptLoaders\Widgets::enqueue();
		if ( $inline_js ) {
			return $html . $inline_js;
		}

		return $html;
	}

	/**
	 * Construct a cache key for the oEmbed response. Account for query parameters needing to bust cache
	 *
	 * @since 1.3.0
	 *
	 * @param array $query_parameters oEmbed API query parameters
	 *
	 * @return string cache key
	 */
	public static function oEmbedCacheKey( array $query_parameters )
	{
		if ( ! ( isset( $query_parameters['id'] ) && $query_parameters['id'] ) ) {
			return '';
		}

		$key_pieces = array( static::SHORTCODE_TAG, $query_parameters['id'] );

		// separate cache for each explicitly-defined display language
		if ( isset( $query_parameters['lang'] ) && $query_parameters['lang'] ) {
			$key_pieces[] = $query_parameters['lang'];
		}

		if ( isset( $query_parameters['limit'] ) && $query_parameters['limit'] ) {
			$key_pieces[] = $query_parameters['limit'];
		}

		return implode( '_', $key_pieces );
	}
}
