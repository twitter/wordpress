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

namespace Twitter\WordPress\Shortcodes\Embeds;

/**
 * Display a Tweet
 *
 * @since 2.0.0
 */
class Tweet implements \Twitter\WordPress\Shortcodes\ShortcodeInterface, \Twitter\WordPress\Shortcodes\PublishOEmbedEndpoint
{
	use \Twitter\WordPress\Shortcodes\OEmbedTrait;

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'tweet';

	/**
	 * The oEmbed regex registered by WordPress Core
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const OEMBED_CORE_REGEX = '#https?://(www\.)?twitter\.com/.+?/status(es)?/.*#i';

	/**
	 * Regex used to match a Tweet in text
	 *
	 * More specific than WordPress Core regex
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const URL_REGEX = '#^https?://(www\.)?twitter\.com/[a-z0-9_]{1,20}/status(es)?/([0-9]+)#i';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-tweet';

	/**
	 * Tweet object class
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const TWEET_CLASS = '\Twitter\Widgets\Embeds\Tweet';

	/**
	 * Base URL used to reconstruct a Tweet URL
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const BASE_URL = 'https://twitter.com/_/status/';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array(
		'id'           => null,
		'conversation' => true,
		'cards'        => true,
		'width'        => null,
		'align'        => null,
		'theme'        => null,
		'link_color'   => null,
		'border_color' => null,
	);

	/**
	 * Extra parameters accepted by Jetpack included for compatibility
	 *
	 * @since 2.0.0
	 *
	 * @link https://github.com/Automattic/jetpack/blob/master/modules/shortcodes/tweet.php Jetpack tweet shortcode
	 *
	 * @type array
	 */
	public static $JETPACK_SHORTCODE_EXTRAS = array(
		'tweet'       => null,
		'hide_thread' => false,
		'hide_media'  => false,
	);

	/**
	 * Data attribute keys used in a Tweet object mapped to oEmbed keys used by Jetpack
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $DATA_ATTRIBUTES_TO_OEMBED_KEYS = array(
		'cards'        => 'hide_media',
		'conversation' => 'hide_thread',
	);

	/**
	 * Shortcode attributes to be converted from underscores to dashes before object initialization
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $OPTIONS_KEYS_UNDERSCORE_TO_DASHES = array(
		'link_color'   => 'link-color',
		'border_color' => 'border-color',
	);

	/**
	 * Attach handlers for embedded Tweets
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

		if ( ! is_admin() ) {
			// unhook the WordPress Core oEmbed handler
			wp_oembed_remove_provider( static::OEMBED_CORE_REGEX );
			// pass a Tweet detail URL through the Tweet shortcode handler
			wp_embed_register_handler(
				self::SHORTCODE_TAG,
				static::URL_REGEX,
				array( $classname, 'linkHandler' ),
				1
			);
		}
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
		return __( 'Embedded Tweet', 'twitter' );
	}

	/**
	 * Describe shortcode for Shortcake UI
	 *
	 * @since 2.0.0
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
	 * Handle a URL matched by an embed handler
	 *
	 * @since 2.0.0
	 *
	 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 *
	 * @return string HTML markup for the Tweet or an empty string if requirements not met
	 */
	public static function linkHandler( $matches )
	{
		if ( ! ( is_array( $matches ) && isset( $matches[3] ) && $matches[3] ) ) {
			return '';
		}

		return static::shortcodeHandler( array( 'id' => $matches[3] ) );
	}

	/**
	 * Convert a Tweet ID in ID or URL form into a trimmed ID
	 *
	 * @since 2.0.0
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
	 * Shortcode allowed attributes and default values Tweet
	 *
	 * Jetpack compatibility mixed in
	 *
	 * @since 2.0.0
	 *
	 * @return array shortcode allowed attributes and default values
	 */
	public static function getShortcodeDefaults()
	{
		return array_merge( static::$JETPACK_SHORTCODE_EXTRAS, static::$SHORTCODE_DEFAULTS );
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

		// support a Tweet ID or URL in the first position for compatibility with WordPress.com / Jetpack
		if ( empty( $options['id'] ) ) {
			// support tweet attribute for WordPress.com / Jetpack compatibility
			if ( ! empty( $options['tweet'] ) ) {
				$options['id'] = $options['tweet'];
			} else {
				// support Tweet ID or URL in the first position for compatibility with WordPress.com / Jetpack
				if ( ! empty( $attributes[0] ) ) {
					$options['id'] = $attributes[0];
					unset( $options['tweet'] );
				}
			}
		}

		return static::shortcodeAttributesToTweetKeys( $options );
	}

