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

namespace Twitter\Intents\Traits;

/**
 * Accounts related to web intent
 *
 * May be used to suggest accounts to follow after completing an action
 *
 * @since 2.0.0
 */
trait Related
{
    /**
     * Related Twitter usernames
     *
     * May be presented as a suggested account to follow after the Tweet is published
     *
     * @since 2.0.0
     *
     * @type array {
     *   @type string username in lowercase
     *   @type string description of how the username relates to Tweet content
     * }
     */
    protected $related = array();

    /**
     * Add a related Twitter account
     *
     * @since 2.0.0
     *
     * @param string $username Twitter username
     * @param string $label brief description of how the account relates to the Tweet content
     *
     * @return self support chaining
     */
    public function addRelated($username, $label = '')
    {
        $username = \Twitter\Helpers\Validators\ScreenName::trim($username);
        if ($username) {
            // normalize passed parameter
            $comparison_username = strtolower($username);
            if (! isset($this->related[ $comparison_username ])) {
                if (property_exists(get_called_class(), 'validate_inputs') && $this->validate_inputs) {
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
     * @since 2.0.0
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
}
