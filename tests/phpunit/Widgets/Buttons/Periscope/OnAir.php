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
 * @coversDefaultClass \Twitter\Widgets\Buttons\Periscope\OnAir
 */
final class OnAir extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Initialize a new OnAir object using this username
     *
     * @since 2.0.0
     *
     * @type string
     */
    const USERNAME = 'twitter';

    /**
     * OnAir object initialized to 'twitter' screen_name before each test
     *
     * @since 2.0.0
     *
     * @type \Twitter\Widgets\Buttons\Periscope\OnAir
     */
    protected $button;

    /**
     * Initialize a OnAir object before each test
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->button = new \Twitter\Widgets\Buttons\Periscope\OnAir(self::USERNAME, /* validate */ false);
    }

    /**
     * Test getting username
     *
     * @since 2.0.0
     *
     * @covers ::getUsername
     * @small
     *
     * @return void
     */
    public function testGetUsername()
    {
        $this->assertEquals($this->button->getUsername(), self::USERNAME);
    }

    /**
     * Test setting button size
     *
     * @since 2.0.0
     *
     * @covers ::setSize
     * @small
     *
     * @dataProvider sizeProvider
     *
     * @param string $size size
     * @param bool $expected_valid is the passed size expected to pass?
     * @param string $message error message
     *
     * @return void
     */
    public function testSetSize($size, $expected_valid, $message = '')
    {
        $this->button->setSize($size);
        $this->setSizeResult($size, $expected_valid, $message);
    }

    /**
     * Test if hide screen name was successfully set
     *
     * @since 2.0.0
     *
     * @param string $size size
     * @param bool $expected_valid is the passed size expected to pass?
     * @param string $message error message
     *
     * @return void
     */
    protected function setSizeResult($size, $expected_valid, $message = '')
    {
        $property = self::getProperty($this->button, 'size');
        $key = 'size';
        $data = $this->button->toArray();

        if ($expected_valid) {
            $this->assertEquals($size, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Test sizes
     *
     * @since 2.0.0
     *
     * @return array sizes to test {
     *   @type array size string, expected validity, message
     * }
     */
    public static function sizeProvider()
    {
        return array(
            array( 'large', true, 'Failed to set large button size' ),
            array( 'medium', false, 'Accepted an invalid size value' ),
        );
    }

    /**
     * Create a new array with the base required values expected by the ::fromArray constructor
     *
     * @since 2.0.0
     *
     * @return array options array {
     *   @type string key
     *   @type string screen_name
     * }
     */
    protected static function optionsArraySetUp()
    {
        return array(
            'username' => self::USERNAME,
        );
    }

    /**
     * Test setting size from an options array
     *
     * @since 2.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @dataProvider sizeProvider
     *
     * @param string $size size
     * @param bool $expected_valid is the passed size expected to pass?
     * @param string $message error message
     *
     * @return void
     */
    public function testsetSizeFromOptionsArray($size, $expected_valid, $message = '')
    {
        $options = self::optionsArraySetUp();
        $options['size'] = $size;

        $this->button = \Twitter\Widgets\Buttons\Periscope\OnAir::fromArray($options);
        $this->assertNotNull($this->button);
        $this->setSizeResult($size, $expected_valid, $message);
    }
}
