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

namespace Twitter\WordPress\Shortcodes\Buttons;

/**
 * Display a Follow button
 *
 * @since 1.0.0
 */
class Follow implements \Twitter\WordPress\Shortcodes\ShortcodeInterface
{
	use \Twitter\WordPress\Shortcodes\AuthorContext;

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_follow';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'twitter-follow';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'screen_name' => '', 'show_count' => true, 'show_screen_name' => true, 'size' => 'medium' );

	/**
	 * Attach handlers for a follow button
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
	 * Reference the feature by name
	 *
	 * @since 2.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return __( 'Follow Button', 'twitter' );
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
							'placeholder' => 'WordPress',
							'pattern'     => \Twitter\Helpers\Validators\ScreenName::getPattern(),
						),
					),
					array(
						'attr'    => 'size',
						'label'   => esc_html( __( 'Button size:', 'twitter' ) ),
						'type'    => 'radio',
						'value'   => '',
						'options' => array(
							''      => esc_html( _x( 'medium', 'medium size button', 'twitter' ) ),
							'large' => esc_html( _x( 'large', 'large size button', 'twitter' ) ),
						),
					),
				),
			)
		);
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

		if ( isset( $attributes['screen_name'] ) ) {
			$attributes['screen_name'] = \Twitter\Helpers\Validators\ScreenName::trim( $attributes['screen_name'] );
			if ( ! $attributes['screen_name'] ) {
				unset( $attributes['screen_name'] );
			}
		}

		$attributes = \Twitter\WordPress\Shortcodes\Helpers\Attributes::booleanOption(
			$attributes,
			array( 'show_count', 'show_screen_name' )
		);

		// large is the only option
		if ( isset( $attributes['size'] ) ) {
			if ( is_string( $attributes['size'] ) && in_array( strtolower( $attributes['size'] ), array( 'large', 'l' ), /* strict */ true ) ) {
				$attributes['size'] = 'large';
			}
		}

		return $attributes;
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
		$options = static::getShortcodeAttributes( $attributes );

		if ( ! isset( $options['screen_name'] ) ) {
			$options['screen_name'] = static::getScreenName();
		}

		// follow target required
		if ( ! $options['screen_name'] ) {
		    return '';
		}

		$follow = \Twitter\Widgets\Buttons\Follow::fromArray( $options );
		if ( ! $follow ) {
			return '';
		}

		$html = $follow->toHTML( _x( 'Follow %s', 'Follow a Twitter user', 'twitter' ), '\Twitter\WordPress\Helpers\HTMLBuilder' );
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
