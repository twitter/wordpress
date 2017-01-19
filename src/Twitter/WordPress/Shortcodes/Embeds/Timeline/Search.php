<?php
/*
The MIT License (MIT)

Copyright (c) 2017 Twitter Inc.

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

namespace Twitter\WordPress\Shortcodes\Embeds\Timeline;

/**
 * Display recent search results in a timeline
 *
 * @since 2.0.0
 */
class Search implements \Twitter\WordPress\Shortcodes\ShortcodeInterface
{
	use \Twitter\WordPress\Shortcodes\Embeds\Timeline;

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_search';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-timeline-search';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'widget_id' => '', 'terms' => '' );

	/**
	 * Reference the feature by name
	 *
	 * @since 2.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return _x( 'Twitter Search', 'Tweets matching a search result query', 'twitter' );
	}

	/**
	 * Attach handlers for shortcode, shortcode UI
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
	}

	/**
	 * Describe shortcode for Shortcake UI
	 *
	 * @since 2.0.0
	 *
	 * @link https://github.com/wp-shortcake/shortcake Shortcode UI
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
						'attr'  => 'widget_id',
						'label' => esc_html( __( 'Widget ID', 'twitter' ) ),
						'type'  => 'text',
						'meta'  => array(
							'pattern'     => '[0-9]+',
						),
					),
				),
			)
		);
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
	 * @return string HTML markup. empty string if parameter requirement not met or no profile info found
	 */
	public static function shortcodeHandler( $attributes, $content = '' )
	{
		$options = static::getShortcodeAttributes( $attributes );
		if ( isset( $options['widget_id'] ) ) {
			$options['widget_id'] = trim( $options['widget_id'] );
		} else {
			return '';
		}

		$timeline = \Twitter\Widgets\Embeds\Timeline\Search::fromArray( $options );
		if ( ! ( $timeline && $timeline->getWidgetID() ) ) {
			return '';
		}

		return static::getHTMLForTimeline( $timeline );
	}

	/**
	 * Get HTML markup for a timeline
	 *
	 * @since 2.0.0
	 *
	 * @param \Twitter\Widgets\Embeds\Timeline\Search $timeline timeline object
	 *
	 * @return string HTML markup or empty string if minimum requirements not met
	 */
	public static function getHTMLForTimeline( $timeline )
	{
		// verify passed parameter
		if ( ! is_a( $timeline, '\Twitter\Widgets\Embeds\Timeline\Search' ) ) {
			return '';
		}

		if ( $timeline->getSearchTerms() ) {
			$link_text = _x( 'Tweets about %s', 'Tweets about a term or topic', 'twitter' );
		} else {
			$link_text = static::featureName();
		}

		$html = $timeline->toHTML( $link_text, '\Twitter\WordPress\Helpers\HTMLBuilder' );
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
	 * oEmbed not supported for search timeline
	 *
	 * @since 2.0.0
	 *
	 * @param array $query_parameters not used
	 *
	 * @return string empty string
	 */
	public static function getOEmbedCacheKeyCustomParameters( array $query_parameters )
	{
		return '';
	}

	/**
	 * oEmbed not supported for search timeline
	 *
	 * @since 2.0.0
	 *
	 * @param string $id               not used
	 * @param array  $query_parameters not used
	 *
	 * @return string empty string
	 */
	public static function getOEmbedCacheKey( $id, array $query_parameters )
	{
		return '';
	}
}
