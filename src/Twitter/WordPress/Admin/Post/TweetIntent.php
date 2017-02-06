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
 * Store custom Tweet Intent data for a WordPress post
 *
 * @since 1.0.0
 */
class TweetIntent
{
	/**
	 * Meta field key used to store custom Tweet Intent data
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const META_KEY = 'twitter_share';

	/**
	 * Associative array key representing the hashtags CSV value inside the META_KEY array
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const HASHTAGS_KEY = 'hashtags';

	/**
	 * Associative array key representing the Tweet text string inside META_KEY array
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const TEXT_KEY = 'text';

	/**
	 * Register the post meta key and its sanizitzer
	 *
	 * Used by WordPress JSON API to expose programmatic editor beyond the post meta box display used in the HTML-based admin interface
	 *
	 * @since 1.0.0
	 * @uses  register_meta
	 *
	 * @return void
	 */
	public static function registerPostMeta()
	{
		$args = array( get_called_class(), 'sanitizeFields' );
		// extra parameters for WordPress 4.6+
		if ( function_exists( 'registered_meta_key_exists' ) ) {
			$args = array(
				'sanitize_callback' => $args,
				'description'       => __( 'Customize Tweet button pre-populated share text and hashtags', 'twitter' ),
				'show_in_rest'      => true,
				'type'              => 'array',
				'single'            => true,
			);
		}
		register_meta(
			'post',
			static::META_KEY,
			$args
		);
	}

	/**
	 * Get a Twitter configuration object for up-to-date character counts for a wrapped URL
	 *
	 * Hard coded in this plugin to avoid requiring Twitter application credentials
	 * Implementing sites may want to override this function with a cached value from Twitter's help/configuration API response
	 *
	 * @since 1.0.0
	 *
	 * @see https://dev.twitter.com/rest/reference/get/help/configuration
	 *
	 * @return object object with short_url_length and optional short_url_length_https properties
	 */
	public static function getTwitterConfiguration()
	{
		$config = new \stdClass();
		$config->short_url_length = 23;

		return $config;
	}

	/**
	 * Get the length of a wrapped post URL shared on Twitter
	 *
	 * @since 1.0.0
	 *
	 * @return int wrapped URL length or 0 if no length info found
	 */
	public static function getShortURLLength()
	{
		$config = static::getTwitterConfiguration();

		if ( ! ( is_object( $config ) && isset( $config->short_url_length ) ) ) {
			return 0;
		}
		return absint( $config->short_url_length );
	}

