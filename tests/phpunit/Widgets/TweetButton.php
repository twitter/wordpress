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

namespace Twitter\Tests\Widgets;

/**
 * @coversDefaultClass \Twitter\Widgets\TweetButton
 */
final class TweetButton extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Initialized TweetButton
     *
     * @since 1.0.0
     *
     * @type \Twitter\Widgets\TweetButton
     */
    protected $button;

    /**
     * Initialize a TweetButton object before each test
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->button = new \Twitter\Widgets\TweetButton();
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

    /**
     * Test setting a count display option
     *
     * @since 1.0.0
     *
     * @covers ::setCount
     * @small
     *
     * @dataProvider countProvider
     *
     * @param string $count count display configuration
     * @param bool $expected_valid expected validity
     * @param string $message error message to display on negative assertion
     *
     * @return void
     */
    public function testSetCount($count, $expected_valid, $message = '')
    {
        $this->button->setCount($count);
        $property = self::getProperty($this->button, 'count');

        $this->assertEquals(
            ( $expected_valid ? $count : '' ),
            $property,
            $message
        );
    }

    /**
     * Tweet count configurations
     *
     * @since 1.0.0
     *
     * @return array count display configurations {
     *   @type array type config, expected validity, error message
     * }
     */
    public static function countProvider()
    {
        return array(
            array( 'none', true, 'Failed to set a valid count of none' ),
            array( 'vertical', true, 'Failed to set a valid count of vertical' ),
            array( 'horizontal', false, 'Set an invalid count value' ),
        );
    }

    /**
     * Set the URL used to count Tweet mentions
     *
     * @since 1.0.0
     *
     * @covers ::setCountURL
     * @small
     *
     * @dataProvider countURLProvider
     *
     * @param string $count_url URL to be used for counting mentions
     * @param bool $expected_valid expected validity
     * @param string $message error message to display on negative assertion
     *
     * @return void
     */
    public function testSetCountURL($count_url, $expected_valid, $message = '')
    {
        $this->button->setCountURL($count_url);
        $property = self::getProperty($this->button, 'counturl');

        if ($expected_valid) {
            $this->assertEquals($count_url, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Count URL values
     *
     * @since 1.0.0
     *
     * @return array URLs {
     *   @type array URL, expected validity, error message
     * }
     */
    public static function countURLProvider()
    {
        return array(
            array( 'http://example.com/', true, 'Failed to set a HTTP URL' ),
            array( 'https://twitter.com/', true, 'Failed to set a HTTPS URL' ),
            array( '/foo/bar/', false, 'Allowed a relative URL' ),
        );
    }

    /**
     * Set alignment inside the button iframe
     *
     * @since 1.0.0
     *
     * @covers ::setAlign
     * @small
     *
     * @dataProvider alignProvider
     *
     * @param string $align align preference
     * @param bool $expected_valid expected validity
     * @param string $message error message to display on negative assertion
     *
     * @return void
     */
    public function testSetAlign($align, $expected_valid, $message = '')
    {
        $this->button->setAlign($align);
        $property = self::getProperty($this->button, 'align');

        if ($expected_valid) {
            $this->assertEquals($align, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Align values
     *
     * @since 1.0.0
     *
     * @return array align values {
     *   @type array align value, expected validity, error message
     * }
     */
    public static function alignProvider()
    {
        return array(
            array( 'left', true, 'Failed to set a left button alignment' ),
            array( 'right', true, 'Failed to set a right button alignment' ),
            array( 'center', false, 'Allowed an invalid align value' ),
        );
    }
}
