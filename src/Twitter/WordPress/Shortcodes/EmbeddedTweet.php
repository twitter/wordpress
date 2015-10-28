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
 * Display a Tweet
 *
 * @since 1.0.0
 */
class EmbeddedTweet implements ShortcodeInterface
{
	use OEmbedTrait;

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'tweet';

	/**
	 * PHP class to use for fetching oEmbed data
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const OEMBED_API_CLASS = '\Twitter\WordPress\Helpers\TwitterAPI';

	/**
	 * Relative path for the oEmbed API relative to Twitter API base path
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const OEMBED_API_ENDPOINT = 'statuses/oembed';

	/**
	 * oEmbed regex registered by WordPress Core
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const OEMBED_CORE_REGEX = '#https?://(www\.)?twitter\.com/.+?/status(es)?/.*#i';

	/**
	 * Regex used to match a Tweet in text
	 *
	 * More specific than WordPress Core regex
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const TWEET_URL_REGEX = '#^https?://(www\.)?twitter\.com/[a-z0-9_]{1,20}/status(es)?/([0-9]+)#i';

	/**
	 * Base URL used to reconstruct a Tweet URL
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const BASE_URL = 'https://twitter.com/_/status/';

	/**
	 * Accepted values for the align parameter
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	public static $ALIGN_OPTIONS = array( 'left' => true, 'center' => true, 'right' => true );

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '', 'conversation' => true, 'cards' => true, 'align' => '' );

	/**
	 * Attach handlers for embedded Tweets
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
	
		if ( ! is_admin() ) {
			// unhook the WordPress Core oEmbed handler
			wp_oembed_remove_provider( static::OEMBED_CORE_REGEX );
			// pass a Tweet detail URL through the Tweet shortcode handler
			wp_embed_register_handler(
				self::SHORTCODE_TAG,
				static::TWEET_URL_REGEX,
				array( __CLASS__, 'linkHandler' ),
				1
			);
		}
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
				'label'         => esc_html( __( 'Embedded Tweet', 'twitter' ) ),
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
	 * Handle a URL matched by an embed handler
	 *
	 * @since 1.0.0
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
		if ( ! ( is_array( $matches ) && isset( $matches[3] ) && $matches[3] ) ) {
			return '';
		}

		return static::shortcodeHandler( array( 'id' => $matches[3] ) );
	}

	/**
	 * Convert a Tweet ID in ID or URL form into a trimmed ID
	 *
	 * @since 1.0.0
	 *
	 * @param string $tweet_id Tweet identifier
	 *
	 * @return string $tweet_id Tweet identifier or empty string if minimum requirements not met
	 */
	public static function sanitizeTweetID( $tweet_id )
	{
		if ( ! is_string( $tweet_id ) ) {
			return '';
		}

		$tweet_id = trim( $tweet_id );
		if ( ! $tweet_id ) {
			return '';
		}
		$tweet_id = trim( rtrim( trim( $tweet_id ), '/' ) );
		if ( ! $tweet_id ) {
			return '';
		}

		$last_slash = strrpos( $tweet_id, '/' );
		if ( false !== $last_slash ) {
			$tweet_id = substr( $tweet_id, $last_slash + 1 );
		}

		return $tweet_id;
	}

