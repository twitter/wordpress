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
 * @coversDefaultClass \Twitter\Widgets\Follow
 */
final class Follow extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Initialize a new Follow object using this screen_name
     *
     * @since 1.0.0
     *
     * @type string
     */
    const SCREEN_NAME = 'twitter';

    /**
     * Follow object initialized to 'twitter' screen_name before each test
     *
     * @since 1.0.0
     *
     * @type \Twitter\Widgets\Buttons\Follow
     */
    protected $button;

    /**
     * Initialize a Follow object before each test
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->button = new \Twitter\Widgets\Buttons\Follow(self::SCREEN_NAME, /* validate */ false);
    }

    /**
     * Test resetting count option
     *
     * @since 1.0.0
     *
     * @covers ::showCount
     * @small
     *
     * @return void
     */
    public function testShowCount()
    {
        $this->button->hideCount();
        $this->button->showCount();
        $this->assertTrue(self::getProperty($this->button, 'show_count'), 'count not set');
    }

    /**
     * Test if hide screen name was successfully set
     *
     * @since 1.0.0
     *
     * @see ::testShowCount
     *
     * @return void
     */
    protected function hideCountResult()
    {
        $this->assertFalse(self::getProperty($this->button, 'show_count'), 'count not set');
    }

    /**
     * Test setting count option
     *
     * @since 1.0.0
     *
     * @covers ::hideCount
     * @small
     *
     * @return void
     */
    public function testHideCount()
    {
        $this->button->hideCount();

        $this->hideCountResult();
    }

    /**
     * Test getting screen_name
     *
     * The tested function is a getter passthrough to the stored intent
     *
     * @since 1.0.0
     *
     * @covers ::getScreenName
     * @small
     *
     * @return void
     */
    public function testGetScreenName()
    {
        $this->assertEquals($this->button->getScreenName(), self::SCREEN_NAME);
    }

    /**
     * Test hiding show screen_name option
     *
     * @since 1.0.0
     *
     * @covers ::hideScreenName
     * @small
     *
     * @return void
     */
    public function testHideScreenName()
    {
        $this->button->hideScreenName();
        $this->hideScreenNameResult();
    }

    /**
     * Test if hide screen name was successfully set
     *
     * @since 1.0.0
     *
     * @see ::testHideScreenName
     *
     * @return void
     */
    protected function hideScreenNameResult()
    {
        $data = $this->button->toArray();

        $this->assertFalse(self::getProperty($this->button, 'show_screen_name'), 'Failed to hide screen_name');
    }

    /**
     * Test setting show screen_name option
     *
     * @since 1.0.0
     *
     * @covers ::showScreenName
     * @small
     *
     * @return void
     */
    public function testShowScreenName()
    {
        $this->button->hideScreenName();
        $this->button->showScreenName();

        $this->assertTrue(self::getProperty($this->button, 'show_screen_name'), 'show screen name was not successfully reset');
    }

    /**
     * Test setting button size
     *
     * @since 1.0.0
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
     * @since 1.0.0
     *
     * @see ::testHideScreenName
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
     * @since 1.0.0
     *
     * @return array sizes to test {
     *   @type array size string, expected validity, message
     * }
     */
    public static function sizeProvider()
    {
        return array(
            array( 'large', true, 'Failed to set large button size' ),
            array( 'small', false, 'Accepted an invalid size value' ),
        );
    }

    /**
     * Create a new array with the base required values expected by the ::fromArray constructor
     *
     * @since 1.0.0
     *
     * @return array options array {
     *   @type string key
     *   @type string screen_name
     * }
     */
    protected static function optionsArraySetUp()
    {
        return array(
            'screen_name' => self::SCREEN_NAME,
        );
    }

    /**
     * Test setting count from an options array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @dataProvider \Twitter\Tests\CommonProviders::falseyProvider
     *
     * @param string|int|bool $test_value truthy value
     * @param string $message error message
     *
     * @return void
     */
    public function testShowCountFromOptionsArray($test_value, $message = '')
    {
        $options = self::optionsArraySetUp();
        $options['show_count'] = $test_value;

        $this->button = \Twitter\Widgets\Buttons\Follow::fromArray($options);
        $this->assertNotNull($this->button);
        $this->hideCountResult();
    }

    /**
     * Test hiding screen_name from an options array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @dataProvider \Twitter\Tests\CommonProviders::falseyProvider
     *
     * @param string|int|bool $test_value falsey value
     * @param string $message error message
     *
     * @return void
     */
    public function testHideScreenNameFromOptionsArray($test_value, $message = '')
    {
        $options = self::optionsArraySetUp();
        $options['show_screen_name'] = $test_value;

        $this->button = \Twitter\Widgets\Buttons\Follow::fromArray($options);
        $this->assertNotNull($this->button);
        $this->hideScreenNameResult();
    }

    /**
     * Test setting size from an options array
     *
     * @since 1.0.0
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

        $this->button = \Twitter\Widgets\Buttons\Follow::fromArray($options);
        $this->assertNotNull($this->button);
        $this->setSizeResult($size, $expected_valid, $message);
    }
}
