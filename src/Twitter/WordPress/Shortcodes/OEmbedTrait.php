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

namespace Twitter\WordPress\Shortcodes;

/**
 * Set up and fetch a Twitter oEmbed capable object
 *
 * @since 1.3.0
 */
trait OEmbedTrait
{
	/**
	 * Get the base set of oEmbed params before applying shortcode customizations
	 *
	 * @since 1.3.0
	 *
	 * @return array associative array of query parameters ready for http_build_query {
	 *   @type string query parameter name
	 *   @type string|bool query parameter value
	 * }
	 */
	public static function getBaseOEmbedParams()
	{
		// omit JavaScript. enqueue separately
		$query_parameters = array(
			'omit_script' => true,
		);

		// attempt to customize text for site language
		$lang = \Twitter\WordPress\Language::localeToTwitterLang();
		if ( $lang ) {
			$query_parameters['lang'] = $lang;
		}

		return $query_parameters;
	}

	/**
	 * Request and parse oEmbed markup from Twitter's servers
	 *
	 * @since 1.3.0
	 *
	 * @param array  $query_parameters query parameters to be passed to the oEmbed endpoint
	 * @param string $cache_key        key to retrieve and store HTML values for the given configuration
	 *
	 * @return string HTML markup returned by the oEmbed endpoint
	 */
	public static function getOEmbedMarkup( $query_parameters, $cache_key )
	{
		if ( ! (is_string( $cache_key ) && $cache_key ) ) {
			return '';
		}

		// check for cached result
		$html = get_transient( $cache_key );
		if ( $html ) {
			return $html;
		}

		$classname = get_called_class();
		if ( ! ( defined( $classname . '::OEMBED_API_CLASS' ) && static::OEMBED_API_CLASS ) ) {
			return '';
		}
		$oembed_api_class = static::OEMBED_API_CLASS;
		if ( ! ( class_exists( $oembed_api_class ) && method_exists( $oembed_api_class, 'getJSON' ) ) ) {
			return '';
		}

		if ( ! isset( $query_parameters['url'] ) ) {
			if ( defined( $classname . '::BASE_URL' ) && isset( $query_parameters['id'] ) ) {
				// convert ID to full URL to allow more flexibility for oEmbed endpoint
				$query_parameters['url'] = static::BASE_URL . $query_parameters['id'];
				unset( $query_parameters['id'] );
			} else {
				return '';
			}
		}

		$allowed_resource_types = array( 'rich' => true, 'video' => true );
		$ttl = DAY_IN_SECONDS;

		$oembed_response = $oembed_api_class::getJSON( static::OEMBED_API_ENDPOINT, $query_parameters );
		if ( ! ($oembed_response
			&& isset( $oembed_response->type )
			&& isset( $allowed_resource_types[ $oembed_response->type ] )
			&& isset( $oembed_response->html )
			&& $oembed_response->html            )
		) {
			// do not rerequest errors with every page request
			set_transient( $cache_key, ' ', $ttl );
			return '';
		}

		$html = $oembed_response->html;

		if ( isset( $oembed_response->cache_age ) ) {
			$ttl = absint( $oembed_response->cache_age );
		}
		set_transient( $cache_key, $html, $ttl );

		return $html;
	}
}
