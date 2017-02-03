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
 * Display a Moment
 *
 * @since 1.2.0
 */
class Moment extends Timeline\CollectionGrid
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
	 * The oEmbed regex registered by WordPress Core
	 *
	 * @since 1.5.0
	 *
	 * @type string
	 */
	const OEMBED_CORE_REGEX = '#https?://(www\.)?twitter\.com/i/moments/.*#i';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-moment';

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
	 * Reference the feature by name
	 *
	 * @since 1.3.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return __( 'Twitter Moment', 'twitter' );
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
		// Moment ID required
		if ( ! $options['id'] ) {
			return '';
		}

		$timeline = \Twitter\Widgets\Embeds\Moment::fromArray( $options );
		if ( ! ( $timeline && $timeline->getID() ) ) {
			return '';
		}

		return static::getHTMLForTimeline( $timeline );
	}
}
