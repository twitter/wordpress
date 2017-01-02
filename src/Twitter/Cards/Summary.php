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

namespace Twitter\Cards;

/**
 * Twitter summary card
 *
 * @since 1.0.0
 *
 * @link https://dev.twitter.com/docs/cards/types/summary-card Twitter Summary Card
 */
class Summary extends Card
{
    use \Twitter\Cards\Components\Creator;
    use \Twitter\Cards\Components\Description;
    use \Twitter\Cards\Components\SingleImage;

    /**
     * Twitter Card type value
     *
     * @since 1.0.0
     *
     * @type string
     */
    const TYPE = 'summary';

    /**
     * Minimum width of the image in whole pixels
     *
     * @since 1.0.0
     *
     * @type int
     */
    const MIN_IMAGE_WIDTH = 120;

    /**
     * Minimum height of the image in whole pixels
     *
     * @since 1.0.0
     *
     * @type int
     */
    const MIN_IMAGE_HEIGHT = 120;

    /**
     * Set the card type
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        parent::__construct(static::TYPE);
    }

    /**
     * Convert to an array suitable for use as Twitter Card structured properties
     *
     * @since 1.0.0
     *
     * @return array {
     *  @type string Twitter card property
     *  @type mixed property value
     * }
     */
    public function toArray()
    {
        $card = parent::toArray();

        if (isset($this->description) && $this->description) {
            $card['description'] = $this->description;
        }

        $image_properties = $this->imageCardProperties();
        if (! empty($image_properties)) {
            $card['image'] = $image_properties;
        }
        unset($image_properties);

        if (isset($this->creator) && $this->creator) {
            $creator = $this->creator->asCardProperties();
            if ($creator) {
                $card['creator'] = $creator;
            }
            unset($creator);
        }

        return $card;
    }
}
