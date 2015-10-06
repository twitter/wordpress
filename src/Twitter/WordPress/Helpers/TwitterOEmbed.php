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

namespace Twitter\WordPress\Helpers;

/**
 * Request oEmbeds from Twitter's publisher endpoints
 *
 * @since 1.2.0
 */
class TwitterOEmbed extends TwitterAPI
{

	/**
	 * Twitter Publisher FQDN
	 *
	 * @since 1.2.0
	 *
	 * @type string
	 */
	const HOST = 'publish.twitter.com';

	/**
	 * Response formats supported by Twitter publisher oEmbed endpoints
	 *
	 * @since 1.2.0
	 *
	 * @type array supported formats {
	 *   @type string filename extension
	 *   @type string media type
	 * }
	 */
	public static $RESPONSE_FORMATS = array( 'json' => 'application/json' );

	/**
	 * Build a Twitter REST API URL
	 *
	 * @since 1.2.0
	 *
	 * @param string $relative_path    Publisher oEmbed path
	 * @param array  $query_parameters query parameters to append to the URL
	 * @param string $response_format  requested response format
	 *
	 * @return string absolute API URL or empty string if invalid relative_path passed
	 */
	public static function getAPIURL( $relative_path = 'oembed', $query_parameters = null, $response_format = 'json' )
	{
		if ( ! is_array( $query_parameters ) ) {
			return '';
		}

		// Minimal information not provided
		if ( ! ( isset( $query_parameters['id'] ) || isset( $query_parameters['url'] ) ) ) {
			return '';
		}

		if ( ! is_string( $relative_path ) ) {
			$relative_path = '';
		} else {
			$relative_path = trim( trim( $relative_path, '/' ) );
		}
		if ( ! $relative_path ) {
			$relative_path = 'oembed';
		}

		if ( ! ( is_string( $response_format ) && $response_format && isset( static::$RESPONSE_FORMATS[ $response_format ] ) ) ) {
			$response_format = '';
		}

		$url = 'https://' . implode( '/', array( static::HOST, $relative_path ) );
		if ( $response_format ) {
			$url .= '.' . $response_format;
		}
		$url .= '?' . http_build_query( $query_parameters, '', '&' );

		return $url;
	}
}
