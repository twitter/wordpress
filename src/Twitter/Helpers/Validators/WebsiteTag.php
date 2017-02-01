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

namespace Twitter\Helpers\Validators;

/**
 * Test for Twitter screen_name validity
 *
 * @since 1.0.0
 */
class WebsiteTag
{
    /**
     * Characters allowed in a Twitter website tag
     *
     * @since 2.0.0
     *
     * @type string
     */
    const ALLOWED_CHARACTERS = 'a-zA-Z0-9';

    /**
     * Maximum allowed length of a Twitter website tag
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MAX_LENGTH = 5;

    /**
     * Combine allowed characters and max length into a pattern suitable for use in HTML form validation or inside a regex matcher
     *
     * @since 2.0.0
     *
     * @link https://tc39.github.io/ecma262/#prod-Pattern JavaScript pattern production
     *
     * @return string pattern string
     */
    public static function getPattern()
    {
        return '[' . static::ALLOWED_CHARACTERS . ']{1,' . static::MAX_LENGTH . '}';
    }

	/**
     * Get a PCRE pattern suitable for use in a matcher
     *
     * @since 2.0.0
     *
     * @link http://php.net/manual/en/pcre.pattern.php PHP PCRE pattern
     *
     * @return string PCRE pattern
     */
    public static function getRegexPattern()
    {
        return '/^' . static::getPattern() . '$/';
    }

	/**
     * Tests a supplied Twitter website tag for validity
     *
     * @since 2.0.0
     *
     * @param string $website_tag Twitter website tag
     *
     * @return bool true if valid Twitter website tag
     */
    public static function isValid($website_tag)
    {
        return (bool) preg_match(static::getRegexPattern(), $website_tag);
    }

	/**
     * Sanitize a user-inputted Twitter website tag value
     *
     * @since 2.0.0
     *
     * @param string $website_tag Twitter website tag
     *
     * @return string Twitter website tag or empty string if invalid website provided
     */
    public static function sanitize($website_tag)
    {
	    if (! is_string($website_tag)) {
            return '';
        }

		$website_tag = strtolower(trim($website_tag));
		if ( ! $website_tag ) {
			return '';
		}

		if (! static::isValid($website_tag)) {
            return '';
        }

		return $website_tag;
	}
}
