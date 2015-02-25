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
 * Identify the Twitter profile of a site using XFN relationship tokens
 *
 * @since 1.0.0
 */
class AuthorshipLink
{
	/**
	 * Link to a site's Twitter profile using rel me
	 *
	 * Adds via attribution to Twitter widgets on the page
	 *
	 * @link http://microformats.org/wiki/rel-me XFN rel me
	 *
	 * @return void
	 */
	public static function relMe()
	{
		$site_username = \Twitter\WordPress\Site\Username::getViaAttribution( ( in_the_loop() ? get_the_ID() : null ) );
		if ( $site_username ) {
			echo '<link rel="me" href="' . esc_url( \Twitter\Helpers\TwitterURL::profile( $site_username ), array( 'https', 'http' ) ) . '"' . \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement() . '>';
		}
	}
}
