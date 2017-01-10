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
 * Display a collection of Tweets in a grid template
 *
 * Maintains compatibility with the twitter_grid shortcode added in 1.3.0
 *
 * @since 2.0.0
 */
class CollectionGrid extends \Twitter\WordPress\Shortcodes\Embeds\Timeline\Collection
{
	/**
	 * Shortcode tag to be matched
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_grid';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-collection-grid';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'id' => '' );

	/**
	 * Collection shortcode defaults not supported in a grid template
	 *
	 * @since 2.0.0
	 *
	 * @type string[] shortcode defaults to remove if seen
	 */
	public static $UNSUPPORTED_SHORTCODE_DEFAULTS = array(
		'height',
		'aria_polite',
		'theme',
		'link_color',
		'border_color',
	);

	/**
	 * Reference the feature by name
	 *
	 * @since 2.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return _x( 'Twitter Collection Grid', 'Tweets organized into a collection displayed in a grid template', 'twitter' );
	}

	/**
	 * Override the shortcode initialization and URL handler init with only shortcode logic
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
	 * Shortcode allowed attributes and default values for grid template
	 *
	 * Remove attributes only applicable to standard timelines, allowing developers acting on the shortcode_atts_twitter_grid filter to have only valid parameters
	 *
	 * @since 2.0.0
	 *
	 * @return array shortcode allowed attributes and default values
	 */
	public static function getShortcodeDefaults()
	{
		$attributes = array_merge( static::$TIMELINE_SHORTCODE_DEFAULTS, static::$SHORTCODE_DEFAULTS );

		foreach ( static::$UNSUPPORTED_SHORTCODE_DEFAULTS as $key ) {
			unset( $attributes[ $key ] );
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
	 * @return string HTML markup. empty string if parameter requirement not met or no collection info found
	 */
	public static function shortcodeHandler( $attributes, $content = '' )
	{
		$options = static::getShortcodeAttributes( $attributes );
		// collection ID required
		if ( ! $options['id'] ) {
			return '';
		}
		$options['widget_type'] = \Twitter\Widgets\Embeds\Timeline\Collection::WIDGET_TYPE_GRID;

		$timeline = \Twitter\Widgets\Embeds\Timeline\Collection::fromArray( $options );
		if ( ! ( $timeline && $timeline->getID() ) ) {
			return '';
		}

		return static::getHTMLForTimeline( $timeline );
	}
}
