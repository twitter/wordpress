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

namespace Twitter\Tests\WordPress\Shortcodes\Embeds;

/**
 * @group shortcode
 * @coversDefaultClass \Twitter\WordPress\Shortcodes\Embeds\Tweet
 */
final class Tweet extends \WP_UnitTestCase
{
    /**
     * Test handling a regex match for a Tweet URL
     *
     * @since 2.0.0
     *
     * @covers ::linkHandler
     * @small
     *
     * @dataProvider linkRegexResultMismatchProvider
     *
     * @param array $matches test value
     *
     * @return void
     */
    public function testLinkHandlerMismatch($matches)
    {
        $this->assertEquals(
            '',
            \Twitter\WordPress\Shortcodes\Embeds\Tweet::linkHandler($matches, array(), '', array()),
            'Failed to reject a bad regex match response'
        );
    }

    /**
     * Provide regex matches values which should all fail to extract a Tweet ID
     *
     * @since 2.0.0
     *
     * @return array array of test values
     */
    public static function linkRegexResultMismatchProvider()
    {
        return array(
            array( false ),
            array( '' ),
            array( array() ),
            array( array( '', '', '', '' ) ),
        );
    }

    /**
     * Test sanitizing a provided Tweet ID
     *
     * @since 2.0.0
     *
     * @covers ::sanitizeTweetID
     * @small
     *
     * @dataProvider sanitizeTweetIDProvider
     *
     * @param string $tweet_id Tweet ID test value
     *
     * @return void
     */
    public function testSanitizeTweetID($tweet_id)
    {
        $this->assertEquals('20', \Twitter\WordPress\Shortcodes\Embeds\Tweet::sanitizeTweetID($tweet_id), 'Failed to clean up valid Tweet input');
    }

    /**
     * Provide Tweet values which should all evaluate to a Tweet ID of 20
     *
     * @since 2.0.0
     *
     * @return array array of test values
     */
    public static function sanitizeTweetIDProvider()
    {
        return array(
            array( 'https://twitter.com/jack/status/20' ),
            array( 'https://twitter.com/jack/statuses/20' ),
            array( '20' ),
            array( ' 20 ' ),
        );
    }

    /**
     * Shortcode attributes which should all trigger a cards option equal to false
     *
     * @since 2.0.0
     *
     * @return array array of test values
     */
    public static function hideCardsProvider()
    {
        return array(
            array( array( 'cards' => false ) ),
            array( array( 'cards' => 'false' ) ),
            array( array( 'cards' => 'FALSE' ) ),
            array( array( 'cards' => 0 ) ),
            array( array( 'cards' => '0' ) ),
            array( array( 'cards' => 'no' ) ),
            array( array( 'cards' => 'NO' ) ),
            array( array( 'cards' => 'off' ) ),
            array( array( 'cards' => 'OFF' ) ),
            array( array( 'hide_media' => true ) ),
            array( array( 'hide_media' => 'true' ) ),
            array( array( 'hide_media' => 'TRUE' ) ),
            array( array( 'hide_media' => 1 ) ),
            array( array( 'hide_media' => '1' ) ),
            array( array( 'hide_media' => 'yes' ) ),
            array( array( 'hide_media' => 'YES' ) ),
            array( array( 'hide_media' => 'on' ) ),
            array( array( 'hide_media' => 'ON' ) ),
            array( array( 'cards' => false, 'hide_media' => false ) ),
        );
    }

    /**
     * Shortcode attributes which should all trigger a conversation option equal to false
     *
     * @since 2.0.0
     *
     * @return array array of test values
     */
    public static function hideConversationProvider()
    {
        return array(
            array( array( 'conversation' => false ) ),
            array( array( 'conversation' => 'false' ) ),
            array( array( 'conversation' => 'FALSE' ) ),
            array( array( 'conversation' => 0 ) ),
            array( array( 'conversation' => '0' ) ),
            array( array( 'conversation' => 'no' ) ),
            array( array( 'conversation' => 'NO' ) ),
            array( array( 'conversation' => 'off' ) ),
            array( array( 'conversation' => 'OFF' ) ),
            array( array( 'hide_thread' => true ) ),
            array( array( 'hide_thread' => 'true' ) ),
            array( array( 'hide_thread' => 'TRUE' ) ),
            array( array( 'hide_thread' => 1 ) ),
            array( array( 'hide_thread' => '1' ) ),
            array( array( 'hide_thread' => 'yes' ) ),
            array( array( 'hide_thread' => 'YES' ) ),
            array( array( 'hide_thread' => 'on' ) ),
            array( array( 'hide_thread' => 'ON' ) ),
            array( array( 'conversation' => false, 'hide_thread' => false ) ),
        );
    }

    /**
     * Shortcode align values and their expected saved values
     *
     * @since 2.0.0
     *
     * @return array array of test values
     */
    public static function alignProvider()
    {
        return array(
            array( 'left', 'left', 'Failed to accept a left align value' ),
            array( 'LEFT', 'left', 'Failed to accept an ALL CAPS align value' ),
            array( ' left ', 'left', 'Failed to trim spaces from align value' ),
            array( 'center', 'center', 'Failed to accept a center align value' ),
            array( 'right', 'right', 'Failed to accept a right align value' ),
        );
    }

    /**
     * Test building a unique string for shortcode parameters
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
            'mtr',
            \Twitter\WordPress\Shortcodes\Embeds\Tweet::getOEmbedCacheKeyCustomParameters(array(
                'hide_media'  => true,
                'hide_thread' => true,
                'align'       => 'right',
            )),
            'Failed to build the expected cache key component from shortcode customizations'
        );
    }

    /**
     * Test building a unique cache key for requested query parameters
     *
     * @since 2.0.0
     *
     * @covers ::getOEmbedCacheKey
     * @small
     *
     * @return void
     */
    public function testGetOEmbedCacheKey()
    {
        $id = '20';
        $this->assertEquals(
            'tweet_' . $id,
            \Twitter\WordPress\Shortcodes\Embeds\Tweet::getOEmbedCacheKey($id, array()),
            'Unexpected cache key'
        );
    }
}
