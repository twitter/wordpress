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
 * Help build HTML strings
 *
 * Extensible class meant to be extended for use in a framework's HTML builders
 *
 * @since 1.0.0
 */
class HTMLBuilder extends \Twitter\Helpers\HTMLBuilder
{
	/**
	 * Escape HTML class names in the WordPress way
	 *
	 * @since 1.0.0
	 *
	 * @param string $class possible HTML class
	 *
	 * @return string class name stripped of invalid values or empty string
	 */
	public static function escapeClassName( $class )
	{
		return sanitize_html_class( $class );
	}

	/**
	 * Escape an element's inner text
	 *
	 * @since 1.0.0
	 *
	 * @param string $inner_text inner text of a DOM element
	 *
	 * @return string escaped string or empty string if passed string failed to parse
	 */
	public static function escapeInnerText( $inner_text )
	{
		return esc_html( $inner_text );
	}

	/**
	 * Escape an element attribute value
	 *
	 * @since 1.0.0
	 *
	 * @param string $value element attribute value
	 *
	 * @return string escaped string or empty string if passed string failed to parse
	 */
	public static function escapeAttributeValue( $value )
	{
		return esc_attr( $value );
	}

	/**
	 * Escape an element attribute value
	 *
	 * @since 1.0.0
	 *
	 * @param string $url web URL
	 *
	 * @return string escaped string or empty string if passed string failed to parse
	 */
	public static function escapeURL( $url )
	{
		return esc_url( $url, array( 'http', 'https' ) );
	}

	/**
	 * Close a void HTML element in a HTML or xHTML (default)
	 *
	 * @since 1.0.0
	 *
	 * @return string empty string if known HTML, else space and slash
	 */
	public static function closeVoidHTMLElement()
	{
		return (current_theme_supports( 'html5' ) ? '' : ' /');
	}
}
