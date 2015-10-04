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

namespace Twitter\WordPress\JavaScriptLoaders;

/**
 * Load the remotely hosted Twitter advertising tracking JavaScript
 *
 * @since 1.0.0
 */
class Tracking
{
	/**
	 * Twitter tracking JavaScript handle
	 *
	 * Used in WordPress JavaScript queue
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const QUEUE_HANDLE = 'twitter-tracking';

	/**
	 * Twitter advertising JavaScript absolute URI
	 *
	 * @since 1.2.0
	 *
	 * @type string
	 */
	const URI = 'https://platform.twitter.com/oct.js';

	/**
	 * Proactively resolve Twitter advertising JS FQDN asynchronously before later use
	 *
	 * @since 1.0.0
	 *
	 * @link http://dev.chromium.org/developers/design-documents/dns-prefetching Chromium prefetch behavior
	 * @link https://developer.mozilla.org/en-US/docs/Controlling_DNS_prefetching Firefox prefetch behavior
	 *
	 * @return void
	 */
	public static function dnsPrefetch()
	{
		echo '<link rel="dns-prefetch" href="//platform.twitter.com"';
		echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		echo '>' . "\n";
	}

	/**
	 * Register Twitter advertising JavaScript
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register()
	{
		wp_register_script(
			self::QUEUE_HANDLE,
			self::URI,
			array(), // no dependencies
			null, // no not add extra query parameters for cache busting
			true // in footer
		);
	}

	/**
	 * Enqueue the advertising JavaScript
	 *
	 * @since 1.0.0
	 *
	 * @uses wp_enqueue_script()
	 *
	 * @return void
	 */
	public static function enqueue()
	{
		wp_enqueue_script( self::QUEUE_HANDLE );
	}
}
