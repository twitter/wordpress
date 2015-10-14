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

namespace Twitter\WordPress\User;

/**
 * Get WordPress user meta values
 *
 * @since 1.0.0
 */
class Meta
{

	/**
	 * Get a username value stored for the given WordPress user identifier
	 *
	 * @since 1.3.0
	 *
	 * @param int|string $user_id WordPress user identifier. may be WP_User->ID or a separate identifier used by an extending system
	 * @param string     $key user attribute or meta key storing the username of interest
	 *
	 * @return string stored username. empty string if no user_id provided or no username found
	 */
	public static function getSocialUsername( $user_id, $key ) {
		// basic test for invalid passed parameter
		if ( ! $user_id ) {
			return '';
		}
		if ( ! is_string( $key ) || ! $key ) {
			return '';
		}

		if ( function_exists( 'get_user_attribute' ) ) {
			$username = get_user_attribute( $user_id, $key );
		} else {
			$username = get_user_meta( $user_id, $key, /* single */ true );
		}

		if ( ! is_string( $username ) ) {
			$username = '';
		}

		// pass a username through a filter if not explicitly defined through user meta
		if ( ! $username ) {
			/**
			 * Allow sites to provide a WordPress user's Twitter username through a filter
			 *
			 * @since 1.0.0
			 *
			 * @param string     $username Twitter username associated with a WordPress user ID
			 * @param int|string $user_id  WordPress user identifier. may be WP_User->ID or a separate identifier used by an extending system
			 */
			$username = apply_filters( $key . '_username', $username, $user_id );
		}

		return $username;
	}

	/**
	 * Get a Twitter @username stored for a given WordPress user identifier
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $user_id WordPress user identifier. may be WP_User->ID or a separate identifier used by an extending system
	 *
	 * @return string Twitter username value stored for the given WordPress user identifier
	 */
	public static function getTwitterUsername( $user_id ) {
		return static::getSocialUsername( $user_id, 'twitter' );
	}

	/**
	 * Get a Periscope username stored for a given WordPress identifier
	 *
	 * @since 1.3.0
	 *
	 * @param int|string $user_id WordPress user identifier. may be WP_User->ID or a separate identifier used by an extending system
	 *
	 * @return string Periscope username value stored for the given WordPress user identifier
	 */
	public static function getPeriscopeUsername( $user_id ) {
		return static::getSocialUsername( $user_id, 'periscope' );
	}
}
