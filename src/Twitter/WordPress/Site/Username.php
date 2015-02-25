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

namespace Twitter\WordPress\Site;

/**
 * Associate site Twitter username(s) with hooks used across the site
 *
 * @since 1.0.0
 */
class Username
{

	/**
	 * Attribute a webpage to a Twitter @username for the site or site section
	 *
	 * Similar to a site byline or masthead with Twitter attribution
	 * Affects Twitter Analytics
	 *
	 * @since 1.0.0
	 *
	 * @link https://dev.twitter.com/cards/markup#twitter-site Twitter Card site attribution
	 * @link https://dev.twitter.com/cards/analytics Twitter Card analytics
	 *
	 * @param int|string|bool|null $post_id WP_Post->ID, false response of get_the_ID, proprietary post ID, or null if outside of a post context or no post ID found
	 *
	 * @return string Twitter username or empty string
	 */
	public static function getSiteAttribution( $post_id = null )
	{
		// simplify passed types for filter
		if ( false === $post_id ) {
			$post_id = null;
		}

		$username = get_option( \Twitter\WordPress\Admin\Settings\SiteAttribution::OPTION_NAME, '' );

		if ( ! is_string( $username ) ) {
			$username = '';
		}

		/**
		 * Allow sites to provide a WordPress site or site section Twitter username through a filter
		 *
		 * A username should be provided without its @ prefix.
		 * A site username might be overridden if a better match is available based on the post or archive context, such as AcmeSports overriding a general site username of Acme when displaying content inside the sports category
		 *
		 * @since 1.0.0
		 *
		 * @param string          $username Twitter username stored for the site
		 * @param int|string|null $post_id  WP_Post->ID, proprietary post ID, or null if outside of a post context or no post ID found
		 */
		return apply_filters( 'twitter_site_username', $username, $post_id );
	}

	/**
	 * Attribute a Tweet created through a link on your site to a Twitter username
	 *
	 * @since 1.0.0
	 *
	 * @link https://dev.twitter.com/web/tweet-button/web-intent#tweet-web-intent-via Tweet Web Intent via parameter
	 *
	 * @param int|string|null $post_id WP_Post->ID, proprietary post ID, or null if outside of a post context or no post ID found
	 *
	 * @return string Twitter username or empty string
	 */
	public static function getViaAttribution( $post_id = null )
	{
		/**
		 * Allow sites to provide a WordPress site or site section Twitter username through a filter
		 *
		 * A username should be provided without its @ prefix
		 *
		 * @since 1.0.0
		 *
		 * @param @param string   $username Twitter username stored for the site
		 * @param int|string|null $post_id  WP_Post->ID, proprietary post ID, or null if outside of a post context or no post ID found
		 */
		return apply_filters( 'twitter_via_username', static::getSiteAttribution( $post_id ), $post_id );
	}
}
