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

namespace Twitter\Tests\Intents;

/**
 * @coversDefaultClass \Twitter\Intents\Follow
 */
final class Follow extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * A valid Twitter screen_name
     *
     * @since 1.0.0
     *
     * @type string
     */
    const VALID_SCREEN_NAME = 'twitter';

    /**
     * Invalid Twitter screen name
     *
     * @since 1.0.0
     *
     * @type string
     */
    const INVALID_SCREEN_NAME = 'hello world';

    /**
     * Return a new Follow Intent object for a valid screen_name
     *
     * @since 1.0.0
     *
     * @return \Twitter\Intents\Follow Follow Intent object with an initialized screen_name
     */
    public static function getFollowIntentForValidScreenName()
    {
        return (new \Twitter\Intents\Follow(self::VALID_SCREEN_NAME));
    }

    /**
     * Return a new Follow Intent object for an invalid screen_name
     *
     * @since 1.0.0
     *
     * @return \Twitter\Intents\Follow Follow Intent object with an uninitialized screen_name
     */
    public static function getFollowIntentForInvalidScreenName()
    {
        return (new \Twitter\Intents\Follow(self::INVALID_SCREEN_NAME));
    }

    /**
     * Return a new Follow Intent object for an invalid screen_name with validation disabled
     *
     * @since 1.0.0
     *
     * @return \Twitter\Intents\Follow Follow Intent object with an initialized screen_name
     */
    public static function getFollowIntentForInvalidScreenNameWithoutValidation()
    {
        return (new \Twitter\Intents\Follow(self::INVALID_SCREEN_NAME, /* validate */ false));
    }

    /**
     * Return a new Follow Intent object for a empty trimmed screen_name
     *
     * @since 1.0.0
     *
     * @return \Twitter\Intents\Follow Follow Intent object with an uninitialized screen_name
     */
    public static function getFollowIntentForEmptyTrimmedScreenName()
    {
        return (new \Twitter\Intents\Follow(' '));
    }

    /**
     * Test screen_name retrieval from Follow Intent object
     *
     * @since 1.0.0
     *
     * @covers ::getScreenName
     * @small
     *
     * @dataProvider getScreenNameProvider
     *
     * @param \Twitter\Intents\Follow follow intent object
     * @param string $expected_result expected result from getScreenName
     * @param string $message error message
     *
     * @return void
     */
    public function testGetScreenName($follow_object, $expected_result, $message = '')
    {
        $this->assertEquals(
            $expected_result,
            $follow_object->getScreenName(),
            $message
        );
    }

    /**
     * Build test vales and expected outputs for getScreenNameTest
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array Follow Intent object, expected getScreenName result, error message
     * }
     */
    public static function getScreenNameProvider()
    {
        return array(
            array( self::getFollowIntentForValidScreenName(), self::VALID_SCREEN_NAME, 'Failed to retrieve a valid screen_name' ),
            array( self::getFollowIntentForInvalidScreenName(), '', 'Failed to reject an invalid screen_name' ),
            array( self::getFollowIntentForInvalidScreenNameWithoutValidation(), self::INVALID_SCREEN_NAME, 'Failed to allow an invalid screen name when validation disabled' ),
            array( self::getFollowIntentForEmptyTrimmedScreenName(), '', 'Failed to reject an empty screen_name' )
        );
    }

    /**
     * Test Follow Intent URL builder from Follow Intent object
     *
     * @since 1.0.0
     *
     * @covers ::getIntentURL
     * @small
     *
     * @dataProvider getIntentURLProvider
     *
     * @param \Twitter\Intents\Follow follow intent object
     * @param string $expected_result expected result from getIntentURL
     * @param string $message error message
     *
     * @return void
     */
    public function testGetIntentURL($follow_object, $expected_result, $message = '')
    {
        $this->assertEquals(
            $expected_result,
            $follow_object->getIntentURL(),
            $message
        );
    }

    /**
     * Build test vales and expected outputs for getScreenNameTest
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array Follow Intent object, expected getScreenName result, error message
     * }
     */
    public static function getIntentURLProvider()
    {
        $base_url = 'https://twitter.com/intent/follow?screen_name=';
        return array(
            array( self::getFollowIntentForValidScreenName(), $base_url . self::VALID_SCREEN_NAME, 'Failed to build a Follow Intent URL from a valid screen_name' ),
            array( self::getFollowIntentForInvalidScreenName(), '', 'Failed to reject an invalid screen_name' ),
            array( self::getFollowIntentForInvalidScreenNameWithoutValidation(), $base_url . rawurlencode(self::INVALID_SCREEN_NAME), 'Failed to build URL for invalid screen name when validation disabled' ),
            array( self::getFollowIntentForEmptyTrimmedScreenName(), '', 'Failed to reject an empty screen_name' )
        );
    }
}
