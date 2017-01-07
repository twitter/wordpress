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

namespace Twitter\Widgets\Embeds\Tweet;

/**
 * Basics of a Tweet shared between all display templates
 *
 * @since 2.0.0
 */
abstract class Base extends \Twitter\Widgets\Base
{
    /**
     * Construct a full Twitter URI by appending to base string
     *
     * @since 2.0.0
     *
     * @type string
     */
    const BASE_URL = 'https://twitter.com/_/status/';

    /**
     * Tweet ID
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $id;

    /**
     * Initialize the Tweet object with a Tweet ID
     *
     * @since 2.0.0
     *
     * @param string $id Tweet identifier
     */
    public function __construct($id)
    {
        $this->setID($id);
    }

    /**
     * Test given Tweet ID for validity
     *
     * @since 2.0.0
     *
     * @param string $id Tweet identifier
     *
     * @return bool true if valid, else false
     */
    public static function isValidID($id)
    {
        if (! (is_string($id) && $id)) {
            return false;
        }
        if (function_exists('ctype_digit')) {
            if (ctype_digit($id)) {
                return true;
            }
        } else {
            if (preg_match('/^[0-9]+$/', $id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the Tweet ID
     *
     * @since 2.0.0
     *
     * @return string Tweet ID, or empty string if not set
     */
    public function getID()
    {
        return $this->id ?: '';
    }

    /**
     * Set the Tweet ID. Tests passed Tweet ID for validity before saving
     *
     * @since 2.0.0
     *
     * @param string $id Tweet identifier
     *
     * @return self support chaining
     */
    public function setID($id)
    {
        if (is_string($id)) {
            $id = trim($id);
            if ($id && static::isValidID($id)) {
                $this->id = $id;
            }
        }

        return $this;
    }

    /**
     * Tweet URL
     *
     * The username component is purposely left as a placeholder value: `_`.
     * The URL returned by this function will redirect to a full Tweet URL page complete with the current Twitter username of the Tweet author
     *
     * @since 2.0.0
     *
     * @return string absolute URL or empty string if ID not set
     */
    public function getURL()
    {
        if ($this->id) {
            return static::BASE_URL . $this->id;
        } else {
            return '';
        }
    }

    /**
     * Create a new Tweet object from an associative array of properties
     *
     * @since 2.0.0
     *
     * @param array $options associative array of options {
     *   @type string      option key
     *   @type string|bool option value
     * }
     *
     * @return static|null new instance of calling class or null if minimum requirements not met
     */
    public static function fromArray($options)
    {
        // Tweet ID required
        if (! (is_array($options) && isset($options['id']) && $options['id'])) {
            return null;
        }

        $class = get_called_class();
        $tweet = new $class( $options['id'] );
        unset($class);
        if (method_exists($tweet, 'setBaseOptions')) {
            $tweet->setBaseOptions($options);
        }

        return $tweet;
    }

    /**
     * Convert Tweet object into an array suitable for use as data-* attributes
     *
     * @since 2.0.0
     *
     * @return array associative array of data attribute values or empty if no id set
     */
    public function toArray()
    {
        $data = parent::toArray();

        if ($this->id) {
            $data['id'] = $this->id;
        } else {
            return array();
        }

        return $data;
    }

    /**
     * Output Tweet as an array suitable for use as oEmbed query parameters
     *
     * @since 2.0.0
     *
     * @return array Tweet parameter array {
     *   @type string query parameter name
     *   @type string query parameter value
     * }
     */
    public function toOEmbedParameterArray()
    {
        $oembed = parent::toArray();

        $url = $this->getURL();
        if (! $url) {
            return array();
        }
        $oembed['url'] = $url;

        return $oembed;
    }
}
