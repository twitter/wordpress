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

namespace Twitter\Widgets\Embeds\Timeline;

/**
 * Display the latest Tweets from multiple Twitter users
 *
 * @since 2.0.0
 */
class TwitterList extends Profile
{
    /**
     * Short name of the list. Unique by list owner
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $slug;

    /**
     * Create a new list timeline for a given owner screen name and list slug
     *
     * @since 2.0.0
     *
     * @param string $screen_name Twitter handle
     * @param string $slug        Slug unique to the owner's account
     */
    public function __construct($screen_name, $slug)
    {
        parent::__construct($screen_name);

        if ($this->screen_name) {
            $this->setSlug($slug);
        }
    }

    /**
     * Test a supplied slug string for expected characters and length
     *
     * @since 2.0.0
     *
     * @param string $slug unique text identifier
     *
     * @return bool true if valid list slug
     */
    public static function isValidSlug($slug)
    {
        return (bool) preg_match('/^[a-z][a-z0-9_\\-]{0,24}$/i', $slug);
    }

    /**
     * Get the list short name
     *
     * @since 2.0.0
     *
     * @return string list slug or empty string if no slug set
     */
    public function getSlug()
    {
        return $this->slug ?: '';
    }

    /**
     * Set the unique identifier for the list
     *
     * @since 2.0.0
     *
     * @param string $slug unique text identifier
     *
     * @return self support chaining
     */
    public function setSlug($slug)
    {
        if (is_string($slug) && $slug) {
            $slug = trim($slug);
            if (static::isValidSlug($slug)) {
                $this->slug = $slug;
            }
        }

        return $this;
    }

    /**
     * Twitter list URL
     *
     * @since 2.0.0
     *
     * @return string Twitter list URL or empty string if no screen_name or slug set
     */
    public function getURL()
    {
        if ($this->screen_name && $this->slug) {
            $url = parent::getURL();
            if ($url) {
                return $url . '/lists/' . $this->slug;
            }
        }

        return '';
    }

    /**
     * Create a list timeline object from an associative array
     *
     * @since 2.0.0
     *
     * @param array $options {
     *   @type string          parameter name
     *   @type string|int|bool parameter value
     * }
     *
     * @return self|null new List object with configured properties
     */
    public static function fromArray($options)
    {
        // screen-name required
        if (! (is_array($options) && isset($options['screen_name']) && isset($options['slug']) )) {
            return null;
        }

        $screen_name = null;
        $slug = null;
        if (is_string($options['screen_name']) && $options['screen_name']) {
            $screen_name = $options['screen_name'];
            if (is_string($options['slug']) && $options['slug']) {
                $slug = $options['slug'];
            }
        }

        if (! ($screen_name && $slug)) {
            return null;
        }

        unset($options['screen_name']);
        unset($options['slug']);

        $class = __CLASS__;
        $timeline = new $class( $screen_name, $slug );
        unset($class);

        if (method_exists($timeline, 'setBaseOptions')) {
            $timeline->setBaseOptions($options);
        }

        return $timeline;
    }

    /**
     * Return list timeline parameters suitable for conversion to data-*
     *
     * @since 2.0.0
     *
     * @return array list timeline parameter array {
     *   @type string dashed parameter name
     *   @type string parameter value
     * }
     */
    public function toArray()
    {
        $data = parent::toArray();

        if (!isset($data['screen-name'])) {
            return array();
        }

        if ($this->slug) {
            $data['slug'] = $this->slug;
        } else {
            return array();
        }

        return $data;
    }
}
