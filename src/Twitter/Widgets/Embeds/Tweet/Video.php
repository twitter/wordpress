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

namespace Twitter\Widgets\Embeds\Tweet;

/**
 * Display a Tweetn with video in a video-focused template
 *
 * @since 2.0.0
 */
class Video extends \Twitter\Widgets\Embeds\Tweet\Base
{
    /**
     * Display template type passed to oEmbed endpoints for a Tweet URL
     *
     * @since 2.0.0
     *
     * @type string
     */
    const WIDGET_TYPE = 'video';

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

        $data['widget-type'] = static::WIDGET_TYPE;

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

        $oembed['widget_type'] = static::WIDGET_TYPE;

        return $oembed;
    }
}
