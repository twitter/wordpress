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
 * Bundle information about a Twitter account as used in Cards markup
 *
 * @since 1.0.0
 */
class Account
{
    /**
     * Twitter account screen name. Example: ev
     *
     * @since 1.0.0
     *
     * @type string
     */
    public $screen_name;

    /**
     * Twitter account identifier. Example: '20'
     *
     * @since 1.0.0
     *
     * @type string
     */
    public $id;

    /**
     * Create a new account object from a Twitter screen name
     *
     * @since 1.0.0
     *
     * @return self|null
     */
    public static function fromScreenName($screen_name)
    {
        $account = new self();
        $account->setScreenName($screen_name);

        if (! $account->hasScreenName()) {
            return null;
        }

        return $account;
    }

    /**
     * Create a new account object from a Twitter id
     *
     * @since 1.0.0
     *
     * @param string $id Twitter account identifier
     *
     * @return self|null
     */
    public static function fromID($id)
    {
        $account = new self();
        $account->setID($id);

        if (! $account->hasID()) {
            return null;
        }

        return $account;
    }

    /**
     * Set a Twitter screen_name for a Twitter account
     *
     * @since 1.0.0
     *
     * @param string $screen_name Twitter screen name
     *
     * @return self support chaining
     */
    public function setScreenName($screen_name)
    {
        if (! is_string($screen_name)) {
            return $this;
        }

        // remove any preceding @
        $screen_name = \Twitter\Helpers\Validators\ScreenName::trim($screen_name);
        if (! $screen_name) {
            return $this;
        }

        $this->screen_name = $screen_name;

        return $this;
    }

    /**
     * Test if account has screen_name set
     *
     * @since 1.0.0
     *
     * @return bool true if screen_name set and not blank
     */
    public function hasScreenName()
    {
        return (bool) $this->screen_name;
    }

    /**
     * Set a Twitter ID for a Twitter account
     *
     * @since 1.0.0
     *
     * @param string $id Twitter user id
     *
     * @return self support chaining
     */
    public function setID($id)
    {
        $id = trim((string) $id);
        if (! $id) {
            return $this;
        }
        if (function_exists('ctype_digit') && ! ctype_digit($id)) {
            return $this;
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Test if account has ID set
     *
     * @since 1.0.0
     *
     * @return bool true if ID set and not blank
     */
    public function hasID()
    {
        return (bool) $this->id;
    }

    /**
     * Convert the account into a Twitter Card property
     *
     * @since 1.0.0
     *
     * @return string|array site username or id as structured property
     */
    public function asCardProperties()
    {
        if ($this->hasID()) {
            return array( 'id' => $this->id );
        } elseif ($this->hasScreenName()) {
            return '@' . $this->screen_name;
        }
    }
}
