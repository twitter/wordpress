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
 * Store a Twitter username for attribution of site content
 *
 * @since 1.0.0
 */
class SiteAttribution implements SettingsSection
{
	/**
	 * Define our option name
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const OPTION_NAME = 'twitter_site_attribution';

	/**
	 * Priority of the section affecting insertion order on the single settings page
	 *
	 * Display after theme section
	 *
	 * @since 1.0.0
	 *
	 * @type int
	 */
	const SECTION_PRIORITY = 5;

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
		return _x( 'Site attribution', 'Attribute content on a website to a Twitter account', 'twitter' );
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

		register_setting( $hook_suffix, self::OPTION_NAME, array( __CLASS__, 'sanitize' ) );
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

		add_action(
			'add-' . $this->hook_suffix . '-section',
			array( &$this, 'defineSection' ),
			static::SECTION_PRIORITY,
			0  // no parameters
		);

		// contextual help
		add_action(
			'add-' . $this->hook_suffix . '-help-tab',
			array( __CLASS__, 'addHelpTab' ),
			static::SECTION_PRIORITY,
			1 // accept current screen as a parameter
		);
	}

	/**
	 * Define site attribution section and the fields within
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

		$section = 'site-attribution';
		add_settings_section(
			$section,
			static::featureName(),
			array( __CLASS__, 'sectionHeader' ),
			$this->hook_suffix
		);

		add_settings_field(
			'site-attribution-username',
			\Twitter\WordPress\Admin\Profile\User::contactMethodLabel(),
			array( &$this, 'displaySiteAttributionUsername' ),
			$this->hook_suffix,
			$section,
			array( 'label_for' => self::OPTION_NAME )
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
		echo esc_html( __( "Attribute shared content to your site's Twitter account", 'twitter' ) );
		echo '</p>';
	}

	/**
	 * Input a Twitter screen_name to attribute to site content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function displaySiteAttributionUsername()
	{
		$html = '<input type="text" id="' . esc_attr( self::OPTION_NAME ) . '" name="' . esc_attr( self::OPTION_NAME ) . '" size="20"';
		$site_username = get_option( self::OPTION_NAME );
		if ( $site_username ) {
			$html .= ' value="' . esc_attr( $site_username ) . '"';
		}
		$html .= \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';

		// escaped in markup builder
		// @codingStandardsIgnoreStart WordPress.XSS.EscapeOutput
		echo $html;
		// @codingStandardsIgnoreEnd WordPress.XSS.EscapeOutput
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

		$content = '<p>' . sprintf( esc_html( __( 'Site attribution is used to attribute site content to a Twitter account in %1$s and %2$s.', 'twitter' ) ), '<a href="' . esc_url( 'https://dev.twitter.com/cards/overview', array( 'https', 'http' ) ) . '">' . esc_html( __( 'Twitter Cards', 'twitter' ) ) . '</a>', '<a href="' . esc_url( 'https://support.twitter.com/articles/20170934-twitter-card-analytics-dashboard', array( 'https', 'http' ) ) . '">' . esc_html( __( 'Twitter Analytics', 'twitter' ) ) . '</a>' ) . '</p>';
		$content .= '<p>' . esc_html( __( 'The account may also be used to note a Tweet originated from a Tweet Button on your site.', 'twitter' ) ) . '</p>';

		$screen->add_help_tab( array(
			'id'      => 'twitter-site-attribution-help',
			'title'   => static::featureName(),
			'content' => $content,
		) );
	}

	/**
	 * Clean up user inputted Twitter username value before saving the option
	 *
	 * @since 1.0.0
	 *
	 * @param string $screen_name inputted Twitter username value
	 *
	 * @return string sanitized Twitter username value
	 */
	public static function sanitize( $screen_name )
	{
		if ( ! is_string( $screen_name ) ) {
			return '';
		}
		$screen_name = trim( $screen_name );
		if ( ! $screen_name ) {
			return '';
		}
		$screen_name = sanitize_text_field( $screen_name );
		if ( ! $screen_name ) {
			return '';
		}

		return \Twitter\Helpers\Validators\ScreenName::sanitize( $screen_name );
	}
}
