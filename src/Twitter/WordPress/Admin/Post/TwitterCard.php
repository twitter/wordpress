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
 * Store Twitter Card data for a WordPress post
 *
 * @since 1.0.0
 */
class TwitterCard
{
	/**
	 * Meta field key used to store custom Twitter Card data
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const META_KEY = 'twitter_card';

	/**
	 * Associative array key representing the title value inside the META_KEY array
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const TITLE_KEY = 'title';

	/**
	 * Associative array key representing the description value inside the META_KEY array
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const DESCRIPTION_KEY = 'description';

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
		if ( function_exists( 'registered_meta_key_exists' ) ) {
			$args = array(
				'sanitize_callback' => $args,
				'description'       => __( 'Customize title and description shown in Twitter link previews', 'twitter' ),
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
	 * Limit displayed card fields to fields supported by the post type
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type post type
	 *
	 * @return array associative array of supported fields {
	 *   @type   string field name (examples: title, description)
	 *   @type   bool   exists boolean for easy key reference
	 * }
	 */
	public static function supportedCardFieldsByPostType( $post_type )
	{
		if ( ! is_string( $post_type ) && $post_type ) {
			return array();
		}

		$features = array();
		if ( post_type_supports( $post_type, 'title' ) ) {
			$features[ static::TITLE_KEY ] = true;
		}
		if ( post_type_supports( $post_type, 'excerpt' ) ) {
			$features[ static::DESCRIPTION_KEY ] = true;
		}
		return $features;
	}

	/**
	 * Display Twitter Card customizations
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function metaBoxContent()
	{
		$post = get_post();
		$cards_fields_supported_by_post_type = array();

		$stored_values = array();
		if ( $post && is_a( $post, 'WP_Post' ) && isset( $post->ID ) ) {
			$stored_values = get_post_meta( $post->ID, static::META_KEY, true );
			if ( ! is_array( $stored_values ) ) {
				$stored_values = array();
			}
			$cards_fields_supported_by_post_type = static::supportedCardFieldsByPostType( get_post_type( $post ) );
		}

		// no supported Twitter Cards fields
		if ( empty( $cards_fields_supported_by_post_type ) ) {
			return;
		}

		// separate Twitter Cards content from Intent content above
		echo '<hr';
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		echo '>';

		echo '<h3>' . esc_html( __( 'Twitter Card', 'twitter' ) ) . '</h3>';

		// set up the table
		echo '<table id="twitter-card">';
		echo '<thead><tr><th scope="col">' . esc_html( _x( 'Property', 'Object component, such as a title and description of an article', 'twitter' ) ) . '</th><th scope="col">' . esc_html( _x( 'Value', 'Table column header: user-inputted value', 'twitter' ) ) . '</th></tr></thead><tbody>';

		if ( isset( $cards_fields_supported_by_post_type[ static::TITLE_KEY ] ) ) {
			echo '<tr>';
			echo '<th scope="row" class="left"><label for="twitter-card-title">' . esc_html( __( 'Title' ) ) . '</label></th>';
			echo '<td><input type="text" id="twitter-card-title" name="' . esc_attr( static::META_KEY . '[' . static::TITLE_KEY . ']' ) . '" maxlength="70" autocomplete="off"';
			if ( isset( $stored_values[ static::TITLE_KEY ] ) ) {
				echo ' value="' . esc_attr( $stored_values[ static::TITLE_KEY ] ) . '"';
			} else {
				echo ' placeholder="' . esc_attr( get_the_title( $post ) ) . '"';
			}

			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
			echo '></td>';
			echo '</tr>';
		}

		if ( isset( $cards_fields_supported_by_post_type[ static::DESCRIPTION_KEY ] ) ) {
			echo '<tr>';
			echo '<th scope="row" class="left"><label for="twitter-card-description">' . esc_html( __( 'Description' ) ) . '</label></th>';
			echo '<td><input type="text" id="twitter-card-description" name="' . esc_attr( static::META_KEY . '[' . static::DESCRIPTION_KEY . ']' ) . '" maxlength="200" autocomplete="off"';
			if ( isset( $stored_values[ static::DESCRIPTION_KEY ] ) ) {
				echo ' value="' . esc_attr( $stored_values[ static::DESCRIPTION_KEY ] ) . '"';
			}
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
			echo '></td>';
			echo '</tr>';
		}

		// close the table, describe its contents
		echo '</tbody></table>';
		echo '<p class="description">' . esc_html( __( 'Customize Twitter link previews', 'twitter' ) ) . '</p>';
	}

	/**
	 * Sanitize user inputs for Twitter Card data before saving as a post meta value
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

		if ( isset( $fields[ static::TITLE_KEY ] ) ) {
			$title = \Twitter\WordPress\Cards\Sanitize::sanitizePlainTextString( $fields[ static::TITLE_KEY ] );
			if ( $title ) {
				$cleaned_fields[ static::TITLE_KEY ] = $title;
			}
			unset( $title );
		}
		if ( isset( $fields[ static::DESCRIPTION_KEY ] ) ) {
			$description = \Twitter\WordPress\Cards\Sanitize::sanitizePlainTextString( $fields[ static::DESCRIPTION_KEY ] );
			if ( $description ) {
				$cleaned_fields[ static::DESCRIPTION_KEY ] = $description;
			}
			unset( $description );
		}

		return $cleaned_fields;
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
