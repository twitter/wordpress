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
 * Display the latest Tweets from a list of Twitter accounts
 *
 * @since 2.0.0
 */
class TwitterList implements \Twitter\WordPress\Shortcodes\ShortcodeInterface, \Twitter\WordPress\Shortcodes\PublishOEmbedEndpoint
{
	use \Twitter\WordPress\Shortcodes\OEmbedTrait, \Twitter\WordPress\Shortcodes\Embeds\Timeline;

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_list';

	/**
	 * The oEmbed regex registered by WordPress Core
	 *
	 * @since 1.5.0
	 *
	 * @type string
	 */
	const OEMBED_CORE_REGEX = '#https?://(www\.)?twitter\.com/\w{1,15}/lists/.*#i';

	/**
	 * Regex used to match a List in text
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const URL_REGEX = '#^https://twitter\.com/([a-z0-9_]{1,20})/lists/([a-z][a-z0-9_\\-]{0,24})$#i';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-timeline-list';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 2.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'screen_name' => '', 'slug' => '' );

	/**
	 * Reference the feature by name
	 *
	 * @since 2.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return _x( 'Twitter List', 'The latest Tweets authored by members of a Twitter list', 'twitter' );
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
		// Shortcode required
		if (  ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}

		shortcode_ui_register_for_shortcode(
			static::SHORTCODE_TAG,
			array(
				'label'         => esc_html( static::featureName() ),
				'listItemImage' => 'dashicons-twitter',
				'attrs'         => array(
					array(
						'attr'  => 'screen_name',
						'label' => esc_html( __( 'Twitter @username', 'twitter' ) ),
						'type'  => 'text',
						'meta'  => array(
							'placeholder' => 'UN',
							'pattern'     => '[A-Za-z0-9_]{1,20}',
						),
					),
					array(
						'attr'  => 'slug',
						'label' => esc_html( _x( 'List slug', 'Unique identifier for a Twitter List', 'twitter' ) ),
						'type'  => 'text',
						'meta'  => array(
							'placeholder' => 'security-council',
							'pattern'     => '[a-z][a-z0-9_\\-]{0,24}',
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
	 * @param array  $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 * @param array  $attr    Embed attributes. Not used.
	 * @param string $url     The original URL that was matched by the regex. Not used.
	 * @param array  $rawattr The original unmodified attributes. Not used.
	 *
	 * @return string HTML markup for the profile timeline or an empty string if requirements not met
	 */
	public static function linkHandler( $matches, $attr, $url, $rawattr )
	{
		if (  ! ( is_array( $matches ) && isset( $matches[1] ) && $matches[1] && isset( $matches[2] ) && $matches[2] ) ) {
			return '';
		}

		return static::shortcodeHandler( array( 'screen_name' => $matches[1], 'slug' => $matches[2] ) );
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

		if (  ! ($options['screen_name'] && $options['slug']) ) {
			return '';
		}

		$timeline = \Twitter\Widgets\Embeds\Timeline\TwitterList::fromArray( $options );
		if (  ! ( $timeline && $timeline->getScreenName() && $timeline->getSlug() ) ) {
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
		if (  ! is_a( $timeline, '\Twitter\Widgets\Embeds\Timeline' ) ) {
			return '';
		}

		$screen_name = $timeline->getScreenName();
		if (  ! $screen_name ) {
			return '';
		}
		$slug = $timeline->getSlug();
		if (  ! $slug ) {
			return '';
		}

		return $screen_name . '/' . $slug;
	}
}
