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

namespace Twitter\Tests\Widgets\Buttons;

/**
 * @coversDefaultClass \Twitter\Widgets\Buttons\Tweet
 */
final class Tweet extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Initialized Tweet button object
     *
     * @since 1.0.0
     *
     * @type \Twitter\Widgets\Buttons\Tweet
     */
    protected $button;

    /**
     * Initialize a Tweet object before each test
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->button = new \Twitter\Widgets\Buttons\Tweet();
    }

    /**
     * Test setting button sizes
     *
     * @since 1.0.0
     *
     * @covers ::setSize
     * @small
     *
     * @dataProvider sizeProvider
     *
     * @param string $size button size configuration
     * @param bool $expected_valid expected validity
     * @param string $message error message to display on negative assertion
     *
     * @return void
     */
    public function testSetSize($size, $expected_valid, $message = '')
    {
        $this->button->setSize($size);
        $property = self::getProperty($this->button, 'size');

        if ($expected_valid) {
            $this->assertEquals($size, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Button sizes
     *
     * @since 1.0.0
     *
     * @return array sizes {
     *   @type array size, expected validity, error message
     * }
     */
    public static function sizeProvider()
    {
        return array(
            array( 'large', true, 'Failed to set a valid size' ),
            array( 'small', false, 'Set an invalid size' ),
        );
    }
}
