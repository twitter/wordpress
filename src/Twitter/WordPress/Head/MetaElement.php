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
 * Build a HTML meta element
 *
 * @since 1.0.0
 *
 * @link http://www.whatwg.org/specs/web-apps/current-work/multipage/semantics.html#the-meta-element meta element
 */
class MetaElement
{
	/**
	 * Meta element name prefix
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const PREFIX = 'twitter:';

	/**
	 * Build a HTML meta element from name and content values
	 *
	 * @since 1.0.0
	 *
	 * @param string $name    name attribute value
	 * @param mixed  $content content attribute value. will be converted to string for output
	 *
	 * @return string HTML meta element or empty string if name and content attribute values not provided
	 */
	public static function fromNameContentPair( $name, $content )
	{
		if ( ! ( $name && $content ) ) {
			return '';
		}

		return '<meta name="' . esc_attr( self::PREFIX . $name ) . '" content="' . esc_attr( (string) $content ) . '"' . \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';
	}
}