	/**
	 * Convert shortcode parameters, attributes, and defaults into a clean set of Tweet parameters
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes set of shortcode attribute-value pairs or positional content matching the WordPress shortcode regex {
	 *   @type string|int attribute name or positional int
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

		if ( isset( $attributes['id'] ) ) {
			$tweet_id = static::sanitizeTweetID( (string) $attributes['id'] );
			if ( $tweet_id ) {
				$options['id'] = $tweet_id;
			}
			unset( $tweet_id );
		} else if ( isset( $attributes[0] ) ) {
			// compatibility with WordPress.com positional shortcode
			$tweet_id = static::sanitizeTweetID( (string) $attributes[0] );
			if ( $tweet_id ) {
				$options['id'] = $tweet_id;
			}
			unset( $tweet_id );
		}

		foreach ( array( 'cards' => 'hide_media', 'conversation' => 'hide_thread' ) as $falsey_option => $alternate_naming ) {
			// check for falsey values passed to shortcode
			if ( isset( $attributes[ $falsey_option ] ) ) {
				if ( false === $attributes[ $falsey_option ] || '0' == $attributes[ $falsey_option ] || ( is_string( $attributes[ $falsey_option ] ) && in_array( strtolower( $attributes[ $falsey_option ] ), array( 'false', 'no', 'off' ) ) ) ) {
					$options[ $falsey_option ] = false;
				}
			} else if ( isset( $attributes[ $alternate_naming ] ) ) {
				// test for an oEmbed-style parameter provided in the shortcode if the equivalent parameter was not defined
				if ( true === $attributes[ $alternate_naming ] || '1' == $attributes[ $alternate_naming ] || ( is_string( $attributes[ $alternate_naming ] ) && in_array( strtolower( $attributes[ $alternate_naming ] ), array( 'true', 'yes', 'on' ) ) ) ) {
					// test alternate attribute used by other shortcodes
					$options[ $falsey_option ] = false;
				}
			}
		}

		if ( isset( $attributes['align'] ) && $attributes['align'] ) {
			$attributes['align'] = trim( strtolower( $attributes['align'] ) );
			if ( array_key_exists( $attributes['align'], static::$ALIGN_OPTIONS ) ) {
				$options['align'] = $attributes['align'];
			}
		}

		return $options;
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attributes set of shortcode attribute-value pairs or positional content matching the WordPress shortcode regex {
	 *   @type string|int attribute name or positional int
	 *   @type mixed      shortcode value
	 * }
	 * @param string $content    content inside a shortcode macro. no effect on this shortcode
	 *
	 * @return string HTML markup. empty string if parameter requirement not met or no Tweet info found
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

		$html = '<div class="twitter-tweet">' . $html . '</div>';

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
	 * @return array associative array of query parameters ready for http_build_query {
	 *   @type string query parameter name
	 *   @type string|bool query parameter value
	 * }
	 */
	public static function shortcodeParamsToOEmbedParams( $tweet_id, $shortcode_options = array() )
	{
		$query_parameters = static::getBaseOEmbedParams( $tweet_id );
		if ( empty( $query_parameters ) ) {
			return array();
		}

		// test for valid align value
		if ( isset( $shortcode_options['align'] ) && $shortcode_options['align'] && array_key_exists( $shortcode_options['align'], static::$ALIGN_OPTIONS ) ) {
			$query_parameters['align'] = $shortcode_options['align'];
		}

		// oembed parameters are the opposite of widget parameters
		// hide_* in oEmbed API
		foreach ( array( 'cards' => 'hide_media', 'conversation' => 'hide_thread' ) as $bool_option => $oembed_parameter ) {
			if ( isset( $shortcode_options[ $bool_option ] ) && false === $shortcode_options[ $bool_option ] ) {
				$query_parameters[ $oembed_parameter ] = true;
			}
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

		if ( isset( $query_parameters['hide_media'] ) && $query_parameters['hide_media'] ) {
			$customizations .= 'm';
		}
		if ( isset( $query_parameters['hide_thread'] ) && $query_parameters['hide_thread'] ) {
			$customizations .= 't';
		}
		// left, right, center
		if ( isset( $query_parameters['align'] ) && $query_parameters['align'] && array_key_exists( $query_parameters['align'], static::$ALIGN_OPTIONS ) ) {
			$customizations .= substr( $query_parameters['align'], 0, 1 );
		}

		return $customizations;
	}

	/**
	 * Construct a cache key for the oEmbed response. Account for query parameters needing to bust cache
	 *
	 * @since 1.0.0
	 *
	 * @link https://dev.twitter.com/rest/reference/get/statuses/oembed oEmbed doc
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

		$customizations = static::getOEmbedCacheKeyCustomParameters( $query_parameters );
		if ( $customizations ) {
			$key_pieces[] = $customizations;
		}

		return implode( '_', $key_pieces );
	}
}
