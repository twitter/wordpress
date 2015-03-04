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

namespace Twitter\WordPress\Admin\Settings;

/**
 * Site-level settings for Twitter widget themes
 *
 * @since 1.0.0
 */
class Theme implements SettingsSection
{
	/**
	 * Define our option array value.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const OPTION_NAME = 'twitter_widgets';

	/**
	 * Priority of the section affecting insertion order on the single settings page
	 *
	 * First section
	 *
	 * @since 1.0.0
	 *
	 * @type int
	 */
	const SECTION_PRIORITY = 1;

	/**
	 * The hook suffix of the parent settings page
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $hook_suffix;

	/**
	 * Reference the feature by name
	 *
	 * @since 1.0.0
	 *
	 * @return string translated feature name
	 */
	public static function featureName()
	{
		return _x( 'Theme', 'Visual and color theme', 'twitter' );
	}

	/**
	 * Theme options and labels
	 *
	 * @since 1.0.0
	 *
	 * @return array associative array of accepted values and the value's translated label
	 */
	public static function themeChoices()
	{
		return array(
			'light' => _x( 'light', 'Option for content to appear with a light background and dark text', 'twitter' ),
			'dark'  => _x( 'dark', 'Option for content to appear with a dark background and light text', 'twitter' )
		);
	}

	/**
	 * Default options if no options exist
	 *
	 * @since 1.0.0
	 *
	 * @return array associative array of option values
	 */
	public static function defaultOptions()
	{
		return array(
			'theme' => 'light',
		);
	}

