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

namespace Twitter\WordPress;

/**
 * Hook the WordPress plugin into the appropriate WordPress actions and filters
 *
 * @since 1.0.0
 */
class PluginLoader
{
	/**
	 * Uniquely identify plugin version
	 *
	 * Bust caches based on this value
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const VERSION = '1.0.1';

	/**
	 * Unique domain of the plugin's translated text
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const TEXT_DOMAIN = 'twitter';

	/**
	 * Bind to hooks and filters
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		// load translated text
		add_action( 'init', array( __CLASS__, 'loadTranslatedText' ) );

		// compatibility wrappers to coexist with other popular plugins
		add_action( 'plugins_loaded', array( __CLASS__, 'compatibility' ) );

		// make widgets available on front and back end
		add_action( 'widgets_init', array( __CLASS__, 'widgetsInit' ) );

		// register Twitter JavaScript to eligible for later enqueueing
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'registerScripts' ), 1, 0 );

		if ( is_admin() ) {
			// admin-specific functionality
			add_action( 'init', array( __CLASS__, 'adminInit' ) );
		} else {
			// hooks to be executed on general execution of WordPress such as public pageviews
			static::registerShortcodeHandlers();
			add_action( 'init', array( __CLASS__, 'publicInit' ) );
			add_action( 'wp_head', array( __CLASS__, 'wpHead' ), 1, 0 );
		}
	}

	/**
	 * Full path to the directory containing the Twitter for WordPress plugin files
	 *
	 * @since 1.0.0
	 *
	 * @return string full directory path
	 */
	public static function getPluginDirectory()
	{
		return dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/';
	}

	/**
	 * Full path to the main file of the Twitter for WordPress plugin
	 *
	 * @since 1.0.0
	 *
	 * @return string full path to file
	 */
	public static function getPluginMainFile()
	{
		return static::getPluginDirectory() . 'twitter.php';
	}

	/**
	 * Load translated strings for the current locale, if a translation exists
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function loadTranslatedText()
	{
		load_plugin_textdomain(
			static::TEXT_DOMAIN,
			false, // deprecated parameter as of WP 2.7
			dirname( plugin_basename( static::getPluginMainFile() ) ) . '/languages' // directory location of MO files
		);
	}

	/**
	 * Register widgets
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function widgetsInit()
	{
		register_widget( '\Twitter\WordPress\Widgets\Follow' );
	}

	/**
	 * Hook into actions and filters specific to a WordPress administrative view
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function adminInit()
	{
		// Twitter settings menu
		\Twitter\WordPress\Admin\Settings\Loader::init();

		// Edit post meta box
		add_action( 'admin_init', array( '\Twitter\WordPress\Admin\Post\MetaBox', 'init' ) );

		// User profile fields
		add_action( 'admin_init', array( '\Twitter\WordPress\Admin\Profile\User', 'init' ) );
	}

	/**
	 * Register actions and filters shown in a non-admin context
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function publicInit()
	{
		// enhance web browser view only
		if ( is_feed() ) {
			return;
		}

		// load widgets JS if a Twitter widget is active
		if ( is_active_widget( false, false, \Twitter\WordPress\Widgets\Follow::BASE_ID, true ) ) {
			// enqueue after the script is registered in wp_enqueue_scripts action priority 1
			add_action( 'wp_enqueue_scripts', array( '\Twitter\WordPress\JavaScriptLoaders\Widgets', 'enqueue' ) );
		}

		// do not add content filters to HTTP 404 response
		if ( is_404() ) {
			return;
		}

		/**
		 * Set the priority to apply to Twitter elements automatically added to the_content
		 *
		 * Allow publishers to adjust the order of Twitter buttons relative to other the_content actors
		 *
		 * @since 1.0.0
		 *
		 * @param int $priority filter priority
		 */
		$twitter_content_priority = apply_filters( 'twitter_content_filter_priority', 15 );

		// possibly add Tweet button(s)
		add_filter(
			'the_content',
			array( '\Twitter\WordPress\Content\TweetButton', 'contentFilter' ),
			$twitter_content_priority
		);
	}

	/**
	 * Register shortcodes handlers and callbacks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function registerShortcodeHandlers()
	{
		// features requiring HTTPS remote requests
		if ( wp_http_supports( array( 'ssl' => true ) ) ) {
			// Embedded Tweet
			add_action(
				'plugins_loaded',
				array( '\Twitter\WordPress\Shortcodes\EmbeddedTweet', 'init' ),
				5,
				0
			);
			// Twitter embedded videos
			add_action(
				'plugins_loaded',
				array( '\Twitter\WordPress\Shortcodes\EmbeddedTweetVideo', 'init' ),
				5,
				0
			);
		}

		// Follow button
		add_action(
			'plugins_loaded',
			array( '\Twitter\WordPress\Shortcodes\Follow', 'init' ),
			5,
			0
		);

		// Tweet button
		add_action(
			'plugins_loaded',
			array( '\Twitter\WordPress\Shortcodes\Share', 'init' ),
			5,
			0
		);

		// Ad conversion and audience tracking
		add_action(
			'plugins_loaded',
			array( '\Twitter\WordPress\Shortcodes\Tracking', 'init' ),
			5,
			0
		);
	}

	/**
	 * Attach actions to the wp_head action
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function wpHead()
	{
		// Twitter Cards markup
		add_action(
			'wp_head',
			array( '\Twitter\WordPress\Head\CardsMetaElements', 'outputMetaElements' ),
			99, // late priority to override if multiple values provided
			0 // expects no arguments
		);

		// page-level customizations referenced by Twitter JavaScript
		add_action(
			'wp_head',
			array( '\Twitter\WordPress\Head\WidgetsMetaElements', 'outputMetaElements' ),
			11, // priority
			0 // expects no arguments
		);

		if ( ! is_singular() && ! is_author() ) {
			// attribute authorship to site or site section when a post author does not exist
			add_action(
				'wp_head',
				array( '\Twitter\WordPress\Head\AuthorshipLink', 'relMe' ),
				10, // default priority
				0   // no parameters
			);
		}
	}

	/**
	 * Register JavaScript during the enqueue scripts action
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function registerScripts()
	{
		// widgets.js
		\Twitter\WordPress\JavaScriptLoaders\Widgets::register();

		// ad tracker
		\Twitter\WordPress\JavaScriptLoaders\Tracking::register();
	}

	/**
	 * Compatibility wrappers for popular plugins
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function compatibility()
	{
		\Twitter\WordPress\Cards\Compatibility::init();
	}
}
