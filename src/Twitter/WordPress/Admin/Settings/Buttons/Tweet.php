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

namespace Twitter\WordPress\Admin\Settings\Buttons;

/**
 * Store a Twitter username for attribution of site content
 *
 * @since 1.0.0
 */
class Tweet implements \Twitter\WordPress\Admin\Settings\SettingsSection
{
	/**
	 * Define our option name
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const OPTION_NAME = 'twitter_share';

	/**
	 * Priority of the section affecting insertion order on the single settings page
	 *
	 * Display after site attribution section
	 *
	 * @since 1.0.0
	 *
	 * @type int
	 */
	const SECTION_PRIORITY = 6;

	/**
	 * The hook suffix of the parent settings page
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $hook_suffix;

	/**
	 * Store existing options, if any exist
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	protected $existing_options;

	/**
	 * Reference the feature by name
	 *
	 * @since 1.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return __( 'Tweet Button', 'twitter' );
	}

	/**
	 * Add Tweet button content wrapper option and settings section to an existing settings page
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix hook suffix of an existing settings page
	 *
	 * @return bool site attribution settings loaded
	 */
	public static function addToSettingsPage( $hook_suffix )
	{
		if ( ! ( is_string( $hook_suffix ) && $hook_suffix ) ) {
			return false;
		}

		$settings = new static();
		$settings->hook_suffix = $hook_suffix;

		register_setting( $hook_suffix, self::OPTION_NAME, array( __CLASS__, 'sanitizeOption' ) );
		add_action(
			'load-' . $hook_suffix,
			array( &$settings, 'onload' ),
			5, // after theme settings
			0
		);

		return true;
	}

	/**
	 * Set up settings section
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function onload()
	{
		if ( ! isset( $this->hook_suffix ) ) {
			return;
		}

		$options = get_option( self::OPTION_NAME );
		if ( ! is_array( $options ) ) {
			$options = array();
		}
		$this->existing_options = $options;

		add_action(
			'add_' . $this->hook_suffix . '_section',
			array( &$this, 'defineSection' ),
			static::SECTION_PRIORITY,
			0  // no parameters
		);
	}

	/**
	 * Define Tweet button section and the fields within
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function defineSection()
	{
		if ( ! isset( $this->hook_suffix ) ) {
			return;
		}

		$section = 'tweet-button';
		add_settings_section(
			$section,
			static::featureName(),
			array( __CLASS__, 'sectionHeader' ),
			$this->hook_suffix
		);

		add_settings_field(
			'tweet-button-position',
			_x( 'Position', 'Display of content relative to other content. above, below, left, right', 'twitter' ),
			array( &$this, 'displayPosition' ),
			$this->hook_suffix,
			$section
		);
		add_settings_field(
			'tweet-button-size',
			__( 'Size' ),
			array( &$this, 'displaySize' ),
			$this->hook_suffix,
			$section
		);
	}

	/**
	 * Introduce the settings section
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function sectionHeader()
	{
		echo '<p>';
		echo esc_html( __( 'Add a Tweet Button to every public post.', 'twitter' ) );
		echo '</p>';
	}

	/**
	 * Get option values and labels for dropdown display
	 *
	 * @since 1.0.0
	 *
	 * @return array option values and labels {
	 *   @type   string option value
	 *   @type   string translated option label
	 * }
	 */
	public static function getPositionOptions()
	{
		return array(
			'' => ' ',
			'before' => _x( 'before', 'before another piece of content', 'twitter' ),
			'after' => _x( 'after', 'after another piece of content', 'twitter' ),
			'both' => _x( 'before & after', 'before and after another piece of content', 'twitter' ),
		);
	}

	/**
	 * Choose to display a Tweet button before, after, or before & after every public post
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function displayPosition()
	{
		$key = 'position';

		$position_options = static::getPositionOptions();
		$existing_value = '';
		if ( isset( $this->existing_options ) && isset( $this->existing_options[ $key ] ) && $this->existing_options[ $key ] && isset( $position_options[ $this->existing_options[ $key ] ] ) ) {
			$existing_value = $this->existing_options[ $key ];
		}

		$select = '<select name="' . esc_attr( static::OPTION_NAME . '[' . $key . ']' ) . '">';
		foreach ( $position_options as $option => $label ) {
			$select .= '<option';
			if ( $option ) {
				$select .= ' value="' . esc_attr( $option ) . '"';
			}
			if ( $option === $existing_value ) {
				$select .= ' selected';
				if ( ! current_theme_supports( 'html5' ) ) {
					$select .= '="selected"';
				}
			}
			$select .= '>' . esc_html( $label ) . '</option>';
		}
		$select .= '</select>';

		// <select> markup escaped when building the element
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo sprintf( esc_html( _x( 'Display Tweet Button %s post content', 'display Tweet Button relative to the content of an article', 'twitter' ) ), $select );
	}

	/**
	 * Choose a large button size, overriding the default
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function displaySize()
	{
		$key = 'size';

		echo '<label><input type="checkbox" name="' . esc_attr( static::OPTION_NAME . '[' . $key . ']' ) . '" value="large"';

		if ( isset( $this->existing_options ) && isset( $this->existing_options[ $key ] ) && 'large' === $this->existing_options[ $key ] ) {
			echo ' checked';
			if ( ! current_theme_supports( 'html5' ) ) {
				echo '="checked"';
			}
		}

		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();

		echo '> ' . esc_html( __( 'Large button', 'twitter' ) ) . '</label>';
	}

	/**
	 * Sanitize posted option before saving
	 *
	 * @since 1.0.0
	 *
	 * @param array $options submitted option {
	 *   @type  string option name
	 *   @type  mixed option value
	 * }
	 *
	 * @return array $options cleaned option {
	 *   @type   string option name
	 *   @type   string option value
	 * }
	 */
	public static function sanitizeOption( $options )
	{
		if ( ! is_array( $options ) ) {
			return array();
		}

		$clean_options = array();

		$key = 'position';
		if ( isset( $options[ $key ] ) && $options[ $key ] ) {
			$position_options = static::getPositionOptions();
			if ( isset( $position_options[ $options[ $key ] ] ) ) {
				$clean_options[ $key ] = $options[ $key ];
			}
			unset( $position_options );
		}
		unset( $key );

		if ( isset( $options['size'] ) && 'large' === $options['size'] ) {
			$clean_options['size'] = 'large';
		}

		return $clean_options;
	}
}
