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

namespace Twitter\Widgets\Embeds\Timeline;

/**
 * Display the latest Tweets from a Twitter user / profile
 *
 * @since 2.0.0
 */
class Profile extends \Twitter\Widgets\Embeds\Timeline
{
    /**
     * Construct a full Twitter URI by appending to base string
     *
     * @since 2.0.0
     *
     * @type string
     */
    const BASE_URL = 'https://twitter.com/';

    /**
     * Twitter handle
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $screen_name;

    /**
     * Create a new profile timeline for a given screen name
     *
     * @since 2.0.0
     *
     * @param string $screen_name Twitter handle
     */
    public function __construct($screen_name)
    {
        $this->setScreenName($screen_name);
    }

    /**
     * Get the Twitter handle associated with the object
     *
     * @since 2.0.0
     *
     * @return string Twitter handle or empty string if not set
     */
    public function getScreenName()
    {
        return $this->screen_name ?: '';
    }

    /**
     * Set the Twitter handle
     *
     * @since 2.0.0
     *
     * @param string $screen_name Twitter username / screen name
     *
     * @return self support chaining
     */
    public function setScreenName($screen_name)
    {
        $screen_name = \Twitter\Helpers\Validators\ScreenName::sanitize($screen_name);
        if ($screen_name) {
            $this->screen_name = $screen_name;
        }

        return $this;
    }

    /**
     * Twitter profile URL
     *
     * @since 2.0.0
     *
     * @return string Twitter profile URL or empty string if no screen_name set
     */
    public function getURL()
    {
        if ($this->screen_name) {
            return static::BASE_URL . $this->screen_name;
        }

        return '';
    }

    /**
     * Create a profile timeline object from an associative array
     *
     * @since 2.0.0
     *
     * @param array $options {
     *   @type string          parameter name
     *   @type string|int|bool parameter value
     * }
     *
     * @return self|null new Profile object with configured properties
     */
    public static function fromArray($options)
    {
        // screen_name required
        if (! (is_array($options) && isset($options['screen_name']))) {
            return null;
        }

        if (! (is_string($options['screen_name']) && $options['screen_name'])) {
            return null;
        }

        $class = get_called_class();
        $timeline = new $class( $options['screen_name'] );
        unset($class);
        unset($options['screen_name']);

        if (method_exists($timeline, 'setBaseOptions')) {
            $timeline->setBaseOptions($options);
        }

        return $timeline;
    }

    /**
     * Return profile timeline parameters suitable for conversion to data-*
     *
     * @since 2.0.0
     *
     * @return array profile timeline parameter array {
     *   @type string dashed parameter name
     *   @type string parameter value
     * }
     */
    public function toArray()
    {
        $data = parent::toArray();

        if ($this->screen_name) {
            $data['screen-name'] = $this->screen_name;
        } else {
            return array();
        }

        return $data;
    }

    /**
     * Output timeline as an array suitable for use as oEmbed query parameters
     *
     * @since 2.0.0
     *
     * @return array profile timeline parameter array {
     *   @type string query parameter name
     *   @type string query parameter value
     * }
     */
    public function toOEmbedParameterArray()
    {
        $data = parent::toOEmbedParameterArray();

        $url = $this->getURL();
        if (! $url) {
            return array();
        }
        $data['url'] = $url;

        return $data;
    }
}
