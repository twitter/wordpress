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
 * Display a Moment
 *
 * @since 1.2.0
 */
class Moment
{

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.2.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_moment';

	/**
	 * Relative path for the oEmbed API relative to Twitter publishers base path
	 *
	 * @since 1.2.0
	 *
	 * @type string
	 */
	const OEMBED_API_ENDPOINT = 'oembed';

	/**
	 * Regex used to match a Moment in text
	 *
	 * @since 1.2.0
	 *
	 * @type string
	 */
	const URL_REGEX = '#^https://twitter\.com/i/moments/([0-9]+)#i';

	/**
	 * Base URL used to reconstruct a Moment URL
	 *
	 * @since 1.2.0
	 *
	 * @type string
	 */
	const BASE_URL = 'https://twitter.com/i/moments/';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.2.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '' );

	/**
	 * Attach handlers for Twitter Moment
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public static function init()
	{
		// register our shortcode and its handler
		add_shortcode( self::SHORTCODE_TAG, array( __CLASS__, 'shortcodeHandler' ) );

		wp_embed_register_handler(
			self::SHORTCODE_TAG,
			static::URL_REGEX,
			array( __CLASS__, 'linkHandler' ),
			1
		);

		if ( is_admin() ) {
			// Shortcake UI
			if ( function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
				add_action(
					'admin_init',
					array( __CLASS__, 'shortcodeUI' ),
					5,
					0
				);
			}
		}
	}

	/**
	 * Describe shortcode for Shortcake UI
	 *
	 * @since 1.2.0
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
			self::SHORTCODE_TAG,
			array(
				'label'         => __( 'Twitter Moment', 'twitter' ),
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
				),
			)
		);
	}

	/**
	 * Handle a URL matched by a Moment embed handler
	 *
	 * @since 1.2.0
	 *
	 * @param array  $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 * @param array  $attr    Embed attributes. Not used.
	 * @param string $url     The original URL that was matched by the regex. Not used.
	 * @param array  $rawattr The original unmodified attributes. Not used.
	 *
	 * @return string HTML markup for the Tweet or an empty string if requirements not met
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
	 * @since 1.2.0
	 *
	 * @param array  $attributes set of shortcode attribute-value pairs or positional content matching the WordPress shortcode regex {
	 *   @type string|int attribute name or positional int
	 *   @type mixed      shortcode value
	 * }
	 * @param string $content    content inside a shortcode macro. no effect on this shortcode
	 *
	 * @return string HTML markup. empty string if parameter requirement not met or no Moment info found
	 */
	public static function shortcodeHandler( $attributes, $content = '' )
	{
		$options = shortcode_atts(
			static::$SHORTCODE_DEFAULTS,
			$attributes,
			static::SHORTCODE_TAG
		);

		$moment_id = trim( $options['id'] );
		if ( ! $moment_id ) {
			return '';
		}
		$query_parameters = static::getBaseOEmbedParams( $moment_id );

		// fetch HTML markup from Twitter oEmbed endpoint for the given parameters
		$html = trim( static::getOEmbedMarkup( $query_parameters ) );
		if ( ! $html ) {
			return '';
		}

		$html = '<div class="twitter-moment">' . $html . '</div>';

		$inline_js = \Twitter\WordPress\JavaScriptLoaders\Widgets::enqueue();
		if ( $inline_js ) {
			return $html . $inline_js;
		}

		return $html;
	}

	/**
	 * Get the base set of oEmbed params before applying shortcode customizations
	 *
	 * @since 1.2.0
	 *
	 * @param string $moment_id Moment identifier
	 *
	 * @return array associative array of query parameters ready for http_build_query {
	 *   @type string query parameter name
	 *   @type string|bool query parameter value
	 * }
	 */
	public static function getBaseOEmbedParams( $moment_id )
	{
		$moment_id = trim( $moment_id );
		if ( ! $moment_id ) {
			return array();
		}

		// omit JavaScript. enqueue separately
		$query_parameters = array(
			'id' => $moment_id,
			'omit_script' => true,
		);

		// attempt to customize text for site language
		$lang = \Twitter\WordPress\Language::localeToTwitterLang();
		if ( $lang ) {
			$query_parameters['lang'] = $lang;
		}

		return $query_parameters;
	}

	/**
	 * Construct a cache key for the oEmbed response. Account for query parameters needing to bust cache
	 *
	 * @since 1.2.0
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

		$key_pieces = array( self::SHORTCODE_TAG, $query_parameters['id'] );

		// separate cache for each explicitly-defined display language
		if ( isset( $query_parameters['lang'] ) && $query_parameters['lang'] ) {
			$key_pieces[] = $query_parameters['lang'];
		}

		return implode( '_', $key_pieces );
	}

	/**
	 * Request and parse Moment oEmbed markup from Twitter's servers
	 *
	 * @since 1.2.0
	 *
	 * @param string $moment_id Moment identifier
	 *
	 * @todo make this not mocked
	 * @return string HTML markup returned by the oEmbed endpoint
	 */
	public static function getOEmbedMarkup( $query_parameters ) {
		$cache_key = static::oEmbedCacheKey( $query_parameters );
		if ( ! $cache_key ) {
			return '';
		}

		$cache_key = static::oEmbedCacheKey( $query_parameters );
		if ( ! $cache_key ) {
			return '';
		}

		// check for cached result
		$html = get_transient( $cache_key );
		if ( $html ) {
			return $html;
		}

		$ttl = DAY_IN_SECONDS;

		// convert ID to full URL to allow more flexibility for oEmbed endpoint as embed types expand
		$query_parameters['url'] = self::BASE_URL . $query_parameters['id'];
		unset( $query_parameters['id'] );

		$oembed_response = \Twitter\WordPress\Helpers\TwitterOEmbed::getJSON( self::OEMBED_API_ENDPOINT, $query_parameters );
		if ( ! $oembed_response || ! isset( $oembed_response->type ) || 'rich' !== $oembed_response->type || ! ( isset( $oembed_response->html ) && $oembed_response->html ) ) {
			// do not rerequest errors with every page request
			set_transient( $cache_key, ' ', $ttl );
			return '';
		}

		$html = $oembed_response->html;

		if ( isset( $oembed_response->cache_age ) ) {
			$ttl = absint( $oembed_response->cache_age );
		}
		set_transient( $cache_key, $html, $ttl );

		return $html;
	}
}
