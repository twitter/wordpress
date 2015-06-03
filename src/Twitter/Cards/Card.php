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
 * Properties common to all Twitter Cards
 *
 * @since 1.0.0
 */
class Card
{

    /**
     * Twitter Card type. summary, summary_large_image, etc.
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $type;

    /**
     * Title of the content
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $title;

    /**
     * Twitter account responsible for site content
     *
     * @since 1.0.0
     *
     * @type \Twitter\Cards\Components\Account
     */
    protected $site;

    /**
     * @since 1.0.0
     *
     * @param string $type card type
     */
    public function __construct($type)
    {
        if (! ( is_string($type) && $type )) {
            // summary default
            $type = 'summary';
        }
        $this->type = $type;
    }

    /**
     * Prepare a passed description for the requirements of a Twitter Card description
     *
     * @since 1.0.0
     *
     * @param string $title title unique to the page
     *
     * @return string sanitized title, or empty string of minimum requirements not met
     */
    public static function sanitizeTitle($title)
    {
        if (! ( is_string($title) && $title )) {
            return '';
        }

        $title = trim($title);
        if (! $title) {
            return '';
        }

        return $title;
    }

    /**
     * Set the title of the content
     *
     * @since 1.0.0
     *
     * @param string $title content title
     *
     * @return __CLASS__ support chaining
     */
    public function setTitle($title)
    {
        $title = static::sanitizeTitle($title);
        if ($title) {
            $this->title = $title;
        }

        return $this;
    }

    /**
     * Set the site associated with content
     *
     * @param \Twitter\Cards\Components\Account $site Twitter account
     *
     * @since 1.0.0
     *
     * @return __CLASS__ support chaining
     */
    public function setSite($site)
    {
        if ($site && is_a($site, '\Twitter\Cards\Components\Account')) {
            $this->site = $site;
        }

        return $this;
    }

    /**
     * Convert class properties to an array
     *
     * @since 1.0.0
     *
     * @return array class properties as an array {
     *   @type string           property name
     *   @type string|int|array property value or an array for nested properties
     * }
     */
    public function toArray()
    {
        if (! ( isset( $this->type ) && $this->type )) {
            return array();
        }

        $card = array( 'card' => $this->type );
        if (isset( $this->title ) && $this->title) {
            $card['title'] = $this->title;
        }

        if (isset( $this->site ) && $this->site) {
            $site = $this->site->asCardProperties();
            if ($site) {
                $card['site'] = $site;
            }
            unset( $site );
        }

        return $card;
    }
}
