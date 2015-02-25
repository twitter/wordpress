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
 * Request information from the Twitter API using OAuth
 *
 * @since 1.0.0
 */
class TwitterAPI
{

	/**
	 * Twitter API FQDN
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const HOST = 'api.twitter.com';

	/**
	 * Response formats supported by the Twitter API
	 *
	 * @since 1.0.0
	 *
	 * @type array supported formats {
	 *   @type string filename extension
	 *   @type string media type
	 * }
	 */
	public static $RESPONSE_FORMATS = array( 'json' => 'application/json', 'xml' => 'application/xml' );

	/**
	 * Build a Twitter REST API URL
	 *
	 * @since 1.0.0
	 *
	 * @param string $relative_path    API path
	 * @param array  $query_parameters query parameters to append to the URL
	 * @param string $response_format  requested response format
	 *
	 * @return string absolute API URL or empty string if invalid relative_path passed
	 */
	public static function getAPIURL( $relative_path, $query_parameters = null, $response_format = 'json' )
	{
		if ( ! is_string( $relative_path ) ) {
			return '';
		}
		$relative_path = trim( trim( $relative_path, '/' ) );
		if ( ! $relative_path ) {
			return '';
		}

		if ( ! ( is_string( $response_format ) && $response_format && isset( static::$RESPONSE_FORMATS[ $response_format ] ) ) ) {
			$response_format = '';
		}

		$api_version = '1.1';
		if ( 'statuses/oembed' === $relative_path ) {
			$api_version = '1';
		}

		$url = 'https://' . implode( '/', array( static::HOST, $api_version, $relative_path ) );
		if ( $response_format ) {
			$url .= '.' . $response_format;
		}
		if ( is_array( $query_parameters ) && ! empty( $query_parameters ) ) {
			$url .= '?' . http_build_query( $query_parameters, '', '&' );
		}

		return $url;
	}

	/**
	 * Build a User-Agent string for use in a HTTP request to Twitter REST API servers
	 *
	 * @since 1.0.0
	 *
	 * @return string User-Agent
	 */
	public static function getUserAgent()
	{
		global $wp_version;

		return apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; TfWP/' . \Twitter\WordPress\PluginLoader::VERSION . '; ' . get_bloginfo( 'url' ) );
	}

	/**
	 * Request JSON data from the Twitter API. JSON decode the results
	 *
	 * @since 1.0.0
	 *
	 * @param string $relative_path API path without the response type. e.g. statuses/show
	 * @param array  $parameters    query parameters
	 *
	 * @return stdClass|null json decoded result or null if no JSON returned or issues with parameters
	 */
	public static function getJSON( $relative_path, $parameters = null )
	{
		if ( ! $relative_path ) {
			return;
		}

		$request_url = static::getAPIURL( $relative_path, $parameters );
		if ( ! $request_url ) {
			return;
		}

		$response = wp_safe_remote_get(
			$request_url,
			array(
				'redirection' => 0,
				'httpversion' => '1.1',
				'user-agent' => static::getUserAgent()
			)
		);
		if ( is_wp_error( $response ) ) {
			return;
		}
		$response_body = wp_remote_retrieve_body( $response );
		if ( ! $response_body ) {
			return;
		}

		$json_response = json_decode( $response_body );

		// account for parse failures
		if ( $json_response ) {
			return $json_response;
		}
	}
}
