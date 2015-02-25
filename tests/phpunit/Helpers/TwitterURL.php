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

namespace Twitter\Tests\Helpers;

/**
 * @coversDefaultClass \Twitter\Helpers\TwitterURL
 */
final class TwitterURL extends \PHPUnit_Framework_TestCase
{
    /**
     * Test building a profile from a passed screen_name
     *
     * @since 1.0.0
     *
     * @covers ::profile
     * @small
     *
     * @dataProvider screenNamesProvider
     *
     * @param string $screen_name screen_name to be used to construct a URL
     * @param string $url expected result
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testProfile($screen_name, $url, $message = '')
    {
        $this->assertEquals(
            $url,
            \Twitter\Helpers\TwitterURL::profile($screen_name),
            $message
        );
    }

    /**
     * Define multiple screen_name values to be tested
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test screen_name, expected URL response, and error string
     * }
     */
    public static function screenNamesProvider()
    {
        return array(
            array( 'twitter', 'https://twitter.com/twitter', 'Did not successfully build a Twitter username URL' ),
            array( 20, '', 'Did not reject passed int' )
        );
    }

    /**
     * Test building a Tweet URL from a passed screen_name and Tweet ID
     *
     * @since 1.0.0
     *
     * @covers ::tweet
     * @small
     *
     * @dataProvider tweetDataProvider
     *
     * @param string $screen_name screen_name to be used to construct a URL
     * @param string|int $status_id Tweet status identifier
     * @param string $url expected result
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testTweet($screen_name, $status_id, $url, $message = '')
    {
        $this->assertEquals(
            $url,
            \Twitter\Helpers\TwitterURL::tweet($screen_name, $status_id),
            $message
        );
    }

    /**
     * Define multiple screen_name and Tweet ID values to be tested
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test screen_name, status ID, expected URL response, and error string
     * }
     */
    public static function tweetDataProvider()
    {
        return array(
            array( 'twitter', '532610627382951936', 'https://twitter.com/twitter/status/532610627382951936', 'Failed to build a Tweet URL from screen_name and Tweet ID' ),
            array( 'twitter', '', '', 'Did not reject an empty status ID' ),
            array( 20, '', '', 'Did not reject a passed int screen_name' ),
        );
    }

    /**
     * Test building a collection URL from a passed screen_name and collection ID
     *
     * @since 1.0.0
     *
     * @covers ::collection
     * @small
     *
     * @dataProvider collectionDataProvider
     *
     * @param string $screen_name screen_name to be used to construct a URL
     * @param string $collection_id collection identifier
     * @param string $url expected result
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testCollection($screen_name, $collection_id, $url, $message = '')
    {
        $this->assertEquals(
            $url,
            \Twitter\Helpers\TwitterURL::collection($screen_name, $collection_id),
            $message
        );
    }

    /**
     * Define multiple screen_name and collection ID values to be tested
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test screen_name, status ID, expected URL response, and error string
     * }
     */
    public static function collectionDataProvider()
    {
        return array(
            array( 'TwitterDev', '539487832448843776', 'https://twitter.com/TwitterDev/timelines/539487832448843776', 'Failed to build a collection URL from screen_name and collection ID' ),
            array( 'TwitterDev', '', '', 'Did not reject an empty collection ID' ),
            array( 20, '', '', 'Did not reject a passed int screen_name' ),
        );
    }
}
