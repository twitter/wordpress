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
 * Display a Periscope On Air button
 *
 * @since 1.3.0
 */
class PeriscopeOnAir implements ShortcodeInterface
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
				'label'         => esc_html( __( 'Periscope On Air Button', 'twitter' ) ),
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
							'large' => esc_html( _x( 'large',  'large size button',  'twitter' ) ),
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
	 * @param array  $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
	 * @param array  $attr    Embed attributes. Not used.
	 * @param string $url     The original URL that was matched by the regex. Not used.
	 * @param array  $rawattr The original unmodified attributes. Not used.
	 *
	 * @return string HTML markup for the Periscope On Air button or an empty string if requirements not met
	 */
	public static function linkHandler( $matches, $attr, $url, $rawattr )
	{
		if ( ! ( is_array( $matches ) && isset( $matches[2] ) && $matches[2] ) ) {
			return '';
		}

		return static::shortcodeHandler( array( 'username' => $matches[2] ) );
	}

	/**
	 * Clean up provided shortcode values
	 *
	 * @since 1.3.0
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

		if ( isset( $attributes['username'] ) ) {
			$username = \Twitter\Helpers\Validators\PeriscopeUsername::trim( $attributes['username'] );
			if ( $username ) {
				$options['username'] = $username;
			}
			unset( $username );
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
	 * Get the Periscope username of the author of the current post
	 *
	 * @since 1.3.0
	 *
	 * @return string Periscope username or empty if no screen name stored
	 */
	public static function getUsername()
	{
		if ( ! in_the_loop() ) {
			return '';
		}

		$username = \Twitter\WordPress\User\Meta::getPeriscopeUsername( get_the_author_meta( 'ID' ) );
		if ( ! $username ) {
			return '';
		}

		return $username;
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

		$username = '';
		if ( isset( $options['username'] ) ) {
			$username = $options['username'];
			unset( $options['username'] );
		}
		if ( ! $username ) {
			$username = static::getUsername();
			// user target required
			if ( ! $username ) {
				return '';
			}
		}

		// update the options array with the Periscope username
		$options['username'] = $username;
		unset( $username );

		$on_air = \Twitter\Widgets\PeriscopeOnAir::fromArray( $options );
		if ( ! $on_air ) {
			return '';
		}

		$html = $on_air->toHTML( '\Twitter\WordPress\Helpers\HTMLBuilder' );
		if ( ! $html ) {
			return '';
		}

		$html = '<div class="periscope-on-air">' . $html . '</div>';

		$inline_js = \Twitter\WordPress\JavaScriptLoaders\Widgets::enqueue();
		if ( $inline_js ) {
			return $html . $inline_js;
		}

		return $html;
	}
}
