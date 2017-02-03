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
 * Display Tweets included in a Twitter Moment
 *
 * @since 2.0.0
 */
class Moment extends \Twitter\Widgets\Embeds\Timeline\Collection
{
    /**
     * Construct a full Twitter URI by appending to base string
     *
     * @since 2.0.0
     *
     * @type string
     */
    const BASE_URL = 'https://twitter.com/i/moments/';

    /**
     * Create a new Moment object
     *
     * @since 2.0.0
     *
     * @param string $id unique identifier of the Moment
     */
    public function __construct($id)
    {
        parent::__construct($id);

        // all moments are grids
        $this->setGridTemplate();
    }

    /**
     * Return Moment parameters suitable for conversion to data-*
     *
     * @since 2.0.0
     *
     * @return array Moment timeline parameter array {
     *   @type string dashed parameter name
     *   @type string parameter value
     * }
     */
    public function toArray()
    {
        $data = parent::toArray();

        unset($data['widget-type']);

        return $data;
    }

    /**
     * Output Moment as an array suitable for use as oEmbed query parameters
     *
     * @since 2.0.0
     *
     * @return array Moment parameter array {
     *   @type string query parameter name
     *   @type string query parameter value
     * }
     */
    public function toOEmbedParameterArray()
    {
        $query_parameters = parent::toOEmbedParameterArray();

        unset($query_parameters['widget_type']);

        return $query_parameters;
    }
}
