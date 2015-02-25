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
 * Tweet button markup to be interpreted by Twitter's widget JavaScript
 *
 * @since 1.0.0
 *
 * @link https://dev.twitter.com/web/tweet-button Tweet button documentation
 */
class TweetButton extends BaseWidget
{

    /**
     * HTML class expected by the Twitter widget JS
     *
     * @since 1.0.0
     *
     * @type string
     */
    const HTML_CLASS = 'twitter-share-button';

    /**
     * Class name of the stored web intent
     *
     * @since 1.0.0
     *
     * @type string
     */
    const INTENT_CLASS = '\Twitter\Intents\Tweet';

    /**
     * Allowed values for the count variable
     *
     * @since 1.0.0
     *
     * @type array allowed values {
     *   @type string count value
     *   @type bool exists
     * }
     */
    public static $ALLOWED_COUNT_VALUES = array(
        ''       => true,   // reset to default (horizontal)
        'none'   => true,   // hide Tweet count
        'vertical' => true, // display above Tweet button
    );

    /**
     * Allowed values for the align variable
     *
     * @since 1.0.0
     *
     * @type array allowed values {
     *   @type string align value
     *   @type bool exists
     * }
     */
    public static $ALLOWED_ALIGN_VALUES = array(
        'left'  => true,
        'right' => true,
    );

    /**
     * Tweet Web Intent
     *
     * @since 1.0.0
     *
     * @type \Twitter\Intents\Tweet
     */
    protected $intent;

    /**
     * Size of the Tweet button
     *
     * Large is currently the only supported size.
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $size;

    /**
     * Show the number of Tweets mentioning this URL
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $count = '';

    /**
     * URL to use for Tweet count purposes
     *
     * Count should also be true for the URL used for the count to affect the button
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $counturl;

    /**
     * Force align the button to the left or right of the generated iframe
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $align;

    /**
     * Create a new button. Initialize the web intent.
     *
     * @since 1.0.0
     *
     * @param bool $validate Validate inputs such as screen
     */
    public function __construct($validate = true)
    {
        $intent_class = static::INTENT_CLASS;
        $this->intent = new $intent_class();
        if (! $validate) {
            $this->intent->disableValidation();
        }
    }

    /**
     * Set a new intent
     *
     * @since 1.0.0
     *
     * @param \Twitter\Intents\Tweet $intent Tweet Intent
     *
     * @return __CLASS__ support chaining
     */
    public function setIntent($intent)
    {
        if (is_a($intent, self::INTENT_CLASS)) {
            $this->intent = $intent;
        }

        return $this;
    }

    /**
     * Set the size of the Tweet button
     *
     * @since 1.0.0
     *
     * @param string $size button size
     *
     * @return __CLASS__ support chaining
     */
    public function setSize($size)
    {
        // only one size override supported
        if ('large' === $size) {
            $this->size = $size;
        }
        return $this;
    }

    /**
     * Show the Tweet count next to the Tweet button
     *
     * @since 1.0.0
     *
     * @param string $count a valid count value
     *
     * @return __CLASS__ support chaining
     */
    public function setCount($count)
    {
        if (is_string($count) && isset(static::$ALLOWED_COUNT_VALUES[$count])) {
            $this->count = $count;
        }

        return $this;
    }

    /**
     * Set the URL used for Tweet counts
     *
     * @since 1.0.0
     *
     * @param string $url absolute URL to be used for Tweet count
     *
     * @return __CLASS__ support chaining
     */
    public function setCountURL($url)
    {
        $url = trim($url);
        if ($url) {
            if ($this->intent->shouldValidate()) {
                if ($this->intent->isHTTPURL($url)) {
                    $this->counturl = $url;
                }
            } else {
                $this->counturl = $url;
            }
        }

        return $this;
    }

    /**
     * Force the alignment of the button inside the iframe
     *
     * @since 1.0.0
     *
     * @param string $align left|right
     *
     * @return __CLASS__ support chaining
     */
    public function setAlign($align)
    {
        if (isset(static::$ALLOWED_ALIGN_VALUES[$align])) {
            $this->align = $align;
        }

        return $this;
    }