	/**
	 * Add site attribution option and settings section to an existing settings page
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

		register_setting(
			$hook_suffix,
			self::OPTION_NAME,
			array( __CLASS__, 'sanitizeOptions' )
		);
		add_action(
			'load-' . $hook_suffix,
			array( &$settings, 'onload' ),
			1, // first section
			0
		);

		return true;
	}

	/**
	 * Store existing options. Set up page sections
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
			$options = static::defaultOptions();
		}
		$this->existing_options = $options;

		// theme
		add_action(
			'add-' . $this->hook_suffix . '-section',
			array( &$this, 'defineSection' ),
			static::SECTION_PRIORITY, // top of screen
			0  // no parameters
		);
		// contextual help
		add_action(
			'add-' . $this->hook_suffix . '-help-tab',
			array( __CLASS__, 'addHelpTab' ),
			static::SECTION_PRIORITY,
			1
		);

		$late_priority = 10;

		// warnings
		add_action(
			'add-' . $this->hook_suffix . '-section',
			array( &$this, 'defineWarningsSection' ),
			$late_priority, // default priority
			0  // no parameters
		);
	}

	/**
	 * Add theme settings section and fields
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function defineSection()
	{
		$section = 'twitter-colorscheme';
		add_settings_section(
			$section,
			static::featureName(),
			array( __CLASS__, 'sectionHeader' ),
			$this->hook_suffix
		);

		add_settings_field(
			'twitter-theme',
			_x( 'Theme', 'A choice of color options grouped as a theme', 'twitter' ),
			array( &$this, 'displayTheme' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'twitter-theme' )
		);

		add_settings_field(
			'twitter-link-color',
			_x( 'Link color', 'CSS color styling of a link text', 'twitter' ),
			array( &$this, 'displayLinkColor' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'twitter-link-color' )
		);

		add_settings_field(
			'twitter-border-color',
			_x( 'Border color', 'CSS color styling of a box border', 'twitter' ),
			array( &$this, 'displayBorderColor' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => 'twitter-border-color' )
		);
	}

	/**
	 * Add warnings settings section and fields
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function defineWarningsSection()
	{
		$section = 'warnings';
		add_settings_section(
			$section,
			__( 'Warnings', 'twitter' ),
			array( __CLASS__, 'warningsSectionHeader' ),
			$this->hook_suffix
		);

		add_settings_field(
			'twitter-csp',
			__( 'Content Security Policy', 'twitter' ),
			array( &$this, 'displayContentSecurityPolicy' ),
			$this->hook_suffix,
			$section
		);
	}

	/**
	 * Add warnings settings section and fields
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function defineRestrictionsSection()
	{
		$section = 'twitter-restrictions';
		add_settings_section(
			$section,
			__( 'Restrictions', 'twitter' ),
			array( __CLASS__, 'restrictionsSectionHeader' ),
			$this->hook_suffix
		);

		// do not track
		add_settings_field(
			'twitter-dnt',
			_x( 'Personalization', 'Personalize Twitter content based on website visits', 'twitter' ),
			array( &$this, 'displayDoNotTrack' ),
			$this->hook_suffix,
			$section
		);
	}

	/**
	 * Introduce the settings page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function sectionHeader()
	{
		echo '<p>';
		echo esc_html( __( 'Customize colors used in Twitter widgets to match your site\'s theme', 'twitter' ) );
		echo '</p>';
	}

		/**
	 * Choose a theme
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function displayTheme()
	{
		$key = 'theme';

		$existing_value = 'light';
		if ( isset( $this->existing_options[ $key ] ) ) {
			$existing_value = $this->existing_options[ $key ];
		}

		$name_attribute = esc_attr( esc_attr( self::OPTION_NAME . '[' . $key . ']' ) );
		$close_void_element = \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';
		$choices = static::themeChoices();

		echo '<fieldset id="' . esc_attr( 'twitter-' . $key ) . '">';
		foreach ( $choices as $value => $label ) {
			echo '<div><label><input type="radio" name="' . $name_attribute . '" value="' . esc_attr( $value ) . '"' . checked( $existing_value, $value, false ) . $close_void_element;
			echo ' ' . esc_html( $label );
			echo '</label></div>';
		}
		echo '</fieldset>';
	}

	/**
	 * Choose a hex color value as the link color
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function displayLinkColor()
	{
		$key = 'link-color';

		echo '<input type="color" name="' . esc_attr( self::OPTION_NAME . '[' . $key . ']' ) . '" id="twitter-link-color"';
		if ( isset( $this->existing_options[ $key ] ) ) {
			echo ' value="' . esc_attr( '#' . $this->existing_options[ $key ] ) . '"';
		}
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';

		echo '<p class="description">';
		echo esc_html( __( 'Color of link text inside a Twitter widget.', 'twitter' ) );
		echo '</p>';
	}

	/**
	 * Choose a hex color value as the border color
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function displayBorderColor()
	{
		$key = 'border-color';

		echo '<input type="color" name="' . esc_attr( self::OPTION_NAME . '[' . $key . ']' ) . '" id="twitter-border-color"';
		if ( isset( $this->existing_options[ $key ] ) ) {
			echo ' value="' . esc_attr( '#' . $this->existing_options[ $key ] ) . '"';
		}
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';

		echo '<p class="description">';
		echo esc_html( __( 'Color of border surrounding a Twitter widget.', 'twitter' ) );
		echo '</p>';
	}

	/**
	 * Display inline help content
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Screen $screen current screen
	 *
	 * @return void
	 */
	public static function addHelpTab( $screen )
	{
		if ( ! $screen ) {
			return;
		}

		$content = '<p>' . esc_html( __( 'Twitter Embedded Tweet and Timeline widgets support customization of color theme, link color, and border color inside a rendered widget.', 'twitter' ) ) . '</p>';
		$content .= '<p>' . esc_html( __( 'Choose a light or dark theme to best match the background color and text color of surrounding content.', 'twitter' ) ) . '</p>';
		$content .= '<p>' . esc_html( __( 'Link color is applied to linked text including URLs, #hashtags, and @mentions.', 'twitter' ) ) . '</p>';
		$content .= '<p>' . esc_html( __( 'Border color applies to borders separating Tweet sections or individual Tweets.', 'twitter' ) ) . '</p>';

		$screen->add_help_tab( array(
			'id'      => 'twitter-theme-help',
			'title'   => static::featureName(),
			'content' => $content,
		) );
	}

