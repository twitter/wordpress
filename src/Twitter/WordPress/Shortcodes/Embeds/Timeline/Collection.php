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

namespace Twitter\WordPress\Shortcodes\Embeds\Timeline;

/**
 * Display a collection of Tweets
 *
 * @since 2.0.0
 */
class Collection implements \Twitter\WordPress\Shortcodes\ShortcodeInterface, \Twitter\WordPress\Shortcodes\PublishOEmbedEndpoint
{
	use \Twitter\WordPress\Shortcodes\OEmbedTrait, \Twitter\WordPress\Shortcodes\Embeds\Timeline;

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_collection';

	/**
	 * The oEmbed regex registered by WordPress Core
	 *
	 * @since 1.5.0
	 *
	 * @type string
	 */
	const OEMBED_CORE_REGEX = '#https?://(www\.)?twitter\.com/\w{1,15}/timelines/.*#i';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-collection';

	/**
	 * Regex used to match a Collection in text
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const URL_REGEX = '#^https://twitter\.com/[a-z0-9_]{1,20}/timelines/([0-9]+)#i';

	/**
	 * Base URL used to reconstruct a Collection URL
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const BASE_URL = 'https://twitter.com/_/timelines/';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '', 'template' => '' );

	/**
	 * Reference the feature by name
	 *
	 * @since 2.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return _x( 'Twitter Collection', 'Tweets organized into a collection', 'twitter' );
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
	 * @since 2.0.0
	 *
	 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 *
	 * @return string HTML markup for the Twitter Collection or an empty string if requirements not met
	 */
	public static function linkHandler( $matches )
	{
		if ( ! ( is_array( $matches ) && isset( $matches[1] ) && $matches[1] ) ) {
			return '';
		}

		return static::shortcodeHandler( array( 'id' => $matches[1] ) );
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
		// allow grid displays to be triggered from the main collection shortcode, handled by the compatibility layer of the grid shortcode
		if ( isset( $attributes['template'] ) && \Twitter\Widgets\Embeds\Timeline\Collection::WIDGET_TYPE_GRID === $attributes['template'] ) {
			return \Twitter\WordPress\Shortcodes\Embeds\Timeline\CollectionGrid::shortcodeHandler( $attributes, $content );
		}
		$options = static::getShortcodeAttributes( $attributes );
		// collection ID required
		if ( ! $options['id'] ) {
			return '';
		}

		$timeline = \Twitter\Widgets\Embeds\Timeline\Collection::fromArray( $options );
		if ( ! ( $timeline && $timeline->getID() ) ) {
			return '';
		}

		return static::getHTMLForTimeline( $timeline );
	}

	/**
	 * Get a unique identifier for the datasource to uniquely identify
	 *
	 * Used in oEmbed cache key to save a short, unique representation of the datasource
	 *
	 * @since 2.0.0
	 *
	 * @param \Twitter\Widgets\Embeds\Timeline $timeline timeline object
	 *
	 * @return string unique identifier or empty string if minimum requirements not met
	 */
	public static function getDataSourceIdentifier( $timeline )
	{
		// verify passed parameter
		if ( ! ( is_a( $timeline, '\Twitter\Widgets\Embeds\Timeline' ) && method_exists( $timeline, 'getID' ) ) ) {
			return '';
		}

		return $timeline->getID();
	}
}
