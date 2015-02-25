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

namespace Twitter\Helpers;

/**
 * Reference Twitter URLs
 *
 * @since 1.0.0
 */
class TwitterURL
{
    /**
     * Scheme, FQDN, and path to Twitter web
     *
     * @since 1.0.0
     *
     * @type string
     */
    const BASE_URL = 'https://twitter.com/';

    /**
     * A Twitter user profile page
     *
     * @since 1.0.0
     *
     * @param string $screen_name Twitter screen name
     *
     * @return string absolute URI of a Twitter profile page
     */
    public static function profile($screen_name)
    {
        if (! ( is_string($screen_name) && $screen_name )) {
            return '';
        }
        return self::BASE_URL . $screen_name;
    }

    /**
     * Individual Tweet / status URI
     *
     * @since 1.0.0
     *
     * @param string $screen_name Twitter account screen name
     * @param string|int $status_id status ID
     *
     * @return string Tweet detail page absolute URI
     */
    public static function tweet($screen_name, $status_id)
    {
        $profile_url = static::profile($screen_name);
        if (! $profile_url) {
            return '';
        }
        $status_id = (string) $status_id;
        if (! $status_id) {
            return '';
        }

        return $profile_url . '/status/' . $status_id;
    }

    /**
     * Twitter Collection / Custom Timeline
     *
     * @since 1.0.0
     *
     * @param string $screen_name Twitter account screen name
     * @param string $collection_id Twitter collection numeric identifier
     *
     * @return string Twitter collection absolute URI
     */
    public static function collection($screen_name, $collection_id)
    {
        if (! ( $screen_name && $collection_id )) {
            return '';
        }

        $profile_url = static::profile($screen_name);
        if (! $profile_url) {
            return '';
        }

        return $profile_url . '/timelines/' . $collection_id;
    }
}
