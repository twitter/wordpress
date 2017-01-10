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

namespace Twitter\Tests\Widgets\Embeds;

/**
 * @coversDefaultClass \Twitter\Widgets\Embeds\Theme
 */
final class Theme extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Set up a widget object for each test
     *
     * @since 2.0.0
     *
     * @type \Twitter\Widgets\Embeds\Timelines\BaseTimeline
     */
    protected $widget;

    /**
     * Initialize a new Theme object for use in each test
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->widget = $this->getMockForTrait('\Twitter\Widgets\Embeds\Theme');
    }

    /**
     * Test possible theme values
     *
     * @since 2.0.0
     *
     * @return array theme values with expected validity {
     *   @type array theme, validity, message
     * }
     */
    public static function themeProvider()
    {
        return array(
            array( 'light',     true,  'Failed to accept light as a valid theme value' ),
            array( 'dark',      true,  'Failed to accept dark as a valid theme value'  ),
            array( 'superdark', false, 'Accepted an invalid theme value'               )
        );
    }

    /**
     * Test validating a theme value
     *
     * @since 2.0.0
     *
     * @covers ::isValidTheme
     * @small
     *
     * @dataProvider themeProvider
     *
     * @param int|string $theme theme value to test
     * @param bool $is_valid validity expectation of the passed theme
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testIsValidTheme($theme, $is_valid, $message = '')
    {
        $class = $this->widget;
        if ($is_valid) {
            $this->assertTrue($class::isValidTheme($theme), $message);
        } else {
            $this->assertFalse($class::isValidTheme($theme), $message);
        }
    }

    /**
     * Test setting a theme property
     *
     * @since 2.0.0
     *
     * @covers ::setTheme
     * @small
     *
     * @dataProvider themeProvider
     *
     * @param string $theme theme value to test
     * @param bool $is_valid validity expectation of the passed theme value
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testSetTheme($theme, $is_valid, $message = '')
    {
        $this->widget->setTheme($theme);
        $property = self::getProperty($this->widget, 'theme');

        if ($is_valid) {
            $this->assertEquals($theme, $property, $message);
        } else {
            $class = $this->widget;
            $this->assertEquals($class::$THEME_LIGHT, $property, $message);
        }
    }

    /**
     * Test setting a light theme
     *
     * @since 2.0.0
     *
     * @covers ::setThemeLight
     * @small
     *
     * @return void
     */
    public function testSetThemeLight()
    {
        self::setProperty($this->widget, 'theme', 'foo');
        $this->widget->setThemeLight();
        $property = self::getProperty($this->widget, 'theme');

        $this->assertEquals('light', $property);
    }

    /**
     * Test setting a light theme
     *
     * @since 2.0.0
     *
     * @covers ::setThemeDark
     * @small
     *
     * @return void
     */
    public function testSetThemeDark()
    {
        $this->widget->setThemeDark();
        $property = self::getProperty($this->widget, 'theme');

        $this->assertEquals('dark', $property);
    }

    /**
     * Test validating a hexadecimal color
     *
     * @since 2.0.0
     *
     * @covers ::isValidHexadecimalColor
     * @small
     *
     * @dataProvider hexadecimalColorProvider
     *
     * @param string $color    color value to test
     * @param bool   $is_valid validity expectation of the passed theme value
     * @param string $message  text to display if test fails
     *
     * @return void
     */
    public function testIsValidHexadecimalColor($color, $is_valid, $message = '')
    {
        $class = $this->widget;
        if ($is_valid) {
            $this->assertTrue($class::isValidHexadecimalColor($color), $message);
        } else {
            $this->assertFalse($class::isValidHexadecimalColor($color), $message);
        }
    }

    /**
     * Test possible color values
     *
     * @since 2.0.0
     *
     * @return array color values with expected validity {
     *   @type array color, validity, message
     * }
     */
    public static function hexadecimalColorProvider()
    {
        return array(
            array( '1DA1F2',  true,  'Rejected Twitter blue uppercase as a valid hex value' ),
            array( '1da1f2',  true,  'Rejected Twitter blue lowercase as a valid hex value' ),
            array( '1NO1P2',  false, 'Accepted alpha characters out of range'               ),
            array( '#1DA1F2', false, 'Accepted leading hash'                                ),
            array( 'FFF',     false, 'Failed to reject a three-character color'             )
        );
    }

    /**
     * Test possible dirty color values and their cleaned versions
     *
     * @since 2.0.0
     *
     * @return array color values with expected cleaned version {
     *   @type array dirty color, cleaned color, message
     * }
     */
    public static function dirtyHexadecimalProvider()
    {
        return array(
            array( '1DA1F2',   '1DA1F2', 'Did not preserve a valid hex color'                                      ),
            array( 42,         '',       'Did not clean an int'                                                    ),
            array( ' 1DA1F2 ', '1DA1F2', 'Did not trim leading and trailing spaces'                                ),
            array( '#1DA1F2',  '1DA1F2', 'Did not remove leading hash'                                             ),
            array( '1da1f2',   '1DA1F2', 'Did not normalize to uppercase letters'                                  ),
            array( 'FFF',      'FFFFFF', 'Did not expand three character abbreviation to six character equivalent' )
        );
    }

    /**
     * Test cleaning a hexadecimal color
     *
     * @covers ::cleanHexadecimalColor
     * @small
     *
     * @dataProvider dirtyHexadecimalProvider
     *
     * @param int|string $dirty          possibly dirty hex color value in need of cleaning
     * @param string     $expected_clean expected cleaned value
     * @param string     $message error  message to display on fail
     */
    public function testCleanHexadecimalColor($dirty, $expected_clean, $message = '')
    {
        $method = self::getMethod('\Twitter\Widgets\Embeds\Theme', 'cleanHexadecimalColor');
        $clean = $method->invokeArgs(null, array($dirty));
        $this->assertEquals($expected_clean, $clean, $message);
    }

    /**
     * Test setting a link color
     *
     * @since 2.0.0
     *
     * @depends testCleanHexadecimalColor
     *
     * @covers ::setLinkColor
     * @small
     *
     * @return void
     */
    public function testSetLinkColor()
    {
        $color = '1DA1F2';
        $this->widget->setLinkColor($color);

        $this->assertEquals($color, self::getProperty($this->widget, 'link_color'), 'Failed to set link color');
    }

    /**
     * Test setting a border color
     *
     * @since 2.0.0
     *
     * @depends testCleanHexadecimalColor
     *
     * @covers ::setBorderColor
     * @small
     *
     * @return void
     */
    public function testSetBorderColor()
    {
        $color = '1DA1F2';
        $this->widget->setBorderColor($color);

        $this->assertEquals($color, self::getProperty($this->widget, 'border_color'), 'Failed to set border color');
    }

    /**
     * Test setting properties from an array
     *
     * @since 2.0.0
     *
     * @depends testSetThemeDark
     * @depends testSetLinkColor
     * @depends testSetBorderColor
     *
     * @covers ::setThemeOptions
     * @small
     *
     * @return void
     */
    public function testSetThemeOptions()
    {
        $options = array(
            'theme' => 'dark',
            'link-color' => '1DA1F2',
            'border-color' => '1DA1F2'
        );
        $this->widget->setThemeOptions($options);

        $this->assertEquals($options['theme'], self::getProperty($this->widget, 'theme'), 'Failed to set dark theme from an options array');
        $this->assertEquals($options['link-color'], self::getProperty($this->widget, 'link_color'), 'Failed to set a link color from an options array');
        $this->assertEquals($options['border-color'], self::getProperty($this->widget, 'border_color'), 'Failed to set a border color from an options array');
    }

    /**
     * Test converting properties to their array equivalent
     *
     * @since 2.0.0
     *
     * @covers ::themeToArray
     * @small
     *
     * @return void
     */
    public function testThemeToArray()
    {
        $theme = 'dark';
        $color = '1DA1F2';

        self::setProperty($this->widget, 'theme', $theme);
        self::setProperty($this->widget, 'link_color', $color);
        self::setProperty($this->widget, 'border_color', $color);

        $options = $this->widget->themeToArray();
        $this->assertArrayHasKey('theme', $options, 'Theme information not returned from array conversion');
        $this->assertEquals($theme, $options['theme'], 'Theme information not properly retrieved during array conversion');
        $this->assertArrayHasKey('link-color', $options, 'Link color information not returned from array conversion');
        $this->assertEquals('#' . $color, $options['link-color'], 'Link color not properly retrieved during array conversion');
        $this->assertArrayHasKey('border-color', $options, 'Border color information not returned from array conversion');
        $this->assertEquals('#' . $color, $options['border-color'], 'Border color information not returned from array conversion');
    }

    /**
     * Test converting properties to oEmbed API query parameter values
     *
     * @since 2.0.0
     *
     * @covers ::themeToOEmbedParameterArray
     * @small
     *
     * @return void
     */
    /*public function testThemeToOEmbedParameterArray()
	{
		$theme = 'dark';
		$color = '1DA1F2';

		self::setProperty($this->widget, 'theme', $theme);
		self::setProperty($this->widget, 'link_color', $color);
		self::setProperty($this->widget, 'border_color', $color);

		$oembed_parameters = $this->widget->themeToOEmbedParameterArray();
		$this->assertArrayHasKey('theme', $oembed_parameters, 'Theme information not returned from oEmbed array conversion');
		$this->assertEquals($theme, $oembed_parameters['theme'], 'Theme information not properly retrieved during oEmbed array conversion');
		$this->assertArrayHasKey('link_color', $oembed_parameters, 'Link color information not returned from oEmbed array conversion');
		$this->assertEquals('#' . $color, $oembed_parameters['link_color'], 'Link color not properly retrieved during oEmbed array conversion');
		$this->assertArrayHasKey('border_color', $oembed_parameters, 'Border color information not returned from oEmbed array conversion');
		$this->assertEquals('#' . $color, $oembed_parameters['border_color'], 'Border color information not returned from oEmbed array conversion');
	}*/
}
