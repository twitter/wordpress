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

namespace Twitter\Tests\WordPress\Shortcodes\Embeds\Tweet;

/**
 * @group shortcode
 * @coversDefaultClass \Twitter\WordPress\Shortcodes\Embeds\Tweet\Video
 */
final class Video extends \PHPUnit_Framework_TestCase
{

    /**
     * Test extracting a Tweet ID from a shortcode through id attribute
     *
     * @since 2.0.0
     *
     * @covers ::sanitizeShortcodeParameters
     * @small
     *
     * @dataProvider tweetIDProvider
     *
     * @param string $tweet_id Tweet ID test value
     *
     * @return void
     */
    public function testSanitizeShortcodeParametersID($tweet_id)
    {
        $expected = '560070183650213889';

        $options = \Twitter\WordPress\Shortcodes\Embeds\Tweet\Video::sanitizeShortcodeParameters(array( 'id' => $tweet_id ));
        $this->assertTrue(( isset($options['id']) && $options['id'] === $expected ), 'Failed to extract ID from attribute');
    }

    /**
     * Provide Tweet values which should all evaluate to a Tweet ID of 20
     *
     * @since 2.0.0
     *
     * @return array array of test values
     */
    public static function tweetIDProvider()
    {
        return array(
            array( 'https://twitter.com/twitter/status/560070183650213889' ),
            array( 'https://twitter.com/twitter/statuses/560070183650213889' ),
            array( '560070183650213889' ),
            array( ' 560070183650213889 ' ),
        );
    }

    /**
     * Test building a unique cache key component for shortcode customizations
     *
     * @since 2.0.0
     *
     * @covers ::getOEmbedCacheKeyCustomParameters
     * @small
     *
     * @return void
     */
    public function testGetOEmbedCacheKeyCustomParameters()
    {
        $this->assertEquals(
            '',
            \Twitter\WordPress\Shortcodes\Embeds\Tweet\Video::getOEmbedCacheKeyCustomParameters(array( 'hide_tweet' => true )),
            'Failed to set an empty cache modifier key'
        );
    }
}
