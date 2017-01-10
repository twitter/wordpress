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

namespace Twitter\Tests\WordPress\Shortcodes\Buttons;

/**
 * @group shortcode
 * @coversDefaultClass \Twitter\WordPress\Shortcodes\Buttons\Follow
 */
final class Follow extends \WP_UnitTestCase
{

    /**
     * Test extracting a Twitter screen name from a shortcode through screen_name attribute
     *
     * @since 2.0.0
     *
     * @covers ::getShortcodeAttributes
     * @small
     *
     * @dataProvider screenNameProvider
     *
     * @param string $screen_name Twitter screen name test value
     *
     * @return void
     */
    public function testGetShortcodeAttributesScreenName($screen_name)
    {
        $expected = 'TwitterDev';

        $options = \Twitter\WordPress\Shortcodes\Buttons\Follow::getShortcodeAttributes(array( 'screen_name' => $screen_name ));
        $this->assertTrue(( isset($options['screen_name']) && $options['screen_name'] === $expected ), 'Failed to extract screen name from attribute');
    }

    /**
     * Twitter screen name provider
     *
     * @since 2.0.0
     *
     * @return array array of arrays of Twitter screen names
     */
    public static function screenNameProvider()
    {
        return array(
            array( 'TwitterDev' ),
            array( '@TwitterDev' ),
            array( ' TwitterDev ' ),
        );
    }

    /**
     * Test hiding Tweet counts through a shortcode attribute value
     *
     * @since 2.0.0
     *
     * @covers ::getShortcodeAttributes
     * @small
     *
     * @dataProvider falseyShortcodeParameterProvider
     *
     * @param bool|int|string $truthy_value truthy value to test
     *
     * @return void
     */
    public function testGetShortcodeAttributesShowCount($truthy_value)
    {
        $options = \Twitter\WordPress\Shortcodes\Buttons\Follow::getShortcodeAttributes(array( 'show_count' => $truthy_value ));
        $this->assertTrue(( isset($options['show_count']) && false === $options['show_count'] ), 'Failed to enable follow count from attribute');
    }

    /**
     * Test hiding Twitter screen name through a shortcode attribute value
     *
     * @since 2.0.0
     *
     * @covers ::getShortcodeAttributes
     * @small
     *
     * @dataProvider falseyShortcodeParameterProvider
     *
     * @param bool|int|string $falsey_value falsey value to test
     *
     * @return void
     */
    public function testGetShortcodeAttributes($falsey_value)
    {
        $options = \Twitter\WordPress\Shortcodes\Buttons\Follow::getShortcodeAttributes(array( 'show_screen_name' => $falsey_value ));
        $this->assertTrue(( isset($options['show_screen_name']) && false === $options['show_screen_name'] ), 'Failed to disable screen name display from attribute');
    }

    /**
     * Show count falsey value provider
     *
     * @since 2.0.0
     *
     * @return array array of shortcode falsey values
     */
    public static function falseyShortcodeParameterProvider()
    {
        return array(
            array( false ),
            array( 0 ),
            array( '0' ),
            array( 'false' ),
            array( 'FALSE' ),
            array( 'no' ),
            array( 'NO' ),
            array( 'off' ),
            array( 'OFF' ),
        );
    }
}
