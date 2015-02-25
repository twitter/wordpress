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
class ScreenName
{
    /**
     * Remove possible '@' from beginning of a Twitter screen_name
     *
     * @since 1.0.0
     *
     * @param string $screen_name Twitter screen name
     *
     * @return string Twitter screen name
     */
    public static function trim($screen_name)
    {
        return ltrim(trim($screen_name), '@＠');
    }

    /**
     * Tests a supplied Twitter screen_name for validity
     *
     * @since 1.0.0
     *
     * @link https://github.com/twitter/twitter-text/blob/master/java/src/com/twitter/Regex.java Twitter text
     *
     * @param string $screen_name Twitter screen name
     *
     * @return bool true if valid screen name
     */
    public static function isValid($screen_name)
    {
        return (bool) preg_match('/^[a-z0-9_]{1,20}$/i', $screen_name);
    }

    /**
     * Sanitize a user-inputted screen name value
     *
     * Account for a leading @, extra spaces, or a Twitter.com URL
     *
     * @since 1.0.0
     *
     * @param string $screen_name Twitter screen name
     *
     * @return string Twitter screen name or empty string if invalid screen name provided
     */
    public static function sanitize($screen_name)
    {
        if (! is_string($screen_name)) {
            return '';
        }

        $screen_name = trim($screen_name);
        if (! $screen_name) {
            return '';
        }
        $screen_name = trim(rtrim(trim($screen_name), '/'));
        if (! $screen_name) {
            return '';
        }

        $last_slash = strrpos($screen_name, '/');
        if (false !== $last_slash) {
            $screen_name = substr($screen_name, $last_slash + 1);
        }
        $screen_name = static::trim($screen_name);
        if (! $screen_name) {
            return '';
        }

        if (! static::isValid($screen_name)) {
            return '';
        }

        return $screen_name;
    }
}
