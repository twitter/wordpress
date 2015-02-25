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

namespace Twitter\Intents;

/**
 * Link to a follow web intent page
 *
 * @since 1.0.0
 *
 * @link https://dev.twitter.com/web/follow-button/web-intent
 */
class Follow
{

    /**
     * Follow Web Intent URL
     *
     * @since 1.0.0
     *
     * @type string
     */
    const INTENT_URL = 'https://twitter.com/intent/follow';

    /**
     * Twitter handle
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $screen_name;

    /**
     * Construct a new follow intent for the given Twitter screen name
     *
     * @since 1.0.0
     *
     * @param string $screen_name Twitter screen name
     * @param bool $validate      validate screen name matches Twitter username allowed characters and length before saving
     */
    public function __construct($screen_name, $validate = true)
    {
        $screen_name = \Twitter\Helpers\Validators\ScreenName::trim($screen_name);
        if ($screen_name) {
            if (false === $validate || \Twitter\Helpers\Validators\ScreenName::isValid($screen_name)) {
                $this->screen_name = $screen_name;
            }
        }
    }

    /**
     * Retrieve the stored Twitter screen name
     *
     * @since 1.0.0
     *
     * @return string Twitter screen name or empty string if none set
     */
    public function getScreenName()
    {
        return $this->screen_name ?: '';
    }

    /**
     * Return the follow intent URL
     *
     * @since 1.0.0
     *
     * @return string Follow intent URL or empty string if no valid screen name
     */
    public function __toString()
    {
        return $this->getIntentURL();
    }

    /**
     * Follow intent URL
     *
     * @since 1.0.0
     *
     * @return string Follow intent URL or empty string if no valid screen name
     */
    public function getIntentURL()
    {
        if (! $this->screen_name) {
            return '';
        }

        return static::INTENT_URL . '?' . http_build_query(array( 'screen_name' => $this->screen_name ), '', '&', PHP_QUERY_RFC3986);
    }
}
