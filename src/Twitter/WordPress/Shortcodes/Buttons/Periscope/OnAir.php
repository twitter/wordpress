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

namespace Twitter\WordPress\Shortcodes\Buttons\Periscope;

/**
 * Display a Periscope On Air button
 *
 * @since 1.3.0
 */
class OnAir implements \Twitter\WordPress\Shortcodes\ShortcodeInterface
{

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'periscope_on_air';

	/**
	 * HTML class to be used in div wrapper
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const HTML_CLASS = 'periscope-on-air';

	/**
	 * Regex used to match a Periscope profile URL in text
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const PERISCOPE_PROFILE_URL_REGEX = '#^https://(www\.)?periscope\.tv/([a-z0-9_]{1,20})#i';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.3.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'username' => '', 'size' => 'small' );

	/**
	 * Register shortcode macro and handler
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public static function init()
	{
		add_shortcode( static::SHORTCODE_TAG, array( __CLASS__, 'shortcodeHandler' ) );

		// pass a Periscope profile URL through the Periscope On Air shortcode handler
		wp_embed_register_handler(
			self::SHORTCODE_TAG,
			static::PERISCOPE_PROFILE_URL_REGEX,
			array( __CLASS__, 'linkHandler' ),
			1
		);

		// Shortcode UI, if supported
		add_action(
			'register_shortcode_ui',
			array( __CLASS__, 'shortcodeUI' ),
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
		return __( 'Periscope On Air Button', 'twitter' );
	}

	/**
	 * Describe shortcode for Shortcake UI
	 *
	 * @since 1.3.0
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
				'listItemImage' => 'dashicons-video-alt',
				'attrs'         => array(
					array(
						'attr'  => 'username',
						'label' => esc_html( _x( 'Periscope username', 'Prompt requesting entry of a Periscope username', 'twitter' ) ),
						'type'  => 'text',
						'meta'  => array(
							'placeholder' => 'photomatt',
							'pattern'     => '[A-Za-z0-9_]{1,20}',
						),
					),
					array(
						'attr'    => 'size',
						'label'   => esc_html( __( 'Button size:', 'twitter' ) ),
						'type'    => 'radio',
						'value'   => '',
						'options' => array(
							''      => esc_html( _x( 'small', 'small size button', 'twitter' ) ),
							'large' => esc_html( _x( 'large', 'large size button', 'twitter' ) ),
						),
					),
				),
			)
		);
	}

	/**
	 * Handle a URL matched by an embed handler
	 *
	 * @since 1.3.0
	 *
	 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 *
	 * @return string HTML markup for the Periscope On Air button or an empty string if requirements not met
	 */
	public static function linkHandler( $matches )
	{
		if ( ! ( is_array( $matches ) && isset( $matches[2] ) && $matches[2] ) ) {
			return '';
		}

		return static::shortcodeHandler( array( 'username' => $matches[2] ) );
	}

	/**
	 * Get the Periscope username of the author of the current post
	 *
	 * @since 1.3.0
	 *
	 * @return string Periscope username or empty if no screen name stored
	 */
	public static function getUsername()
	{
		$username = \Twitter\WordPress\User\Meta::getPeriscopeUsername( get_the_author_meta( 'ID' ) );
		if ( ! $username ) {
			return '';
		}

		return $username;
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

		if ( isset( $options['username'] ) ) {
			$options['username'] = \Twitter\Helpers\Validators\PeriscopeUsername::trim( $options['username'] );
			if ( ! $options['username'] ) {
				unset( $options['username'] );
			}
		}

		$options = \Twitter\WordPress\Shortcodes\Helpers\Attributes::lowercaseStringOption( $options, array( 'size' ) );

		// allow abbreviated size value
		if ( isset( $options['size'] ) && 'l' === $options['size'] ) {
			$options['size'] = 'large';
		}

		return $options;
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 1.3.0
	 *
	 * @param array  $attributes shortcode attributes
	 * @param string $content    shortcode content. no effect
	 *
	 * @return string Periscope On Air button HTML or empty string
	 */
	public static function shortcodeHandler( $attributes, $content = '' )
	{
		$options = static::getShortcodeAttributes( $attributes );

		if ( ! isset( $options['username'] ) ) {
			$options['username'] = static::getUsername();
		}

		// on air target required
		if ( ! $options['username'] ) {
			return '';
		}

		$on_air = \Twitter\Widgets\Buttons\Periscope\OnAir::fromArray( $options );
		if ( ! $on_air ) {
			return '';
		}

		$html = $on_air->toHTML( '\Twitter\WordPress\Helpers\HTMLBuilder' );
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
