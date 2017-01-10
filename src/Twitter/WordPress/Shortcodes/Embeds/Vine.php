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
 * Display a Vine
 *
 * @since 2.0.0
 */
class Vine implements \Twitter\WordPress\Shortcodes\ShortcodeInterface
{
	use \Twitter\WordPress\Shortcodes\OEmbedTrait;

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'vine';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'vine-embed';

	/**
	 * Regex used to match a Vine in text
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const URL_REGEX = '#https?://vine\.co/v/([a-z0-9]+)\/?#i';

	/**
	 * The oEmbed regex registered by WordPress Core since 4.1
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const OEMBED_CORE_REGEX = '#https?://vine.co/v/.*#i';

	/**
	 * Base URL used to reconstruct a Vine URL
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const BASE_URL = 'https://vine.co/v/';

	/**
	 * PHP class to use for fetching oEmbed data
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const OEMBED_API_CLASS = '\Twitter\WordPress\Helpers\VineOEmbed';

	/**
	 * Relative path for the oEmbed API relative to Vine base path
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const OEMBED_API_ENDPOINT = 'oembed';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '', 'width' => 0 );

	/**
	 * Attach handlers for Vine
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

		// unhook the WordPress Core oEmbed handler
		wp_oembed_remove_provider( static::OEMBED_CORE_REGEX );

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
	 * @since 2.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return 'Vine';
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

		shortcode_ui_register_for_shortcode(
			static::SHORTCODE_TAG,
			array(
				'label'         => esc_html( static::featureName() ),
				'listItemImage' => 'dashicons-video-alt3',
				'attrs'         => array(
					array(
						'attr'  => 'id',
						'label' => 'ID',
						'type'  => 'text',
						'meta'  => array(
							'required'    => true,
							'pattern'     => '[A-Za-z0-9]+',
						),
					),
				),
			)
		);
	}

	/**
	 * Handle a URL matched by a embed handler
	 *
	 * @since 2.0.0
	 *
	 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 *
	 * @return string HTML markup for the Vine or an empty string if requirements not met
	 */
	public static function linkHandler( $matches )
	{
		if ( ! ( is_array( $matches ) && isset( $matches[1] ) && $matches[1] ) ) {
			return '';
		}

		return static::shortcodeHandler( array( 'id' => $matches[1] ) );
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
			$options['id'] = trim( $options['id'] );
		}

		$options = \Twitter\WordPress\Shortcodes\Helpers\Attributes::positiveIntegerOption(
			$options,
			array( 'width' )
		);

		$width = 0;
		if ( isset( $options['width'] ) ) {
			$width = absint( $options['width'] );
			if ( $width < 100 && isset( $content_width ) ) {
				$width = absint( $content_width );
			}
		}
		if ( $width > 100 ) {
			// reset max_width to max value supported by Vine
			// collapses cache hits
			if ( $width > 600 ) {
				$width = 600;
			}
			$options['width'] = $width;
		}

		return $options;
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
	 * @return string HTML markup. empty string if parameter requirement not met or no collection info found
	 */
	public static function shortcodeHandler( $attributes, $content = '' )
	{
		$options = static::getShortcodeAttributes( $attributes );

		if ( ! ( isset( $options['id'] ) && $options['id'] ) ) {
			return '';
		}

		$vine = \Twitter\Widgets\Embeds\Vine::fromArray( $options );

		if ( ! $vine->getWidth() ) {
			// set to content width of the current theme
			// @see https://codex.wordpress.org/Content_Width
			if ( isset( $GLOBALS['content_width'] ) && $GLOBALS['content_width'] ) {
				$vine->setWidth( absint( $GLOBALS['content_width'] ) );
			}
		}

		$query_parameters = $vine->toOEmbedParameterArray();
		$query_parameters = array_merge( static::getBaseOEmbedParams(), $query_parameters );

		// not used by Vine
		unset( $query_parameters['lang'] );

		// fetch HTML markup from Vine oEmbed endpoint for the given parameters
		$html = trim( static::getOEmbedMarkup( $query_parameters, static::getOEmbedCacheKey( $options['id'], $query_parameters ) ) );
		if ( ! $html ) {
			return '';
		}

		$html = '<div class="' . sanitize_html_class( static::HTML_CLASS ) . '">' . $html . '</div>';

		$inline_js = \Twitter\WordPress\JavaScriptLoaders\VineEmbed::enqueue();
		if ( $inline_js ) {
			return $html . $inline_js;
		}

		return $html;
	}

	/**
	 * Construct a cache key for the oEmbed response. Account for query parameters needing to bust cache
	 *
	 * @since 2.0.0
	 *
	 * @param string $id               Vine ID
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

		if ( isset( $query_parameters['maxwidth'] ) && $query_parameters['maxwidth'] ) {
			$key_pieces[] = 'w' . $query_parameters['maxwidth'];
		}

		return implode( '_', $key_pieces );
	}
}
