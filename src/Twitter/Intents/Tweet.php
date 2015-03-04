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

namespace Twitter\Intents;

/**
 * Influence the Tweet creation flow
 *
 * @since 1.0.0
 *
 * @link https://dev.twitter.com/web/tweet-button/web-intent
 */
class Tweet
{

    /**
     * Tweet Web Intent URL
     *
     * @since 1.0.0
     *
     * @type string
     */
    const INTENT_URL = 'https://twitter.com/intent/tweet';

    /**
     * Validate passed variables before storing
     *
     * @since 1.0.0
     *
     * @type bool
     */
    protected $validate_inputs = true;

    /**
     * Parent Tweet identifier
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $in_reply_to;

    /**
     * Pre-populated Tweet text
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $text;

    /**
     * Share a URL
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $url;

    /**
     * Hashtags to include in the Tweet
     *
     * @since 1.0.0
     *
     * @type array {
     *   @type string comparison hashtag in lowercase
     *   @type string passed hashtag
     * }
     */
    protected $hashtags = array();

    /**
     * Associate a Tweet with an source Twitter account such as the username of your website
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $via;

    /**
     * Related Twitter usernames
     *
     * May be presented as a suggested account to follow after the Tweet is published
     *
     * @since 1.0.0
     *
     * @type array {
     *   @type string username in lowercase
     *   @type string description of how the username relates to Tweet content
     * }
     */
    protected $related = array();

    /**
     * Do not validate inputs
     *
     * Disabling validation may speed up Web Intent generation but may also cause user-facing issues
     *
     * @since 1.0.0
     *
     * @return __CLASS__ support chaining
     */
    public function disableValidation()
    {
        $this->validate_inputs = false;
        return $this;
    }

    /**
     * Validate inputs
     *
     * @since 1.0.0
     *
     * @return __CLASS__ support chaining
     */
    public function enableValidation()
    {
        $this->validate_inputs = true;
        return $this;
    }

    /**
     * Should data be validated before setting?
     *
     * @since 1.0.0
     *
     * @return bool validate inputs
     */
    public function shouldValidate()
    {
        return $this->validate_inputs;
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
        $tweet_id = trim($tweet_id);
        if ($tweet_id) {
            $this->in_reply_to = $tweet_id;
        }

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
        $text = trim($text);
        if ($text) {
            $this->text = $text;
        }

        return $this;
    }

    /**
     * Is the passed URL an absolute URL using the HTTP or HTTPS scheme?
     *
     * @since 1.0.0
     *
     * @param string $url URL to test
     *
     * @return bool true if URL was parsed and contains a HTTP or HTTPs scheme
     */
    public static function isHTTPURL($url)
    {
        if (! ( is_string($url) && $url )) {
            return false;
        }

        try {
            $scheme = parse_url($url, PHP_URL_SCHEME);
        } catch (Exception $e) {
            return false;
        }

        if ('http' === $scheme || 'https' === $scheme) {
            return true;
        }

        return false;
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
        $url = trim($url);
        if ($url) {
            if ($this->validate_inputs) {
                if (static::isHTTPURL($url)) {
                    $this->url = $url;
                }
            } else {
                $this->url = $url;
            }
        }

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
        $hashtag = \Twitter\Helpers\Validators\Hashtag::trim($hashtag);
        if ($hashtag) {
            $comparison_hashtag = mb_strtolower($hashtag);
            if (! isset( $this->hashtags[ $comparison_hashtag ] )) {
                $this->hashtags[ $comparison_hashtag ] = $hashtag;
            }
        }

        return $this;
    }

    /**
     * Get a list of hashtags stored for the Tweet
     *
     * @since 1.0.0
     *
     * @return array hashtags {
     *   @type string hashtag
     * }
     */
    public function getHashtags()
    {
        return array_values($this->hashtags);
    }

    /**
     * Get the stored via value
     *
     * @since 1.0.1
     *
     * @return string source Twitter account username
     */
    public function getVia()
    {
        return $this->via ?: '';
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
        $username = \Twitter\Helpers\Validators\ScreenName::trim($username);
        if ($username) {
            if ($this->validate_inputs) {
                if (\Twitter\Helpers\Validators\ScreenName::isValid($username)) {
                    $this->via = $username;
                }
            } else {
                $this->via = $username;
            }
        }

        return $this;
    }