    /**
     * Define a parent Tweet by ID
     *
     * @since 1.0.0
     *
     * @param string $tweet_id Parent Tweet ID
     *
     * @return __CLASS__ support chaining
     */
    public function setInReplyTo($tweet_id)
    {
        $this->intent->setInReplyTo($tweet_id);

        return $this;
    }

    /**
     * Pre-populate Tweet text
     *
     * @since 1.0.0
     *
     * @param string $text Tweet text
     *
     * @return __CLASS__ support chaining
     */
    public function setText($text)
    {
        $this->intent->setText($text);

        return $this;
    }

    /**
     * Share a URL
     *
     * @since 1.0.0
     *
     * @param string $url absolute URL
     *
     * @return __CLASS__ support chaining
     */
    public function setURL($url)
    {
        $this->intent->setURL($url);

        return $this;
    }

    /**
     * Add a hashtag
     *
     * @since 1.0.0
     *
     * @param string $hashtag hashtag
     *
     * @return __CLASS__ support chaining
     */
    public function addHashtag($hashtag)
    {
        $this->intent->addHashtag($hashtag);

        return $this;
    }

    /**
     * Associate Tweet with a source account
     *
     * @since 1.0.0
     *
     * @param string $username Twitter username
     *
     * @return __CLASS__ support chaining
     */
    public function setVia($username)
    {
        $this->intent->setVia($username);
    }

    /**
     * Add a related Twitter account
     *
     * @since 1.0.0
     *
     * @param string $username Twitter username
     * @param string $label    brief description of how the account relates to the Tweet content
     *
     * @return __CLASS__ support chaining
     */
    public function addRelated($username, $label = '')
    {
        $this->intent->addRelated($username, $label);

        return $this;
    }

    /**
     * Create a Tweet button from an associative array
     *
     * @param array $options {
     *   @type string parameter name
     *   @type string|int|bool parameter value
     * }
     *
     * @return __CLASS__ new button with configured parameters
     */
    public static function fromArray($options)
    {
        if (! is_array($options)) {
            // initialize a Tweet button with default parameters
            $options = array();
        }

        $class = __CLASS__;
        $button = new $class();
        unset( $class );

        // remove values which evaluate to false
        $options = array_filter($options);
        $button->setBaseOptions($options);

        // intent parameters
        $intent_class = static::INTENT_CLASS;
        $button->setIntent($intent_class::fromArray($options));
        unset( $intent_class );

        // button parameters
        if (isset( $options['size'] )) {
            $button->setSize($options['size']);
        }
        if (isset( $options['count'] )) {
            $button->setCount($options['count']);
        }
        if (isset( $options['counturl'] )) {
            $button->setCountURL($options['counturl']);
        }
        if (isset( $options['align'] )) {
            $button->setAlign($options['align']);
        }

        return $button;
    }

    /**
     * Return Tweet button parameters suitable
     *
     * @since 1.0.0
     *
     * @return array Tweet button parameter array {
     *   @type string dashed parameter name
     *   @type string parameter value
     * }
     */
    public function toArray()
    {
        $data = parent::toArray();

        if ($this->size) {
            $data['size'] = $this->size;
        }

        // empty string is default value
        if ($this->count) {
            $data['count'] = $this->count;
        }
        // only include counturl if a count will be shown
        if ('none' !== $this->count && $this->counturl) {
            $data['counturl'] = $this->counturl;
        }
        if ($this->align) {
            $data['align'] = $this->align;
        }

        return $data;
    }

    /**
     * Tweet button HTML
     *
     * @since 1.0.0
     *
     * @param string $anchor_text        inner text of the generated anchor element. Default: Tweet
     * @param string $html_builder_class callable HTML builder with a static anchorElement class
     *
     * @return string HTML markup
     */
    public function toHTML($anchor_text = 'Tweet', $html_builder_class = '\Twitter\Helpers\HTMLBuilder')
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
            $anchor_text,
            array(
                'class' => static::HTML_CLASS,
            ),
            $this->toArray()
        );
    }
}
