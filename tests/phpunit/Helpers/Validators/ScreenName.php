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

namespace Twitter\Tests\Helpers\Validators;

/**
 * @coversDefaultClass \Twitter\Helpers\Validators\ScreenName
 */
final class ScreenName extends \PHPUnit_Framework_TestCase
{
    /**
     * Test trimming of an @ symbol before the screenname
     *
     * @since 1.0.0
     *
     * @covers ::trim
     * @small
     *
     * @dataProvider trimmableTwitterScreenNameProvider
     *
     * @param string $test_string string to test
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testTrim($test_string, $message = '')
    {
        $this->assertEquals(
            'twitter',
            \Twitter\Helpers\Validators\ScreenName::trim($test_string),
            $message
        );
    }

    /**
     * Define multiple strings that should all evaluate to 'twitter' after passing through \Twitter\Helpers\Validators\ScreenName::trim
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test string and error string
     * }
     */
    public static function trimmableTwitterScreenNameProvider()
    {
        return array(
            array( '@twitter', 'failed to trim symbol' ),
            array( '＠twitter', 'failed to trim unicode symbol' ),
            array( ' @twitter ', 'failed to trim whitespace' ),
            array( 'twitter', 'failed when no trim needed' )
        );
    }

    /**
     * Test the ability to recognize valid Twitter usernames
     *
     * @since 1.0.0
     *
     * @covers ::isValid
     * @small
     *
     * @return void
     */
    public function testValidity()
    {
        $this->assertFalse(
            \Twitter\Helpers\Validators\ScreenName::isValid(''),
            'empty string is never valid'
        );

        $this->assertTrue(
            \Twitter\Helpers\Validators\ScreenName::isValid('twitter'),
            'failed to mark Twitter username as valid'
        );

        $this->assertTrue(
            \Twitter\Helpers\Validators\ScreenName::isValid('abc_123'),
            'alpha numeric with underscore should be valid'
        );

        $this->assertFalse(
            \Twitter\Helpers\Validators\ScreenName::isValid('twitter$ir'),
            'symbols are not allowed'
        );

        $this->assertFalse(
            \Twitter\Helpers\Validators\ScreenName::isValid('Supercalifragilisticexpialidocious'),
            'Twitter usernames should be limited to 20 characters'
        );
    }

    /**
     * Test sanitizing user-provided inputs into a simplified screen_name
     *
     * @since 1.0.0
     *
     * @covers ::sanitize
     * @small
     *
     * @dataProvider sanitizeInputProvider
     *
     * @param string $test_string input possibly in need of a cleanup
     * @param string $message message to display on test failure
     *
     * @return void
     */
    public function testSanitize($test_string, $message = '')
    {
        $this->assertEquals(
            'jack',
            \Twitter\Helpers\Validators\ScreenName::sanitize($test_string),
            $message
        );
    }

    /**
     * Define multiple inputs that should all evaluate to 'jack' after passing through sanitization
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test string and error string
     * }
     */
    public static function sanitizeInputProvider()
    {
        return array(
            array( 'jack', 'Failed when exact input provided' ),
            array( '@jack', 'Failed when @ prefix provided' ),
            array( 'https://twitter.com/jack', 'Failed to simplify full Twitter URL' ),
            array( 'https://twitter.com/jack/', 'Failed to simplify full Twitter URL with trailing slash' ),
        );
    }
}