    /**
     * Add a related Twitter account
     *
     * @since 1.0.0
     *
     * @param string $username Twitter username
     * @param string $label brief description of how the account relates to the Tweet content
     *
     * @return __CLASS__ support chaining
     */
    public function addRelated($username, $label = '')
    {
        $username = \Twitter\Helpers\Validators\ScreenName::trim($username);
        if ($username) {
            // normalize passed parameter
            $comparison_username = strtolower($username);
            if (! isset( $this->related[ $comparison_username ] )) {
                if ($this->validate_inputs) {
                    if (\Twitter\Helpers\Validators\ScreenName::isValid($username)) {
                        $this->related[ $comparison_username ] = trim($label);
                    }
                } else {
                    $this->related[ $comparison_username ] = trim($label);
                }
            }
        }

        return $this;
    }

    /**
     * Get related Twitter usernames
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type string username in lowercase
     *   @type string description of how the username relates to Tweet content
     * }
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Construct a new Tweet intent object from an options array
     *
     * @since 1.0.0
     *
     * @param array $values options array {
     *   @type string option name
     *   @type string|int|bool option value
     * }
     *
     * @return __CLASS__ object initialized based on passed array values
     */
    public static function fromArray($values)
    {
        if (! is_array($values)) {
            $values = array();
        }

        $class = __CLASS__;
        $intent = new $class;
        unset( $class );

        if (isset( $values['validate'] )) {
            if (false == $values['validate'] || 'false' === $values['validate'] || 0 == $values['validate']) {
                $intent->disableValidation();
            }
        }

        // remove values which evaluate to false
        $values = array_filter($values);

        // intent parameters
        if (isset( $values['in_reply_to'] )) {
            $intent->setInReplyTo($values['in_reply_to']);
        }
        if (isset( $values['text'] )) {
            $intent->setText($values['text']);
        }
        if (isset( $values['url'] )) {
            $intent->setURL($values['url']);
        }
        if (isset( $values['hashtags'] )) {
            $hashtags = array();

            if (is_array($values['hashtags'])) {
                $hashtags = $values['hashtags'];
            } else {
                $hashtags = explode(',', $values['hashtags']);
            }

            if (! empty( $hashtags )) {
                array_walk($hashtags, array( $intent, 'addHashtag' ));
            }

            unset( $hashtags );
        }
        if (isset( $values['via'] )) {
            $intent->setVia($values['via']);
        }
        if (isset( $values['related'] )) {
            $related = array();

            if (is_array($values['related'])) {
                $related = $values['related'];
            } elseif (is_string($values['related'])) {
                $related_accounts = explode(',', $values['related']);
                foreach ($related_accounts as $related_account) {
                    // extract the label
                    $account_pieces = explode(':', $related_account, 2);
                    $related[ $account_pieces[0] ] = ( isset( $account_pieces[1] ) ? rawurldecode($account_pieces[1]) : '' );
                    unset( $account_pieces );
                }
            }

            if (! empty( $related )) {
                foreach ($related as $username => $label) {
                    if (! ( is_string($username) && $username )) {
                        continue;
                    }

                    $intent->addRelated($username, $label);
                }
            }

            unset( $related );
        }

        return $intent;
    }

    /**
     * Convert parameters into an array prepped for use in query parameters (underscores) or data-* attributes (dashed)
     *
     * @since 1.0.0
     *
     * @return array Tweet parameters {
     *   @type string Tweet parameter
     *   @type string parameter value
     * }
     */
    public function toQueryParameters()
    {
        $data = array();

        if ($this->in_reply_to) {
            $data['in_reply_to'] = $this->in_reply_to;
        }
        if ($this->text) {
            $data['text'] = $this->text;
        }
        if ($this->url) {
            $data['url'] = $this->url;
        }

        $hashtags = $this->getHashtags();
        if (! empty( $hashtags )) {
            $data['hashtags'] = implode(',', $hashtags);
        }
        unset( $hashtags );

        if ($this->via) {
            $data['via'] = $this->via;
        }

        if (! empty( $this->related )) {
            $related_value = array();
            foreach ($this->related as $username => $label) {
                if ($label) {
                    $related_value[] = $username . ':' . $label;
                } else {
                    $related_value[] = $username;
                }
            }
            $data['related'] = implode(',', $related_value);
            unset( $related_value );
        }

        return $data;
    }

    /**
     * Tweet intent URL
     *
     * @since 1.0.0
     *
     * @return string Tweet intent URL with query parameters
     */
    public function getIntentURL()
    {
        $query_parameters = $this->toQueryParameters();

        if (! empty( $query_parameters )) {
            return self::INTENT_URL . '?' . http_build_query($query_parameters, '', '&', PHP_QUERY_RFC3986);
        }

        return self::INTENT_URL;
    }
}
