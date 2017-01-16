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

namespace Twitter\WordPress\Head;

/**
 * Generate and output Twitter Cards meta elements
 *
 * @since 1.0.0
 */
class CardsMetaElements
{

	/**
	 * Convert a Twitter card into <meta name="" content=""> pairs
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML <meta> elements
	 */
	public static function buildMetaElements()
	{
		$card = \Twitter\WordPress\Cards\Generator::get();
		if ( ! $card ) {
			return '';
		}

		/**
		 * Filter associative array of Twitter Card values
		 *
		 * Resulting array will be output to the page as <meta> elements
		 * All keys receive the "twitter:" prefix inside a name attribute before output
		 * Structured values are passed as an array. example: 'image' => array( 'src' => 'http://example.com/i.jpg', 'width' => 640 )
		 *
		 * @since 1.0.0
		 *
		 * @param array $card_properties associative array of Twitter Card values {
		 *   @type string card property name
		 *   @type string|int|array property value
		 * }
		 */
		$card_properties = apply_filters( 'twitter_card', $card->toArray() );
		if ( ! is_array( $card_properties ) || empty( $card_properties ) ) {
			return '';
		}

		$html = '';
		foreach ( $card_properties as $name => $content ) {
			if ( is_array( $content ) && $name ) {
				foreach ( $content as $structured_name => $structured_value ) {
					$html .= \Twitter\WordPress\Head\MetaElement::fromNameContentPair(
						( 'src' === $structured_name ) ? $name : $name . ':' . $structured_name,
						$structured_value
					);
				}
			} else {
				$html .= \Twitter\WordPress\Head\MetaElement::fromNameContentPair( $name, $content );
			}
		}
		return $html;
	}

	/**
	 * Output a HTML containing all card elements for the current context
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function outputMetaElements()
	{
		echo "\n";

		// Escaped when building individual elements
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo static::buildMetaElements();

		echo "\n";
	}
}
