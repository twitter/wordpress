<?php
/*
The MIT License (MIT)

Copyright (c) 2016 Twitter Inc.

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
 * @since 1.5.0
 */
interface PublishOEmbedEndpoint {
	/**
	 * PHP class to use for fetching oEmbed data
	 *
	 * @since 1.5.0
	 *
	 * @type string
	 */
	const OEMBED_API_CLASS = '\Twitter\WordPress\Helpers\TwitterOEmbed';

	/**
	 * Relative path for the oEmbed API relative to Twitter publishers base path
	 *
	 * @since 1.5.0
	 *
	 * @type string
	 */
	const OEMBED_API_ENDPOINT = 'oembed';
}