	/**
	 * Introduction to the Twitter restrictions section
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function restrictionsSectionHeader()
	{
		echo '<p>';
		echo esc_html( __( 'Limit Twitter functionality', 'twitter' ) );
		echo '</p>';
	}

	/**
	 * Do not track visitors for use in Twitter advertisers and site suggestions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function displayDoNotTrack()
	{
		$key = 'dnt';

		echo '<label>';
		echo '<input type="checkbox" name="' . esc_attr( self::OPTION_NAME . '[' . $key . ']' ) . '" id="twitter-dnt" value="1"';
		checked( isset( $this->existing_options[ $key ] ) );
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';
		echo esc_html( __( 'Do not use my website to tailor content and suggestions for Twitter users', 'twitter' ) );
		echo '</label>';

		echo '<p class="description">';
		echo esc_html( __( 'Twitter may track website visitors for advertising targeting, suggested Twitter accounts, and other purposes.', 'twitter' ) );
		echo ' ' . '<a href="https://dev.twitter.com/web/overview/privacy" target="_blank">' . esc_html( _x( 'Read more', 'learn more about this topic', 'twitter' ) ) . '</a>';
		echo '</p><p class="description">';
		echo esc_html( __( 'Select this option if you wish to opt-out of Twitter tracking visitors when a Twitter button or widget appears on a webpage.', 'twitter' ) );
		echo '</p>';
	}

	/**
	 * Introduce the warnings section
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function warningsSectionHeader()
	{
		echo '<p>';
		echo esc_html( __( 'Warnings and error display', 'twitter' ) );
		echo '</p>';
	}

	/**
	 * Suppress a Content Security Policy warning
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function displayContentSecurityPolicy()
	{
		$key = 'csp';

		echo '<label><input type="checkbox" name="' . esc_attr( self::OPTION_NAME . '[' . $key . ']' ) . '" value="1"';
		checked( isset( $this->existing_options[ $key ] ) );
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';
		echo esc_html( __( 'Suppress Content Security Policy warnings', 'twitter' ) );
		echo '</label>';

		echo '<p class="description">';
		echo sprintf( esc_html( __( 'Please note: Not all widget functionality is supported when a %s is applied', 'twitter' ) ), '<a href="' . esc_url( 'https://developer.mozilla.org/docs/Web/Security/CSP/Introducing_Content_Security_Policy', array( 'https', 'http' ) ) . '">' . esc_html( __( 'Content Security Policy', 'twitter' ) ) . '</a>' );
		echo '</p>';
	}

	/**
	 * Sanitize theme options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options submitted options
	 *
	 * @return array associative array of clean options
	 */
	public static function sanitizeOptions( $options )
	{
		if ( ! is_array( $options ) || empty( $options ) ) {
			return array();
		}
		$clean_options = array();

		if ( isset( $options['theme'] ) && 'dark' === $options['theme'] ) {
			$clean_options['theme'] = 'dark';
		}
		foreach ( array( 'link-color', 'border-color' ) as $color_option ) {
			if ( ! ( isset( $options[ $color_option ] ) && is_string( $options[ $color_option ] ) ) ) {
				continue;
			}
			$color_hex = ltrim( trim( $options[ $color_option ] ), '#' );
			if ( strlen( $color_hex ) <= 6 && ctype_xdigit( $color_hex ) ) {
				$clean_options[ $color_option ] = strtolower( $color_hex );
			}
		}
		foreach ( array( 'dnt', 'csp' ) as $bool_option ) {
			if ( isset( $options[ $bool_option ] ) && $options[ $bool_option ] ) {
				$clean_options[ $bool_option ] = true;
			}
		}

		return $clean_options;
	}
}
