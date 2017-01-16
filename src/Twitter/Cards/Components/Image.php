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

namespace Twitter\Cards\Components;

/**
 * Twitter Card image representation
 *
 * @since 1.0.0
 */
class Image
{
    /**
     * Image URL. Must be less than 1 MB in size.
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $src;

    /**
     * Height of the image in whole pixels
     *
     * @since 1.0.0
     *
     * @type int
     */
    protected $width;

    /**
     * Width of the image in whole pixels
     *
     * @since 1.0.0
     *
     * @type int
     */
    protected $height;

    /**
     * A text description of the image
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $alt;

    /**
     * @since 1.0.0
     *
     * @param string $src image URL
     */
    public function __construct($src)
    {
        if (! ( is_string($src) && $src )) {
            return;
        }
        $this->src = $src;
    }

    /**
     * Image URL
     *
     * @since 1.0.0
     *
     * @return string absolute URL
     */
    public function getURL()
    {
        return $this->src ?: '';
    }

    /**
     * Get the width of the image
     *
     * @since 1.0.0
     *
     * @return int width of the image in whole pixels
     */
    public function getWidth()
    {
        return $this->width ?: 0;
    }

    /**
     * Set the width of the image
     *
     * @since 1.0.0
     *
     * @param int $width width of the image in whole pixels
     *
     * @return self support chaining
     */
    public function setWidth($width)
    {
        if (is_int($width) && $width >= 0) {
            $this->width = $width;
        }
        return $this;
    }

    /**
     * Get the height of the image
     *
     * @since 1.0.0
     *
     * @return int height of the image in whole pixels
     */
    public function getHeight()
    {
        return $this->height ?: 0;
    }

    /**
     * Set the height of the image
     *
     * @since 1.0.0
     *
     * @param int $height
     *
     * @return self support chaining
     */
    public function setHeight($height)
    {
        if (is_int($height) && $height >= 0) {
            $this->height = $height;
        }
        return $this;
    }

    /**
     * Get a text description of the image
     *
     * @since 2.0.0
     *
     * @return string a text description of the image
     */
    public function getAlternativeText()
    {
        return $this->alt ?: '';
    }

    /**
     * Set a text description of the image
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function setAlternativeText($alt)
    {
        if (is_string($alt)) {
            $alt = trim($alt);
            if ($alt) {
                $this->alt = $alt;
            }
        }
        return $this;
    }

    /**
     * Convert to card properties
     *
     * @since 1.0.0
     *
     * @return array|string image property as shorthand or full properties
     */
    public function asCardProperties()
    {
        if (! ( isset($this->src) && $this->src )) {
            return '';
        }
        $properties = array(
            'src' => $this->src
        );
        $has_properties = false;
        if (isset($this->alt)) {
            $properties['alt'] = $this->alt;
            $has_properties = true;
        }

        if ($has_properties) {
            return $properties;
        } else {
            return $this->src;
        }
    }
}
