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
	const VERSION = '2.0.1';

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
		$classname = get_called_class();

		// load translated text
		add_action( 'init', array( $classname, 'loadTranslatedText' ) );

		// compatibility wrappers to coexist with other popular plugins
		add_action( 'plugins_loaded', array( $classname, 'compatibility' ) );

		// make widgets available on front and back end
		add_action( 'widgets_init', array( $classname, 'widgetsInit' ) );

		// register Twitter JavaScript to eligible for later enqueueing
		add_action( 'wp_enqueue_scripts', array( $classname, 'registerScripts' ), 1, 0 );

		if ( is_admin() ) {
			// admin-specific functionality
			add_action( 'init', array( $classname, 'adminInit' ) );
		} else {
			// hooks to be executed on general execution of WordPress such as public pageviews
			add_action( 'init', array( $classname, 'publicInit' ) );
			add_action( 'wp_head', array( $classname, 'wpHead' ), 1, 0 );
		}

		// shortcodes
		static::registerShortcodeHandlers();
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
		load_plugin_textdomain( static::TEXT_DOMAIN );
	}

	/**
	 * Plugin features and corresponding widget class
	 *
	 * @since 2.0.0
	 *
	 * @return array plugin feature and widget class{
	 *   @type string plugin feature identifier
	 *   @type string WP_Widget class to register
	 * }
	 */
	public static function getAvailableWidgets()
	{
		return array(
			\Twitter\WordPress\Features::FOLLOW_BUTTON    => '\Twitter\WordPress\Widgets\Buttons\Follow',
			\Twitter\WordPress\Features::PERISCOPE_ON_AIR => '\Twitter\WordPress\Widgets\Buttons\Periscope\OnAir',
			\Twitter\WordPress\Features::EMBED_PROFILE    => '\Twitter\WordPress\Widgets\Embeds\Timeline\Profile',
			\Twitter\WordPress\Features::EMBED_LIST       => '\Twitter\WordPress\Widgets\Embeds\Timeline\TwitterList',
			\Twitter\WordPress\Features::EMBED_SEARCH     => '\Twitter\WordPress\Widgets\Embeds\Timeline\Search',
			\Twitter\WordPress\Features::EMBED_COLLECTION => '\Twitter\WordPress\Widgets\Embeds\Timeline\Collection',
			\Twitter\WordPress\Features::TRACKING_PIXEL   => '\Twitter\WordPress\Widgets\Advertising\Tracking',
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
		$features = \Twitter\WordPress\Features::getEnabledFeatures();
		$widgets = static::getAvailableWidgets();

		foreach ( $widgets as $feature => $widget_class ) {
			if ( isset( $features[ $feature ] ) ) {
				register_widget( $widget_class );
			}
		}
	}

	/**
	 * Load Twitter widgets JavaScript early in the page build if a dependent widget will be rendered
	 *
	 * @since 2.0.1
	 *
	 * @return void
	 */
	public static function loadTwitterWidgetsJavaScriptWhenWidgetsActive()
	{
		$widgets_js_widgets = static::getAvailableWidgets();
		// remove widgets not depending on Twitter widgets JS
		unset( $widgets_js_widgets[ \Twitter\WordPress\Features::TRACKING_PIXEL ] );
		if ( empty( $widgets_js_widgets ) ) {
			return;
		}

		$features = \Twitter\WordPress\Features::getEnabledFeatures();
		foreach ( $widgets_js_widgets as $feature_name => $widget_class ) {
			if ( isset( $features[ $feature_name ] ) ) {
				if ( method_exists( $widget_class, 'getBaseID' ) ) {
					$base_id = $widget_class::getBaseID();
					if ( $base_id && is_active_widget( false, false, $base_id, true ) ) {
						// enqueue after the script is registered in wp_enqueue_scripts action priority 1
						add_action( 'wp_enqueue_scripts', array( '\Twitter\WordPress\JavaScriptLoaders\Widgets', 'enqueue' ) );

						// register DNS prefetch before WordPress resource hints run at wp_head priority 2
						add_action( 'wp_head', array( '\Twitter\WordPress\JavaScriptLoaders\Widgets', 'dnsPrefetch' ), 1 );

						// only enqueue once
						return;
					}
					unset( $base_id );
				}
			}
		}
	}

	/**
	 * Load Twitter advertising JavaScript early in the page build if an ad tracker widget will be rendered
	 *
	 * @since 2.0.1
	 *
	 * @return void
	 */
	public static function loadTwitterAdvertisingJavaScriptWhenWidgetsActive()
	{
		$features = \Twitter\WordPress\Features::getEnabledFeatures();
		if ( ! isset( $features[ \Twitter\WordPress\Features::TRACKING_PIXEL ] ) ) {
			return;
		}

		if ( is_active_widget( false, false, \Twitter\WordPress\Widgets\Advertising\Tracking::getBaseID(), true ) ) {
			// enqueue after the script is registered in wp_enqueue_scripts action priority 1
			add_action( 'wp_enqueue_scripts', array( '\Twitter\WordPress\JavaScriptLoaders\Tracking', 'enqueue' ) );

			// register DNS prefetch before WordPress resource hints run at wp_head priority 2
			add_action( 'wp_head', array( '\Twitter\WordPress\JavaScriptLoaders\Tracking', 'dnsPrefetch' ), 1 );
		}
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
		// User profile fields
		add_action( 'admin_init', array( '\Twitter\WordPress\Admin\Profile\User', 'init' ) );
		add_action( 'admin_init', array( '\Twitter\WordPress\Admin\Profile\PeriscopeUser', 'init' ) );

		$features = \Twitter\WordPress\Features::getEnabledFeatures();

		// Twitter settings menu
		\Twitter\WordPress\Admin\Settings\Loader::init();

		if ( isset( $features[ \Twitter\WordPress\Features::CARDS ] )
			|| isset( $features[ \Twitter\WordPress\Features::TWEET_BUTTON ] )
		) {
			// Edit post meta box
			add_action( 'admin_init', array( '\Twitter\WordPress\Admin\Post\MetaBox', 'init' ) );
		}
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

		$features = \Twitter\WordPress\Features::getEnabledFeatures();

		// load widgets JS if a Twitter widget is active
		static::loadTwitterWidgetsJavaScriptWhenWidgetsActive();
		// load advertising JS if an ad tracking widget is active
		static::loadTwitterAdvertisingJavaScriptWhenWidgetsActive();

		// do not add content filters to HTTP 404 response
		if ( is_404() ) {
			return;
		}

		if ( ! isset( $features[ \Twitter\WordPress\Features::TWEET_BUTTON ] ) ) {
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
			array( '\Twitter\WordPress\Content\Buttons\Tweet', 'contentFilter' ),
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
		$features = \Twitter\WordPress\Features::getEnabledFeatures();
		$shortcode_namespace = '\\Twitter\\WordPress\\Shortcodes\\';

		// features requiring HTTPS remote requests
		if ( wp_http_supports( array( 'ssl' => true ) ) ) {
			foreach (
				array(
					\Twitter\WordPress\Features::EMBED_TWEET            => 'Tweet',
					\Twitter\WordPress\Features::EMBED_TWEET_VIDEO      => 'Tweet\\Video',
					\Twitter\WordPress\Features::EMBED_VINE             => 'Vine',
					\Twitter\WordPress\Features::EMBED_PROFILE          => 'Timeline\\Profile',
					\Twitter\WordPress\Features::EMBED_LIST             => 'Timeline\\TwitterList',
					\Twitter\WordPress\Features::EMBED_COLLECTION       => 'Timeline\\Collection',
					\Twitter\WordPress\Features::EMBED_COLLECTION_GRID  => 'Timeline\\CollectionGrid',
					\Twitter\WordPress\Features::EMBED_MOMENT           => 'Moment',
				) as $feature => $shortcode_class
			) {
				if ( ! isset( $features[ $feature ] ) ) {
					continue;
				}

				add_action(
					'plugins_loaded',
					array( $shortcode_namespace . 'Embeds\\' . $shortcode_class, 'init' ),
					5,
					0
				);
			}
		}

		// initialize buttons, search timeline, and ad pixel if not disabled
		foreach (
			array(
				\Twitter\WordPress\Features::FOLLOW_BUTTON    => 'Buttons\\Follow',
				\Twitter\WordPress\Features::TWEET_BUTTON     => 'Buttons\\Share',
				\Twitter\WordPress\Features::PERISCOPE_ON_AIR => 'Buttons\\Periscope\\OnAir',
				\Twitter\WordPress\Features::EMBED_SEARCH     => 'Embeds\\Timeline\\Search',
				\Twitter\WordPress\Features::TRACKING_PIXEL   => 'Advertising\\Tracking',
			) as $feature => $shortcode_class
		) {
			if ( ! isset( $features[ $feature ] ) ) {
				continue;
			}

			add_action(
				'plugins_loaded',
				array( $shortcode_namespace . $shortcode_class, 'init' ),
				5,
				0
			);
		}
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
		$features = \Twitter\WordPress\Features::getEnabledFeatures();

		// Twitter Cards markup
		if ( isset( $features[ \Twitter\WordPress\Features::CARDS ] ) ) {
			add_action(
				'wp_head',
				array( '\Twitter\WordPress\Head\CardsMetaElements', 'outputMetaElements' ),
				99, // late priority to override if multiple values provided
				0 // expects no arguments
			);
		}

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
		// Twitter widgets
		\Twitter\WordPress\JavaScriptLoaders\Widgets::register();

		// Vine embed
		\Twitter\WordPress\JavaScriptLoaders\VineEmbed::register();

		// Twitter audience tracker and conversion
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
		$features = \Twitter\WordPress\Features::getEnabledFeatures();
		if ( isset( $features[ \Twitter\WordPress\Features::CARDS ] ) ) {
			\Twitter\WordPress\Cards\Compatibility::init();
		}
	}
}
