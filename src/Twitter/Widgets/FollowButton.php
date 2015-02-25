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

namespace Twitter\Widgets;

/**
 * Follow button markup to be interpreted by Twitter's widget JavaScript
 *
 * @since 1.0.0
 *
 * @link https://dev.twitter.com/web/follow-button Follow button documentation
 */
class FollowButton extends BaseWidget
{

    /**
     * HTML class expected by the Twitter widget JS
     *
     * @since 1.0.0
     *
     * @type string
     */
    const HTML_CLASS = 'twitter-follow-button';

    /**
     * Allowed values for the size property
     *
     * @since 1.0.0
     *
     * @type array allowed sizes {
     *   @type string size
     *   @type bool exists
     * }
     */
    public static $ALLOWED_SIZES = array( 'medium' => true, 'large' => true );

    /**
     * Show the current number of followers alongside the button
     *
     * @since 1.0.0
     *
     * @type bool
     */
    protected $show_count = true;

    /**
     * Follow Web Intent
     *
     * @since 1.0.0
     *
     * @type \Twitter\Intents\Follow
     */
    protected $intent;

    /**
     * Show or hide the screen name of the account
     *
     * @since 1.0.0
     *
     * @type bool
     */
    protected $show_screen_name = true;

    /**
     * Size of the button
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $size;

    /**
     * Require screen name. Initialize Follow Web Intent
     *
     * @since 1.0.0
     *
     * @param string $screen_name Twitter account name
     * @param bool $validate validate screen name matches Twitter username allowed characters and length before saving
     */
    public function __construct($screen_name, $validate = true)
    {
        $this->intent = new \Twitter\Intents\Follow($screen_name, $validate);
    }

    /**
     * Show the number of followers alongside the Follow button
     *
     * @since 1.0.0
     *
     * @return __CLASS__ support chaining
     */
    public function showCount()
    {
        $this->show_count = true;
        return $this;
    }

    /**
     * Show only the Follow button, without a follower count
     *
     * @since 1.0.0
     *
     * @return __CLASS__ support chaining
     */
    public function hideCount()
    {
        $this->show_count = false;
        return $this;
    }

    /**
     * Return the screen name of the Twitter user
     *
     * @since 1.0.0
     *
     * @return string Twitter screen name, or blank string if no screen name stored
     */
    public function getScreenName()
    {
        return $this->intent->getScreenName();
    }

    /**
     * Show the screen name to follow inside the Follow button
     *
     * @since 1.0.0
     *
     * @return __CLASS__ support chaining
     */
    public function showScreenName()
    {
        $this->show_screen_name = true;
        return $this;
    }

    /**
     * Hide the screen name from display inside the Follow button
     *
     * @since 1.0.0
     *
     * @return __CLASS__ support chaining
     */
    public function hideScreenName()
    {
        $this->show_screen_name = false;
        return $this;
    }

    /**
     * Set the desired size of the Follow button
     *
     * @since 1.0.0
     *
     * @param string $size button size
     *
     * @return __CLASS__ support chaining
     */
    public function setSize($size)
    {
        if ($size && isset(static::$ALLOWED_SIZES[$size])) {
            $this->size = $size;
        }
        return $this;
    }


    /**
     * Build a Follow button object from an associative array
     *
     * @since 1.0.0
     *
     * @param array $options associative array of options {
     *   @type string option name
     *   @type string|int|bool option value
     * }
     *
     * @return __CLASS__ support chaining
     */
    public static function fromArray($options)
    {
        if (! isset( $options['screen_name'] ) && $options['screen_name']) {
            return;
        }

        $class = __CLASS__;
        $follow = new $class( $options['screen_name'] );
        unset( $class );

        $follow->setBaseOptions($options);

        if (isset( $options['show_count'] ) && ( false === $options['show_count'] || 'false' === $options['show_count'] || 0 == $options['show_count'] )) {
            $follow->hideCount();
        }
        if (isset( $options['show_screen_name'] ) && ( false === $options['show_screen_name'] || 'false' === $options['show_screen_name'] || 0 == $options['show_screen_name'] )) {
            $follow->hideScreenName();
        }

        if (isset( $options['size'] ) && 'medium' !== $options['size']) {
            $follow->setSize($options['size']);
        }

        return $follow;
    }

    /**
     * Convert the class object into an array, removing default field values
     *
     * @since 1.0.0
     *
     * @return array properties as associative array
     */
    public function toArray()
    {
        $data = parent::toArray();

        if (false === $this->show_screen_name) {
            $data['show-screen-name'] = 'false';
        }
        if (false === $this->show_count) {
            $data['show-count'] = 'false';
        }
        if ($this->size && 'medium' !== $this->size) {
            $data['size'] = $this->size;
        }

        return $data;
    }

    /**
     * Generate HTML to encourage follow behavior and expose data to the Twitter for Websites JavaScript
     *
     * @since 1.0.0
     *
     * @param string $anchor_text inner text of the generated anchor element. Supports a single '%s' screen name passed through sprintf. Default: Follow %s
     * @param string $html_builder_class callable HTML builder with a static anchorElement class
     *
     * @return string HTML markup or empty string if minimum requirements not met
     */
    public function toHTML($anchor_text = 'Follow %s', $html_builder_class = '\Twitter\Helpers\HTMLBuilder')
    {
        if (! ( is_string($anchor_text) && $anchor_text )) {
            return '';
        }

        // test for invalid passed class
        if (! ( class_exists($html_builder_class) && method_exists($html_builder_class, 'anchorElement') )) {
            return '';
        }

        $intent_url = $this->intent->getIntentURL();
        // no screen name stored
        if (! $intent_url) {
            return '';
        }

        return $html_builder_class::anchorElement(
            $intent_url,
            sprintf($anchor_text, '@' . $this->getScreenName()),
            array(
                'class' => static::HTML_CLASS,
            ),
            $this->toArray()
        );
    }
}
