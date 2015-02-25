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
 * Display a Follow button
 *
 * @since 1.0.0
 */
class Follow
{

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_follow';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'screen_name' => '', 'show_count' => true, 'show_screen_name' => true, 'size' => 'medium' );

	/**
	 * Register shortcode macro and handler
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		add_shortcode( static::SHORTCODE_TAG, array( __CLASS__, 'shortcodeHandler' ) );
	}

	/**
	 * Clean up provided shortcode values
	 *
	 * Be liberal in what we accept in shortcode syntax before constructing a Follow button
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes provided shortcode attributes {
	 *   @type string shortcode attribute name
	 *   @type mixed  shortcode attribute value
	 * }
	 *
	 * @return array simplified shortcode values with defaults removed {
	 *   @type string      shortcode attribute name
	 *   @type bool|string shortcode attribute value
	 * }
	 */
	public static function sanitizeShortcodeParameters( $attributes = array() )
	{
		if ( ! is_array( $attributes ) ) {
			return array();
		}

		$options = array();

		if ( isset( $attributes['screen_name'] ) ) {
			$screen_name = \Twitter\Helpers\Validators\ScreenName::trim( $attributes['screen_name'] );
			if ( $screen_name ) {
				$options['screen_name'] = $screen_name;
			}
			unset( $screen_name );
		}

		foreach ( array( 'show_count', 'show_screen_name' ) as $falsey_option ) {
			// check for falsey values passed to shortcode
			if ( isset( $attributes[ $falsey_option ] ) ) {
				if ( false === $attributes[ $falsey_option ] || '0' == $attributes[ $falsey_option ] || ( is_string( $attributes[ $falsey_option ] ) && in_array( strtolower( $attributes[ $falsey_option ] ), array( 'false', 'no', 'off' ) ) ) ) {
					$options[ $falsey_option ] = false;
				}
			}
		}

		// large is the only option
		if ( isset( $attributes['size'] ) ) {
			if ( is_string( $attributes['size'] ) && in_array( strtolower( $attributes['size'] ), array( 'large', 'l' ) ) ) {
				$options['size'] = 'large';
			}
		}

		return $options;
	}

	/**
	 * Get the Twitter screen name of the author of the current post
	 *
	 * @since 1.0.0
	 *
	 * @return string Twitter screen name or empty if no screen name stored
	 */
	public static function getScreenName()
	{
		if ( ! in_the_loop() ) {
			return '';
		}

		$screen_name = \Twitter\WordPress\User\Meta::getTwitterUsername( get_the_author_meta( 'ID' ) );
		if ( ! $screen_name ) {
			return '';
		}

		return $screen_name;
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attributes shortcode attributes
	 * @param string $content    shortcode content. no effect
	 *
	 * @return string Follow button HTML or empty string
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

		$screen_name = '';
		if ( isset( $options['screen_name'] ) ) {
			$screen_name = $options['screen_name'];
			unset( $options['screen_name'] );
		}
		if ( ! $screen_name ) {
			$screen_name = static::getScreenName();
			// follow target required
			if ( ! $screen_name ) {
				return '';
			}
		}

		// update the options array with the Follow screen name
		$options['screen_name'] = $screen_name;

		$follow = \Twitter\Widgets\FollowButton::fromArray( $options );
		if ( ! $follow ) {
			return '';
		}

		$html = $follow->toHTML( _x( 'Follow %s', 'Follow a Twitter user', 'twitter' ), '\Twitter\WordPress\Helpers\HTMLBuilder' );
		if ( ! $html ) {
			return '';
		}

		\Twitter\WordPress\JavaScriptLoaders\Widgets::enqueue();
		return '<div class="twitter-follow">' . $html . '</div>';
	}
}
