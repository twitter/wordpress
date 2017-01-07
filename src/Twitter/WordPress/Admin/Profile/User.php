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

namespace Twitter\WordPress\Admin\Profile;

/**
 * Associate a WordPress account with a Twitter account
 *
 * @since 1.0.0
 */
class User
{
	/**
	 * Conditionally load features on the edit profile page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		// only show to authors
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// add Twitter after website
		add_filter( 'user_contactmethods', array( __CLASS__, 'addContactMethod' ), 1, 2 );
		// provide additional hints in label text
		add_filter( 'user_twitter_label', array( __CLASS__, 'contactMethodLabel' ), 10, 1 );
		// clean up user input
		add_filter( 'sanitize_user_meta_twitter', array( __CLASS__, 'sanitize' ), 10, 1 );
	}

	/**
	 * Add Twitter as a contact method in the Contact Info profile section
	 *
	 * @since 1.0.0
	 *
	 * @see wp_get_user_contact_methods()
	 *
	 * @param array   $methods contact methods and their labels {
	 *   @type  string contact method
	 *   @type  string label
	 * }
	 * @param \WP_User $user    WP_User object.
	 *
	 * @return array contact methods and their labels {
	 *   @type   string contact method
	 *   @type   string label
	 * }
	 */
	public static function addContactMethod( $methods, $user )
	{
		$methods['twitter'] = 'Twitter';
		return $methods;
	}

	/**
	 * Customize HTML display label for contact method
	 *
	 * @since 1.0.0
	 *
	 * @param string $label HTML label
	 *
	 * @return string HTML label
	 */
	public static function contactMethodLabel( $label = '' )
	{
		return _x( 'Twitter @username', 'Prompt requesting entry of a Twitter username', 'twitter' );
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
