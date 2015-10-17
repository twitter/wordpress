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

namespace Twitter\WordPress\Admin\Post;

/**
 * Post meta box for setting Twitter Card and Tweet text custom values
 *
 * @since 1.0.0
 */
class MetaBox
{
	/**
	 * Check page origin before saving
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const NONCE_NAME = 'twitter_custom';

	/**
	 * Attach hooks when the post edit screen loads
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		// load meta box hooks on post creation screens
		foreach ( array( 'post', 'post-new' ) as $hook ) {
			add_action( 'load-' . $hook . '.php', array( __CLASS__, 'load' ), 1, 0 );
		}
		add_action( 'save_post', array( __CLASS__, 'save' ) );
	}

	/**
	 * Attach meta boxes and save actions to the post edit screen
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function load()
	{
		add_action( 'add_meta_boxes', array( __CLASS__, 'addMetaBox' ), 1, 0 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueueScripts' ) );

		$features = \Twitter\WordPress\Features::getEnabledFeatures();
		if ( isset( $features[ \Twitter\WordPress\Features::TWEET_BUTTON ] ) ) {
			add_action( 'wp', array( '\Twitter\WordPress\Admin\Post\TweetIntent', 'registerPostMeta' ), 10, 0 );
		}
	}

	/**
	 * Is the current post type meant for display on the public Web?
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type WordPress post type
	 *
	 * @return bool true if meant for public viewing and sharing
	 */
	public static function isPostTypePublic( $post_type )
	{
		if ( ! $post_type ) {
			return false;
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( isset( $post_type_object->public ) && $post_type_object->public ) {
			return true;
		}

		return false;
	}

	/**
	 * Add a post meta box to the post editor page to set custom Twitter values for the post
	 *
	 * @since 1.0.0
	 *
	 * @uses add_meta_box
	 *
	 * @return void
	 */
	public static function addMetaBox()
	{
		$post = get_post();

		if ( ! ( $post && is_a( $post, 'WP_Post' ) ) ) {
			return;
		}

		// is the post type meant to be public?
		$post_type = get_post_type( $post );
		if ( ! $post_type ) {
			return;
		}
		$post_type_object = get_post_type_object( $post_type );
		if ( ! ( isset( $post_type_object->public ) && $post_type_object->public ) ) {
			return;
		}

		add_meta_box(
			'twitter-custom',
			_x( 'Twitter Custom Text', 'Text displayed for a Twitter audience', 'twitter' ),
			array( __CLASS__, 'content' ),
			$post_type
		);
	}

	/**
	 * Queue JS and CSS resources for use on the page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueueScripts()
	{
		wp_enqueue_style(
			'twitter-admin-edit-post', // handle
			plugins_url(
				'static/css/admin/post/edit.min.css',
				\Twitter\WordPress\PluginLoader::getPluginMainFile()
			), // absolute URI
			array(), // no dependencies
			'1.0.1' // last change. used for caching
		);
	}

	/**
	 * Display meta box content
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post WordPress post object
	 *
	 * @return void
	 */
	public static function content( $post )
	{
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), self::NONCE_NAME );

		$features = \Twitter\WordPress\Features::getEnabledFeatures();

		if ( isset( $features[ \Twitter\WordPress\Features::TWEET_BUTTON ] ) ) {
			\Twitter\WordPress\Admin\Post\TweetIntent::metaBoxContent();
		}
		if ( isset( $features[ \Twitter\WordPress\Features::CARDS ] ) ) {
			\Twitter\WordPress\Admin\Post\TwitterCard::metaBoxContent();
		}
	}

	/**
	 * Get post capability singular base to be used when gating access.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type post type
	 *
	 * @return string post type object capability type or empty string
	 */
	public static function postTypeCapabilityBase( $post_type )
	{
		$post_type_object = get_post_type_object( $post_type );

		if ( ! isset( $post_type_object->capability_type ) ) {
			return '';
		}

		$capability_singular_base = '';
		if ( is_string( $post_type_object->capability_type ) ) {
			$capability_singular_base = $post_type_object->capability_type;
		} else if ( is_array( $post_type_object->capability_type ) ) {
			if ( isset( $post_type_object->capability_type[0] ) ) {
				$capability_singular_base = $post_type_object->capability_type[0];
			}
		}
		return $capability_singular_base;
	}

	/**
	 * Save custom values entered in the post edit screen
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id WordPress post identifier
	 *
	 * @return void
	 */
	public static function save( $post_id )
	{
		// verify if this is an auto save routine
		// do not take action until the form is submitted
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// verify nonce
		if ( empty( $_POST[ self::NONCE_NAME ] ) || ! wp_verify_nonce( $_POST[ self::NONCE_NAME ], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		if ( ! $post_id ) {
			return;
		}
		// does post exist?
		$post = get_post( $post_id );
		if ( ! $post ) {
			return;
		}

		// check permissions
		$post_type = get_post_type( $post );
		if ( ! $post_type ) {
			return;
		}
		$capability_singular_base = static::postTypeCapabilityBase( $post_type );
		if ( ! ( $capability_singular_base && current_user_can( 'edit_' . $capability_singular_base, $post_id ) ) ) {
			return;
		}

		\Twitter\WordPress\Admin\Post\TweetIntent::save( $post );
		\Twitter\WordPress\Admin\Post\TwitterCard::save( $post );
	}
}
