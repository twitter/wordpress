<?php
/*
The MIT License (MIT)

Copyright (c) 2016 Twitter Inc.

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
 * Common references for a Twitter timeline
 *
 * @since 2.0.0
 */
trait Timeline
{

	/**
	 * Accepted timeline shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $TIMELINE_SHORTCODE_DEFAULTS = array(
		'width' => null,
		'height' => null,
		'limit' => null,
		'chrome' => array(),
		'aria_polite' => 'polite',
		'lang' => null,
		'theme' => 'light',
		'link_color' => null,
		'border_color' => null,
	);

	/**
	 * Shortcode attributes to be converted from underscores to dashes before object initialization
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $OPTIONS_KEYS_UNDERSCORE_TO_DASHES = array(
		'aria_polite' => 'aria-polite',
		'link_color'  => 'link-color',
		'border_color' => 'border-color',
	);

	/**
	 * Shortcode attributes expected to be a positive integer value
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $OPTIONS_KEYS_INT_VALUES = array(
		'width',
		'height',
		'limit',
	);

	/**
	 * Attach handlers for shortcode, oEmbed URL, shortcode UI
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		$classname = get_called_class();

		// register our shortcode and its handler
		add_shortcode( static::SHORTCODE_TAG, array( $classname, 'shortcodeHandler' ) );

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

			// convert a URL into the shortcode equivalent
			wp_embed_register_handler(
				static::SHORTCODE_TAG,
				static::URL_REGEX,
				array( $classname, 'linkHandler' ),
				1
			);
		}
	}

	/**
	 * Shortcode allowed attributes and default values for embedded timeline
	 *
	 * @since 2.0.0
	 *
	 * @return array shortcode allowed attributes and default values
	 */
	public static function getShortcodeDefaults()
	{
		return array_merge( static::$TIMELINE_SHORTCODE_DEFAULTS, static::$SHORTCODE_DEFAULTS );
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
	 * @return array array processed by shortcode_atts, prepped for timeline objects
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

		return static::shortcodeAttributesToTimelineKeys( $options );
	}

	/**
	 * Convert shortcode attributes provided as underscores to the dashed values expected by the object builder
	 *
	 * @since 2.0.0
	 *
	 * @param array $options shortcode options array {
	 *   @type string shortcode attribute
	 *   @type mixed  shortcode attribute value
	 * }
	 *
	 * @return array shortcode attributes converted to keys and value types expected by timeline objects
	 */
	public static function shortcodeAttributesToTimelineKeys( $options )
	{
		if ( ! is_array( $options ) ) {
			return array();
		}

		// expected int values
		$options = \Twitter\WordPress\Shortcodes\Helpers\Attributes::positiveIntegerOption(
			$options,
			static::$OPTIONS_KEYS_INT_VALUES
		);

		foreach ( static::$OPTIONS_KEYS_UNDERSCORE_TO_DASHES as $underscore => $dashes ) {
			if ( ! isset( $options[ $underscore ] ) ) {
				continue;
			}
			$options[ $dashes ] = $options[ $underscore ];
			unset( $options[ $underscore ] );
		}

		// set multiple chrome tokens through CSV
		if ( isset( $options['chrome'] ) && ! is_array( $options['chrome'] ) ) {
			$options['chrome'] = explode( ',', $options['chrome'] );
		}

		return $options;
	}

	/**
	 * Generate a unique string representing oEmbed result customizations
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
		if ( empty( $query_parameters ) ) {
			return '';
		}

		$key_pieces = array();

		if ( isset( $query_parameters['maxwidth'] ) ) {
			$key_pieces[] = 'w' . $query_parameters['maxwidth'];
		}
		if ( isset( $query_parameters['limit'] ) ) {
			$key_pieces[] = 'l' . $query_parameters['limit'];
		} else if ( isset( $query_parameters['maxheight'] ) ) {
			$key_pieces[] = 'h' . $query_parameters['maxheight'];
		}

		if ( isset( $query_parameters['chrome'] ) && $query_parameters['chrome'] ) {
			$chrome = explode( ' ', $query_parameters['chrome'] );
			$chrome_piece = array();
			$abbreviated_chrome_tokens = array(
				\Twitter\Widgets\Embeds\Timeline::CHROME_NOHEADER => 'h',
				\Twitter\Widgets\Embeds\Timeline::CHROME_NOFOOTER => 'f',
				\Twitter\Widgets\Embeds\Timeline::CHROME_NOBORDERS => 'b',
				\Twitter\Widgets\Embeds\Timeline::CHROME_NOSCROLLBAR => 's',
				\Twitter\Widgets\Embeds\Timeline::CHROME_TRANSPARENT => 't',
			);
			foreach ( $chrome as $token ) {
				if ( isset( $abbreviated_chrome_tokens[ $token ] ) ) {
					$chrome_piece[ $abbreviated_chrome_tokens[ $token ] ] = true;
				}
			}
			if ( ! empty( $chrome_piece ) ) {
				$key_pieces[] = implode( '', array_keys( $chrome_piece ) );
			}
			unset( $chrome_piece );
			unset( $chrome );
		}

		// ARIA live region override: assertive
		if ( isset( $query_parameters['aria_polite'] ) ) {
			$key_pieces[] = 'a';
		}

		// should only be set for dark theme
		if ( isset( $query_parameters['theme'] ) ) {
			$key_pieces[] = 'd';
		}

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
	 * Construct a cache key for the oEmbed response. Account for query parameters needing to bust cache. 172 characters or fewer
	 *
	 * @since 2.0.0
	 *
	 * @link https://dev.twitter.com/web/embedded-timelines/oembed embedded timelines oEmbed
	 *
	 * @param string $id               datasource ID
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

		if ( isset( $query_parameters['lang'] ) ) {
			$key_pieces[] = $query_parameters['lang'];
		}

		if ( isset( $query_parameters['dnt'] ) ) {
			$key_pieces[] = 'p';
		}

		$customizations = static::getOEmbedCacheKeyCustomParameters( $query_parameters );
		if ( $customizations ) {
			$key_pieces[] = $customizations;
		}

		return implode( '_', $key_pieces );
	}

	/**
	 * Get HTML markup for a timeline
	 *
	 * @since 2.0.0
	 *
	 * @param \Twitter\Widgets\Embeds\Timeline $timeline timeline object
	 *
	 * @return string HTML markup or empty string if minimum requirements not met
	 */
	public static function getHTMLForTimeline( $timeline )
	{
		// verify passed parameter
		if ( ! is_a( $timeline, '\Twitter\Widgets\Embeds\Timeline' ) ) {
			return '';
		}

		$oembed_params = $timeline->toOEmbedParameterArray();
		if ( ! isset( $oembed_params['url'] ) ) {
			return '';
		}
		$oembed_params = array_merge( static::getBaseOEmbedParams(), $oembed_params );

		$cache_key = static::getOEmbedCacheKey( static::getDataSourceIdentifier( $timeline ), $oembed_params );
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
}
