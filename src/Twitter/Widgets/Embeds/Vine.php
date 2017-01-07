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

namespace Twitter\Widgets\Embeds;

/**
 * Display a Vine simple embed
 *
 * @since 2.0.0
 */
class Vine
{
    /**
     * Construct a full Vine URI by appending to base string
     *
     * @since 2.0.0
     *
     * @type string
     */
    const BASE_URL = 'https://vine.co/v/';

    /**
     * Minimum allowed width
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MIN_WIDTH = 100;

    /**
     * Maximum allowed width
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MAX_WIDTH = 600;

    /**
     * Vine ID
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $id;

    /**
     * Width of the embed
     *
     * @since 2.0.0
     *
     * @type int
     */
    protected $width;

    /**
     * Initialize the Vine object with a Vine ID
     *
     * @since 2.0.0
     *
     * @param string $id Vine identifier
     */
    public function __construct($id)
    {
        $this->setID($id);
    }

    /**
     * Get the Vine ID
     *
     * @since 2.0.0
     *
     * @return string Vine ID, or empty string if not set
     */
    public function getID()
    {
        return $this->id ?: '';
    }

    /**
     * Set the Vine ID
     *
     * @since 2.0.0
     *
     * @param string $id Vine identifier
     *
     * @return self support chaining
     */
    public function setID($id)
    {
        if (is_string($id)) {
            $id = trim($id);
            if ($id) {
                $this->id = $id;
            }
        }

        return $this;
    }

    /**
     * Vine URL
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
     * Get the fixed width of the widget display area
     *
     * @since 2.0.0
     *
     * @return int|null specified width of the widget or null if none set
     */
    public function getWidth()
    {
        return $this->width ?: null;
    }

    /**
     * Set the fixed width of the widget display area
     *
     * @since 2.0.0
     *
     * @link https://www.w3.org/wiki/CSS/Properties/max-width CSS max-width
     *
     * @param int $width width of the embed
     *
     * @return self support chaining
     */
    public function setWidth($width)
    {
        if (is_int($width) && $width >= static::MIN_WIDTH && $width <= static::MAX_WIDTH) {
            $this->width = $width;
        }

        return $this;
    }

    /**
     * Create a new Vine object from an associative array of properties
     *
     * @since 2.0.0
     *
     * @param array $options associative array of options {
     *   @type string      option key
     *   @type string|bool option value
     * }
     *
     * @return self new instance of calling class
     */
    public static function fromArray($options)
    {
        // Vine ID required
        if (! (is_array($options) && isset($options['id']) && $options['id'])) {
            return null;
        }

        $class = get_called_class();
        $vine = new $class( $options['id'] );
        unset($class);

        if (isset($options['width']) && method_exists($vine, 'setWidth')) {
            $vine->setWidth($options['width']);
        }

        return $vine;
    }

    /**
     * Output Vine as an array suitable for use as oEmbed query parameters
     *
     * @since 2.0.0
     *
     * @return array Vine parameter array {
     *   @type string query parameter name
     *   @type string query parameter value
     * }
     */
    public function toOEmbedParameterArray()
    {
        $url = $this->getURL();
        if (! $url) {
            return array();
        }
        $oembed = array('url' => $url);

        if ($this->width) {
            $oembed['maxwidth'] = $this->width;
        }

        return $oembed;
    }
}
