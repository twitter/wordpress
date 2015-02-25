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

namespace Twitter\WordPress\Cards;

/**
 * Clean up data from WordPress to prepare for a Twitter Card
 *
 * @since 1.0.0
 */
class Sanitize
{

	/**
	 * Remove HTML, leading and trailing whitespaces, and other unexpected qualities of a plain text string
	 *
	 * @since 1.0.0
	 *
	 * @param string $s string to sanitize
	 *
	 * @return string sanitized string
	 */
	public static function sanitizePlainTextString( $s )
	{
		if ( ! ( is_string( $s ) && $s ) ) {
			return '';
		}

		$s = trim( $s );
		if ( $s ) {
			// strip HTML
			$s = wp_kses( $s, array() );
		}

		return $s;
	}

	/**
	 * Clean up a string before including as a Twitter Card description
	 *
	 * @since 1.0.0
	 *
	 * @param string $description short description of the page
	 *
	 * @return string sanitized short description of the page
	 */
	public static function sanitizeDescription( $description )
	{
		// strip HTML
		$description = static::sanitizePlainTextString( $description );
		if ( ! $description ) {
			return '';
		}

		// strip shortcodes
		$description = strip_shortcodes( $description );
		if ( ! $description ) {
			return '';
		}

		// strip URLs on their own line (possible oEmbeds)
		// @see \WP_Embed::autoembed
		$description = preg_replace( '|^(\s*)(https?://[^\s"]+)(\s*)$|im', '', $description );
		if ( ! $description ) {
			return '';
		}

		$description = wp_trim_words(
			$description,
			/**
			 * Filter excerpt length, measured in number of words, used by a Twitter Card description
			 *
			 * Override the default excerpt length used by the site for a Twitter-specific context
			 * Twitter will truncate text at 200 characters
			 *
			 * @since 1.0.0
			 *
			 * @see wp_trim_excerpt()
			 *
			 * @param int $num_words The number of words in an excerpt
			 */
			apply_filters(
				'twitter_excerpt_length',
				/** This filter is documented in wp-includes/formatting.php */
				apply_filters( 'excerpt_length', 55 )
			),
			/**
			 * Filter the string used after a trimmed excerpt for Twitter Card description text if text is longer than the specified word count
			 *
			 * @since 1.0.0
			 *
			 * @see wp_trim_words()
			 *
			 * @param string $more_string the string shown at the end of generated excerpt text
			 */
			apply_filters(
				'twitter_excerpt_more',
				__( '&hellip;' )
			)
		);
		if ( ! $description ) {
			return '';
		}
		return $description;
	}
}
