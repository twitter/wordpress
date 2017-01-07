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
 * Display a Tweet
 *
 * @since 2.0.0
 */
class Tweet extends \Twitter\Widgets\Embeds\Tweet\Base
{
    use \Twitter\Widgets\Embeds\Theme;

    /**
     * The Tweet is not floated
     *
     * @since 2.0.0
     *
     * @type string
     */
    const ALIGN_NONE = 'none';

    /**
     * Tweet is floated to the left. Content flows on the right side of the Tweet
     *
     * @since 2.0.0
     *
     * @type string
     */
    const ALIGN_LEFT = 'left';

    /**
     * Tweet is floated to the right. Content flows on the left side of the Tweet
     *
     * @since 2.0.0
     *
     * @type string
     */
    const ALIGN_RIGHT = 'right';

    /**
     * Tweet is aligned center
     *
     * @since 2.0.0
     *
     * @type string
     */
    const ALIGN_CENTER = 'center';

    /**
     * Minimum allowed width
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MIN_WIDTH = 220;

    /**
     * Maximum allowed width
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MAX_WIDTH = 550;

    /**
     * Maximum width of the Tweet
     *
     * @since 2.0.0
     *
     * @type int
     */
    protected $width;

    /**
     * Display photos, videos, and link previews cited in the Tweet
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $cards = true;

    /**
     * Display the parent Tweet if the Tweet is in response to another Tweet
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $conversation = true;

    /**
     * Float the Tweet relative to the parent element
     *
     * @since 2.0.0
     *
     * @link https://www.w3.org/wiki/CSS/Properties/float float CSS property
     *
     * @type string
     */
    protected $align = 'none';

    /**
     * Allowed values for align property
     *
     * @since 2.0.0
     *
     * @type array
     */
    public static $ALLOWED_ALIGN_VALUES = array(
        self::ALIGN_LEFT   => true,
        self::ALIGN_RIGHT  => true,
        self::ALIGN_CENTER => true,
        self::ALIGN_NONE   => true,
    );

    /**
     * Set the fixed maximum used width of the widget display area
     *
     * @since 2.0.0
     *
     * @link https://www.w3.org/wiki/CSS/Properties/max-width CSS max-width
     *
     * @param int $width width of the embed
     *
     * @return \Twitter\Widgets\Embeds\Tweet support chaining
     */
    public function setWidth($width)
    {
        if (is_int($width) && $width >= static::MIN_WIDTH && $width <= static::MAX_WIDTH) {
            $this->width = $width;
        }

        return $this;
    }

    /**
     * Show photos, videos, and link previews cited in the Tweet
     *
     * @since 2.0.0
     *
     * @return \Twitter\Widgets\Embeds\Tweet support chaining
     */
    public function showCards()
    {
        $this->cards = true;

        return $this;
    }

    /**
     * Hide photos, videos, and link previews cited in the Tweet
     *
     * @since 2.0.0
     *
     * @return \Twitter\Widgets\Embeds\Tweet support chaining
     */
    public function hideCards()
    {
        $this->cards = false;

        return $this;
    }

    /**
     * Show the parent Tweet if the Tweet is in response to another Tweet
     *
     * @since 2.0.0
     *
     * @return \Twitter\Widgets\Embeds\Tweet support chaining
     */
    public function showParentTweet()
    {
        $this->conversation = true;

        return $this;
    }

    /**
     * Hide the parent Tweet if the Tweet is in response to another Tweet
     *
     * @since 2.0.0
     *
     * @return \Twitter\Widgets\Embeds\Tweet support chaining
     */
    public function hideParentTweet()
    {
        $this->conversation = false;

        return $this;
    }

    /**
     * Set the float alignment of the Tweet
     *
     * @since 2.0.0
     *
     * @param string $align alignment of the Tweet embed in the container
     *
     * @return \Twitter\Widgets\Embeds\Tweet support chaining
     */
    public function setAlign($align)
    {
        if (is_string($align)) {
            $align = strtolower(trim($align));
            if ($align && isset(static::$ALLOWED_ALIGN_VALUES[$align])) {
                $this->align = $align;
            }
        }

        return $this;
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
     * @return static|null Tweet object or null if minimum requirements not met
     */
    public static function fromArray($options)
    {
        $tweet = parent::fromArray($options);
        if (! $tweet) {
            return null;
        }

        $tweet->setThemeOptions($options);

        if (isset($options['width'])) {
            $tweet->setWidth($options['width']);
        }

        if (isset($options['cards']) && ( false === $options['cards'] || 'false' === $options['cards'] || 0 == $options['cards'] )) {
            $tweet->hideCards();
        }
        if (isset($options['conversation']) && ( false === $options['conversation'] || 'false' === $options['conversation'] || 0 == $options['conversation'] )) {
            $tweet->hideParentTweet();
        }

        if (isset($options['align'])) {
            $tweet->setAlign($options['align']);
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

        if (empty($data)) {
            return array();
        }
        $data = array_merge($data, $this->themeToArray());

        if ($this->width) {
            $data['width'] = $this->width;
        }
        if (false === $this->cards) {
            $data['cards'] = 'false';
        }
        if (false === $this->conversation) {
            $data['conversation'] = 'false';
        }
        if (static::ALIGN_NONE !== $this->align) {
            $data['align'] = $this->align;
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
        $oembed = parent::toOEmbedParameterArray();
        if (empty($oembed)) {
            return array();
        }
        $oembed = array_merge($oembed, $this->themeToOEmbedParameterArray());

        if ($this->width) {
            $oembed['maxwidth'] = $this->width;
        }
        if (false === $this->cards) {
            $oembed['hide_media'] = false;
        }
        if (false === $this->conversation) {
            $oembed['hide_thread'] = false;
        }
        if (static::ALIGN_NONE !== $this->align) {
            $oembed['align'] = $this->align;
        }

        return $oembed;
    }
}
