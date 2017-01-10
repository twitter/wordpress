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

namespace Twitter\Widgets\Buttons;

/**
 * Tweet button markup to be interpreted by Twitter's widget JavaScript
 *
 * @since 1.0.0
 *
 * @link https://dev.twitter.com/web/tweet-button Tweet button documentation
 */
class Tweet extends \Twitter\Widgets\Base
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
     * @return self support chaining
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
     * @return self support chaining
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
     * Define a parent Tweet by ID
     *
     * @since 1.0.0
     *
     * @param string $tweet_id Parent Tweet ID
     *
     * @return self support chaining
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
     * @return self support chaining
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
     * @return self support chaining
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
     * @return self support chaining
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
     * @return self support chaining
     */
    public function setVia($username)
    {
        $this->intent->setVia($username);

        return $this;
    }

    /**
     * Add a related Twitter account
     *
     * @since 1.0.0
     *
     * @param string $username Twitter username
     * @param string $label    brief description of how the account relates to the Tweet content
     *
     * @return self support chaining
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
     * @return self new button with configured parameters
     */
    public static function fromArray($options)
    {
        if (! is_array($options)) {
            // initialize a Tweet button with default parameters
            $options = array();
        }

        $class = get_called_class();
        $button = new $class();
        unset($class);

        // remove values which evaluate to false
        $options = array_filter($options);
        $button->setBaseOptions($options);

        // intent parameters
        $intent_class = static::INTENT_CLASS;
        $button->setIntent($intent_class::fromArray($options));
        unset($intent_class);

        // button parameters
        if (isset($options['size'])) {
            $button->setSize($options['size']);
        }

        return $button;
    }

    /**
     * Return Tweet button parameters suitable for conversion to data-*
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
