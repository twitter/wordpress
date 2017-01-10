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
 * Base properties used across all Twitter widgets
 *
 * @since 1.0.0
 */
abstract class Base
{

    /**
     * Opt out of tailoring content and suggestions for Twitter users
     *
     * @since 1.0.0
     *
     * @link https://support.twitter.com/articles/20169421 About tailored suggestions
     *
     * @type bool
     */
    protected $dnt = false;

    /**
     * Requested language for translatable strings in the widget
     *
     * @since 1.0.0
     *
     * @type string
     */
    protected $lang;

    /**
     * Opt-out of tailoring content and suggestions for Twitter users
     *
     * @since 1.0.0
     *
     * @return self support chaining
     */
    public function doNotTrack()
    {
        $this->dnt = true;
        return $this;
    }

    /**
     * Reset the do not track preference back to its default state: false
     *
     * @since 1.0.0
     *
     * @return self support chaining
     */
    public function allowTracking()
    {
        $this->dnt = false;
        return $this;
    }

    /**
     * Explicitly set a Twitter-supported language code for translatable strings in a button or widget
     *
     * @since 1.0.0
     *
     * @uses \Twitter\Widgets\Language::isSupportedLanguage verify language parameter before saving
     *
     * @param string $lang Twitter-supported language code
     *
     * @return self support chaining
     */
    public function setLanguage($lang)
    {
        if ($lang && \Twitter\Widgets\Language::isSupportedLanguage($lang)) {
            $this->lang = $lang;
        }

        return $this;
    }

    /**
     * Populate Base options from a passed associative array
     *
     * @since 1.0.0
     *
     * @param array $options associative array of options {
     *   @type string option name
     *   @type string|int|bool option value
     * }
     *
     * @return self support chaining
     */
    public function setBaseOptions($options)
    {
        if (isset($options['dnt']) && ( true === $options['dnt'] || 'true' === $options['dnt'] || 'on' === $options['dnt'] || 1 == $options['dnt'] )) {
            $this->doNotTrack();
        }

        if (isset($options['lang']) && $options['lang']) {
            $this->setLanguage($options['lang']);
        }

        return $this;
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
        $data = array();

        if (true === $this->dnt) {
            $data['dnt'] = 'true';
        }

        if ($this->lang) {
            $data['lang'] = $this->lang;
        }

        return $data;
    }
}