	/**
	 * Display Tweet Web Intent customizations
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function metaBoxContent()
	{
		$post = get_post();

		$stored_values = array();
		if ( $post && is_a( $post, 'WP_Post' ) && isset( $post->ID ) ) {
			$stored_values = get_post_meta( $post->ID, static::META_KEY, true );
			if ( ! is_array( $stored_values ) ) {
				$stored_values = array();
			}
		}

		echo '<h3>' . esc_html( _x( 'Tweet', 'Tweet verb. Sharing.', 'twitter' ) ) . '</h3>';
		echo '<table id="tweet-intent">';
		echo '<thead><tr><th scope="col">' . esc_html( _x( 'Parameter', 'Customization or variable', 'twitter' ) ) . '</th><th scope="col">' . esc_html( _x( 'Value', 'Table column header: user-inputted value', 'twitter' ) ) . '</th></tr></thead><tbody>';

		$available_characters = 140;
		// t.co wrapped URL length
		$short_url_length = static::getShortURLLength();
		if ( $short_url_length ) {
			// tweet length after accounting for the shared URL with a space separator
			$available_characters = $available_characters - $short_url_length - 1;
		}

		echo '<tr>';
		echo '<th scope="row" class="left"><label for="tweet-text">' . esc_html( _x( 'Text', 'Share / Tweet text', 'twitter' ) ) . '</label></th>';
		echo '<td><input id="tweet-text" name="' . esc_attr( static::META_KEY . '[' . static::TEXT_KEY . ']' ) . '" type="text" maxlength="';
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo $available_characters;
		echo '" autocomplete="off"';
		if ( isset( $stored_values[ static::TEXT_KEY ] ) ) {
			echo ' value="' . esc_attr( $stored_values[ static::TEXT_KEY ] ) . '"';
		} else {
			echo ' placeholder="' . esc_attr( get_the_title( $post ) ) . '"';
		}
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		echo '></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<th scope="row" class="left"><label for="tweet-hashtags">' . esc_html( __( 'Hashtags', 'twitter' ) ) . '</label></th>';
		echo '<td><input id="tweet-hashtags" name="' . esc_attr( static::META_KEY . '[' . static::HASHTAGS_KEY . ']' ) . '" type="text" maxlength="';
		// integer does not need escaping
		// @codingStandardsIgnoreStart WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo ($available_characters - 2);
		// @codingStandardsIgnoreEnd WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo '" autocomplete="off"';
		if ( isset( $stored_values[ static::HASHTAGS_KEY ] ) && is_array( $stored_values[ static::HASHTAGS_KEY ] ) ) {
			echo ' value="' . esc_attr( implode( ',', $stored_values[ static::HASHTAGS_KEY ] ) ) . '"';
		}
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		echo '></td>';
		echo '</tr>';

		echo '</tbody></table>';
		echo '<p class="description">' . esc_html( __( 'Pre-populate Tweet text', 'twitter' ) ) . '</p>';
	}

	/**
	 * Sanitize user inputs for Tweet Intent data before saving as a post meta value
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields POST fields for META_KEY
	 *
	 * @return array|bool sanizited array or false if none set
	 */
	public static function sanitizeFields( $fields )
	{
		if ( ! is_array( $fields ) ) {
			// store nothing
			return false;
		}

		// overwrite everything
		if ( empty( $fields ) ) {
			return array();
		}

		$cleaned_fields = array();

		if ( isset( $fields[ static::HASHTAGS_KEY ] ) ) {
			$hashtags = static::sanitizeCommaSeparatedHashtags( $fields[ static::HASHTAGS_KEY ] );
			if ( ! empty( $hashtags ) ) {
				$cleaned_fields[ static::HASHTAGS_KEY ] = $hashtags;
			}
			unset( $hashtags );
		}

		if ( isset( $fields[ static::TEXT_KEY ] ) ) {
			// allow Tweet text length overruns
			// the text will appear pre-populated in the Tweet composer but require editing before posting a Tweet
			$text = trim( $fields[ static::TEXT_KEY ] );
			if ( $text ) {
				$cleaned_fields[ static::TEXT_KEY ] = $text;
			}
			unset( $text );
		}

		return $cleaned_fields;
	}

	/**
	 * Sanitize an expected comma-separated list of hashtags into an array
	 *
	 * @since 1.0.0
	 *
	 * @param string $hashtag_string comma-separated list of hashtags
	 *
	 * @return array list of hashtags
	 */
	public static function sanitizeCommaSeparatedHashtags( $hashtag_string )
	{
		if ( ! is_string( $hashtag_string ) ) {
			return array();
		}

		$hashtag_string = trim( $hashtag_string );
		if ( ! $hashtag_string ) {
			return array();
		}

		$intent = \Twitter\Intents\Tweet::fromArray( array( 'hashtags' => $hashtag_string ) );
		if ( ! $intent ) {
			return array();
		}

		return $intent->getHashtags();

	}

	/**
	 * Save post meta values
	 *
	 * Basic capability tests should already be applied by \Twitter\WordPress\Admin\Post\MetaBox::save before calling this method
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post WordPress post object
	 *
	 * @return void
	 */
	public static function save( $post )
	{
		// test if post ID exists on object
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		if ( ! isset( $_POST[ static::META_KEY ] ) ) {
			return;
		}

		$fields = $_POST[ static::META_KEY ];
		if ( ! is_array( $fields ) ) {
			return;
		}

		$fields = static::sanitizeFields( $fields );
		if ( empty( $fields ) ) {
			delete_post_meta( $post->ID, static::META_KEY );
		} else {
			update_post_meta( $post->ID, static::META_KEY, $fields );
		}
	}
}
