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
 * @coversDefaultClass \Twitter\Helpers\Validators\Hashtag
 */
final class Hashtag extends \PHPUnit_Framework_TestCase
{

    /**
     * Test trimming of a # symbol before the hashtag
     *
     * @since 1.0.0
     *
     * @covers ::trim
     * @small
     *
     * @dataProvider trimmableHashtagProvider
     *
     * @param string $test_string string to test
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testTrim($test_string, $message = '')
    {
        $this->assertEquals(
            'hashtag',
            \Twitter\Helpers\Validators\Hashtag::trim($test_string),
            $message
        );
    }

    /**
     * Define multiple strings that should all evaluate to 'hashtag' after passing through \Twitter\Helpers\Validators\Hashtag::trim
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test string and error string
     * }
     */
    public static function trimmableHashtagProvider()
    {
        return array(
            array( '#hashtag', 'failed to trim symbol' ),
            array( '＃hashtag', 'failed to trim unicode symbol' ),
            array( ' #hashtag ', 'failed to trim whitespace' ),
            array( 'hashtag', 'failed when no trim needed' )
        );
    }
}