	/**
	 * Convert shortcode parameters, attributes, and defaults into a clean set of Tweet parameters
	 *
	 * @since 2.0.0
	 *
	 * @param array $attributes set of shortcode attribute-value pairs content matching the WordPress shortcode regex {
	 *   @type string|int attribute name or positional int
	 *   @type mixed shortcode value
	 * }
	 *
	 * @return array cleaned up options ready to be passed into a Tweet object {
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

		$attributes = \Twitter\WordPress\Shortcodes\Helpers\Attributes::booleanOption(
			$attributes,
			array( 'cards', 'hide_media', 'conversation', 'hide_thread' )
		);
		$attributes = \Twitter\WordPress\Shortcodes\Helpers\Attributes::positiveIntegerOption(
			$attributes,
			array( 'width' )
		);
		$attributes = \Twitter\WordPress\Shortcodes\Helpers\Attributes::lowercaseStringOption(
			$attributes,
			array( 'align', 'theme', 'link_color', 'border_color' )
		);

		// test for oEmbed-style parameter used by WordPress.com / Jetpack
		// if both attributes are used and have values prefer cards and conversation
		foreach ( static::$DATA_ATTRIBUTES_TO_OEMBED_KEYS as $option => $alternate_naming ) {
			if ( isset( $attributes[ $alternate_naming ] ) ) {
				if ( ! isset( $attributes[ $option ] ) && $attributes[ $alternate_naming ] !== static::$SHORTCODE_DEFAULTS[ $option ] ) {
					$attributes[ $option ] = $attributes[ $alternate_naming ];
				}
				unset( $attributes[ $alternate_naming ] );
			}
		}

		foreach ( static::$OPTIONS_KEYS_UNDERSCORE_TO_DASHES as $underscore => $dashes ) {
			if ( ! isset( $attributes[ $underscore ] ) ) {
				continue;
			}
			$attributes[ $dashes ] = $attributes[ $underscore ];
			unset( $attributes[ $underscore ] );
		}

		return $attributes;
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 2.0.0
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
		$options = static::getShortcodeAttributes( $attributes );

		$tweet_id = '';
		if ( isset( $options['id'] ) ) {
			$tweet_id = $options['id'];
		} else {
			// allow shortcode use in enclosed form
			$content = trim( $content );
			if ( $content ) {
				$tweet_id = static::sanitizeTweetID( $content );
			}
		}
		if ( ! $tweet_id ) {
			return '';
		}
		$options['id'] = $tweet_id;

		$object_class = static::TWEET_CLASS;
		if ( ! method_exists( $object_class, 'fromArray' ) ) {
		    return '';
		}
		$tweet = $object_class::fromArray( $options );
		unset( $object_class );
		if ( ! ($tweet && method_exists( $tweet, 'getID' ) && $tweet->getID() && method_exists( $tweet, 'toOEmbedParameterArray' ) ) ) {
			return '';
		}

		$oembed_params = $tweet->toOEmbedParameterArray();
		if ( empty( $oembed_params ) || ! isset( $oembed_params['url'] ) ) {
			return '';
		}
		$oembed_params = array_merge( static::getBaseOEmbedParams(), $oembed_params );

		$cache_key = static::getOEmbedCacheKey( $tweet_id, $oembed_params );
		if ( ! $cache_key ) {
			return '';
		}

		// fetch HTML markup from Twitter oEmbed endpoint for the given parameters
		$html = trim( static::getOEmbedMarkup( $oembed_params, $cache_key ) );
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
		$key_pieces = array();

		if ( isset( $query_parameters['maxwidth'] ) ) {
			$key_pieces[] = 'w' . $query_parameters['maxwidth'];
		}

		// one letter customizations
		$customizations = '';
		if ( isset( $query_parameters['hide_media'] ) && $query_parameters['hide_media'] ) {
			$customizations .= 'm';
		}
		if ( isset( $query_parameters['hide_thread'] ) && $query_parameters['hide_thread'] ) {
			$customizations .= 't';
		}
		// should only be set for dark theme
		if ( isset( $query_parameters['theme'] ) ) {
			$customizations .= 'd';
		}
		// left, right, center
		if ( isset( $query_parameters['align'] ) && $query_parameters['align'] ) {
			$customizations .= substr( $query_parameters['align'], 0, 1 );
		}
		if ( $customizations ) {
			$key_pieces[] = $customizations;
		}
		unset( $customizations );

		$color_parameters = array(
			'link_color'   => 'l',
			'border_color' => 'b',
		);
		foreach ( $color_parameters as $query_parameter => $cache_abbreviation ) {
			if ( isset( $query_parameters[ $query_parameter ] ) ) {
				$key_pieces[] = $cache_abbreviation . ltrim( $query_parameters[ $query_parameter ], '#' );
			}
		}

		return implode( '_', $key_pieces );
	}

	/**
	 * Construct a cache key for the oEmbed response. Account for query parameters needing to bust cache
	 *
	 * @since 2.0.0
	 *
	 * @link https://dev.twitter.com/rest/reference/get/statuses/oembed oEmbed doc
	 *
	 * @param string $id               Tweet ID
	 * @param array  $query_parameters oEmbed API query parameters
	 *
	 * @return string cache key
	 */
	public static function getOEmbedCacheKey( $id, array $query_parameters )
	{
		if ( ! ( is_string( $id ) && $id ) ) {
			return '';
		}
		$key_pieces = array( static::SHORTCODE_TAG, $id );

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
