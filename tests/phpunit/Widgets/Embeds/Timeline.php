<?php
/*
The MIT License (MIT)

Copyright (c) 2016 Twitter Inc.

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
 * @coversDefaultClass \Twitter\Widgets\Embeds\Timeline
 */
final class Timeline extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Set up a widget object for each test
     *
     * @since 2.0.0
     *
     * @type \Twitter\Widgets\Embeds\Timeline
     */
    protected $widget;

    /**
     * Initialize a new Timeline\Timeline object for use in each test
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->widget = $this->getMockForAbstractClass('\Twitter\Widgets\Embeds\Timeline');
    }

    /**
     * Test validating a width
     *
     * @since 2.0.0
     *
     * @covers ::isValidWidth
     * @small
     *
     * @dataProvider widthProvider
     *
     * @param int|string $width width value to test
     * @param bool $is_valid validity expectation of the passed width
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testIsValidWidth($width, $is_valid, $message = '')
    {
        $class = $this->widget;
        if ($is_valid) {
            $this->assertTrue($class::isValidWidth($width), $message);
        } else {
            $this->assertFalse($class::isValidWidth($width), $message);
        }
    }

    /**
     * Test possible max widths for widget
     *
     * @since 2.0.0
     *
     * @return array width values with expected validity {
     *   @type array width, validity, message
     * }
     */
    public static function widthProvider()
    {
        return array(
            array( '400', false, 'Accepted a string as a valid width'     ),
            array( 400,   true,  'Rejected 400 int as a valid width'      ),
            array( 150,   false, 'Failed to reject a width below minimum' ),
            array( 1440,  false, 'Failed to reject a width above maximum' )
        );
    }

    /**
     * Test setting a width property
     *
     * @since 2.0.0
     *
     * @covers ::setLanguage
     * @small
     *
     * @dataProvider widthProvider
     *
     * @param int|string $width width value to test
     * @param bool $is_valid validity expectation of the passed width
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testSetWidth($width, $is_valid, $message = '')
    {
        $this->widget->setWidth($width);
        $property = self::getProperty($this->widget, 'width');

        if ($is_valid) {
            $this->assertEquals($width, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Test validating a height
     *
     * @since 2.0.0
     *
     * @covers ::isValidHeight
     * @small
     *
     * @dataProvider heightProvider
     *
     * @param int|string $height height value to test
     * @param bool $is_valid validity expectation of the passed height
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testIsValidHeight($height, $is_valid, $message = '')
    {
        $class = $this->widget;
        if ($is_valid) {
            $this->assertTrue($class::isValidHeight($height), $message);
        } else {
            $this->assertFalse($class::isValidHeight($height), $message);
        }
    }

    /**
     * Test setting a height property
     *
     * @since 2.0.0
     *
     * @covers ::setLanguage
     * @small
     *
     * @dataProvider heightProvider
     *
     * @param int|string $height height value to test
     * @param bool $is_valid validity expectation of the passed height
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testSetHeight($height, $is_valid, $message = '')
    {
        $this->widget->setHeight($height);
        $property = self::getProperty($this->widget, 'height');

        if ($is_valid) {
            $this->assertEquals($height, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Test possible max heights for widget
     *
     * @since 2.0.0
     *
     * @return array height values with expected validity {
     *   @type array height, validity, message
     * }
     */
    public static function heightProvider()
    {
        return array(
            array( '400', false, 'Accepted a string as a valid height'     ),
            array( 400,   true,  'Rejected 400 int as a valid height'      ),
            array( 150,   false, 'Failed to reject a height below minimum' )
        );
    }

    /**
     * Test validating a limit
     *
     * @since 2.0.0
     *
     * @covers ::isValidLimit
     * @small
     *
     * @dataProvider limitProvider
     *
     * @param int|string $limit limit value to test
     * @param bool $is_valid validity expectation of the passed limit
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testIsValidLimit($limit, $is_valid, $message = '')
    {
        $class = $this->widget;
        if ($is_valid) {
            $this->assertTrue($class::isValidLimit($limit), $message);
        } else {
            $this->assertFalse($class::isValidHeight($limit), $message);
        }
    }

    /**
     * Test setting a limit property
     *
     * @since 2.0.0
     *
     * @covers ::setLanguage
     * @small
     *
     * @dataProvider limitProvider
     *
     * @param int|string $limit limit value to test
     * @param bool $is_valid validity expectation of the passed limit
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testSetLimit($limit, $is_valid, $message = '')
    {
        $this->widget->setLimit($limit);
        $property = self::getProperty($this->widget, 'limit');

        if ($is_valid) {
            $this->assertEquals($limit, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Test possible limits for widget
     *
     * @since 2.0.0
     *
     * @return array limit values with expected validity {
     *   @type array limit, validity, message
     * }
     */
    public static function limitProvider()
    {
        return array(
            array( '3', false, 'Accepted a string as a valid limit'       ),
            array( 3,   true,  'Rejected 3 int as a valid limit'          ),
            array( 0,   false, 'Failed to reject a number below minimum'  ),
            array( 50,   false, 'Failed to reject a number above maximum' )
        );
    }

    /**
     * Test validating a chrome option
     *
     * @since 2.0.0
     *
     * @covers ::isValidChromeOption
     * @small
     *
     * @dataProvider chromeProvider
     *
     * @param int|string $option chrome value to test
     * @param bool $is_valid validity expectation of the passed chrome
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testIsValidChromeOption($option, $is_valid, $message)
    {
        $class = $this->widget;
        if ($is_valid) {
            $this->assertTrue($class::isValidChromeOption($option), $message);
        } else {
            $this->assertFalse($class::isValidChromeOption($option), $message);
        }
    }

    /**
     * Test setting a single chrome option by name
     *
     * @since 2.0.0
     *
     * @covers ::setChromeOption
     * @small
     *
     * @dataProvider chromeProvider
     *
     * @param int|string $option chrome value to test
     * @param bool $is_valid validity expectation of the passed chrome option
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testSetChromeOption($option, $is_valid, $message = '')
    {
        $chrome_method = self::getMethod($this->widget, 'setChromeOption');
        $result = $chrome_method->invokeArgs($this->widget, array($option));
        $property = self::getProperty($this->widget, 'chrome');

        if ($is_valid) {
            $this->assertTrue($result, $message);
            $this->assertArrayHasKey($option, $property, $message);
        } else {
            $this->assertFalse($result, $message);
            $this->assertEmpty($property, $message);
        }
    }

    /**
     * Test setting a chrome property with a positional array
     *
     * @since 2.0.0
     *
     * @covers ::setChrome
     * @small
     *
     * @dataProvider chromeProvider
     *
     * @param int|string $option chrome value to test
     * @param bool $is_valid validity expectation of the passed chrome option
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testSetChrome($option, $is_valid, $message = '')
    {
        $this->widget->setChrome(array($option));
        $property = self::getProperty($this->widget, 'chrome');

        if ($is_valid) {
            $this->assertArrayHasKey($option, $property, $message);
        } else {
            $this->assertEmpty($property, $message);
        }
    }

    /**
     * Test setting a chrome property with an associative array
     *
     * @since 2.0.0
     *
     * @covers ::setChrome
     * @small
     *
     * @dataProvider chromeProvider
     *
     * @param int|string $option chrome value to test
     * @param bool $is_valid validity expectation of the passed chrome option
     * @param string $message text to display if test fails
     *
     * @return void
     */
    public function testSetChromeAssociative($option, $is_valid, $message = '')
    {
        $this->widget->setChrome(array($option => true));
        $property = self::getProperty($this->widget, 'chrome');

        if ($is_valid) {
            $this->assertArrayHasKey($option, $property, $message);
        } else {
            $this->assertEmpty($property, $message);
        }
    }

    /**
     * Possible chrome settings values
     *
     * @since 2.0.0
     *
     * @return array chrome values with expected validity {
     *   @type array chrome, validity, message
     * }
     */
    public static function chromeProvider()
    {
        return array(
            array( 'noheader',    true,  'Rejected noheader chrome setting' ),
            array( 'nofooter',    true,  'Rejected nofooter chrome setting' ),
            array( 'noborders',   true,  'Rejected noborders chrome setting' ),
            array( 'noscrollbar', true,  'Rejected noscrollbar chrome setting' ),
            array( 'transparent', true,  'Rejected transparent chrome setting' ),
            array( 'none',        false, 'Accepted an invalid chrome setting' ),
            array( 42,            false, 'Accepted an int as a valid setting' )
        );
    }

    /**
     * Test setting a noheader chrome property via the hideHeader method
     *
     * @since 2.0.0
     *
     * @covers ::hideHeader
     * @small
     *
     * @return void
     */
    public function testHideHeader()
    {
        $this->widget->hideHeader();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertArrayHasKey('noheader', $property, 'Failed to set noheader chrome option via hideHeader');
    }

    /**
     * Test unsetting a noheader chrome property via the showHeader method
     *
     * @since 2.0.0
     *
     * @covers ::showHeader
     * @small
     *
     * @return void
     */
    public function testShowHeader()
    {
        self::setProperty($this->widget, 'chrome', array('noheader' => true));
        $this->widget->showHeader();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertEmpty($property, 'Failed to clear noheader chrome option via showHeader');
    }

    /**
     * Test setting a nofooter chrome property via the hideFooter method
     *
     * @since 2.0.0
     *
     * @covers ::hideFooter
     * @small
     *
     * @return void
     */
    public function testHideFooter()
    {
        $this->widget->hideFooter();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertArrayHasKey('nofooter', $property, 'Failed to set nofooter chrome option via hideFooter');
    }

    /**
     * Test unsetting a nofooter chrome property via the showFooter method
     *
     * @since 2.0.0
     *
     * @covers ::showFooter
     * @small
     *
     * @return void
     */
    public function testShowFooter()
    {
        self::setProperty($this->widget, 'chrome', array('nofooter' => true));
        $this->widget->showFooter();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertEmpty($property, 'Failed to clear nofooter chrome option via showFooter');
    }

    /**
     * Test setting a noborders chrome property via the hideBorders method
     *
     * @since 2.0.0
     *
     * @covers ::hideBorders
     * @small
     *
     * @return void
     */
    public function testHideBorders()
    {
        $this->widget->hideBorders();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertArrayHasKey('noborders', $property, 'Failed to set noborders chrome option via hideBorders');
    }

    /**
     * Test unsetting a noborders chrome property via the showBorders method
     *
     * @since 2.0.0
     *
     * @covers ::showBorders
     * @small
     *
     * @return void
     */
    public function testShowBorders()
    {
        self::setProperty($this->widget, 'chrome', array('noborders' => true));
        $this->widget->showBorders();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertEmpty($property, 'Failed to clear noheaders chrome option via showBorders');
    }

    /**
     * Test setting a noscrollbar chrome property via the hideScrollbar method
     *
     * @since 2.0.0
     *
     * @covers ::hideScrollbar
     * @small
     *
     * @return void
     */
    public function testHideScrollbar()
    {
        $this->widget->hideScrollbar();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertArrayHasKey('noscrollbar', $property, 'Failed to set noscrollbar chrome option via hideScrollbar');
    }

    /**
     * Test unsetting a noscrollbar chrome property via the showScrollbar method
     *
     * @since 2.0.0
     *
     * @covers ::showScrollbar
     * @small
     *
     * @return void
     */
    public function testShowScrollbar()
    {
        self::setProperty($this->widget, 'chrome', array('noscrollbar' => true));
        $this->widget->showScrollbar();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertEmpty($property, 'Failed to clear noscrollbar chrome option via showScrollbar');
    }

    /**
     * Test setting a transparent chrome property via the hideThemeBackground method
     *
     * @since 2.0.0
     *
     * @covers ::hideThemeBackground
     * @small
     *
     * @return void
     */
    public function testHideThemeBackground()
    {
        $this->widget->hideThemeBackground();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertArrayHasKey('transparent', $property, 'Failed to set transparent chrome option via hideThemeBackground');
    }

    /**
     * Test unsetting a transparent chrome property via the showThemeBackground method
     *
     * @since 2.0.0
     *
     * @covers ::showThemeBackground
     * @small
     *
     * @return void
     */
    public function testShowThemeBackground()
    {
        self::setProperty($this->widget, 'chrome', array('transparent' => true));
        $this->widget->showThemeBackground();
        $property = self::getProperty($this->widget, 'chrome');

        $this->assertEmpty($property, 'Failed to clear transparent chrome option via showThemeBackground');
    }

    /**
     * Test setting an ARIA live value
     *
     * @since 2.0.0
     *
     * @covers ::setAriaLive
     * @small
     *
     * @dataProvider ariaLiveProvider
     *
     * @return void
     */
    public function testSetAriaLive($politeness, $is_valid, $message = '')
    {
        $this->widget->setAriaLive($politeness);
        $property = self::getProperty($this->widget, 'aria_politeness');

        if ($is_valid) {
            $this->assertEquals($politeness, $property, $message);
        } else {
            $this->assertEquals('polite', $property, $message);
        }
    }

    /**
     * Test possible ARIA live region tokens
     *
     * @since 2.0.0
     *
     * @return array live values with expected validity {
     *   @type array live, validity, message
     * }
     */
    public function ariaLiveProvider()
    {
        return array(
            array( 'polite', true, 'ARIA live region value of polite not accepted' ),
            array( 'assertive', true, 'ARIA live region value of assertive not accepted' ),
            array( 'rude', false, 'Set an unsupported ARIA live region value' )
        );
    }

    /**
     * Test setting an assertive politeness value for ARIA live region
     *
     * @since 2.0.0
     *
     * @covers ::setAriaLiveAssertive
     * @small
     *
     * @return void
     */
    public function testSetAriaLiveAssertive()
    {
        $this->widget->setAriaLiveAssertive();
        $property = self::getProperty($this->widget, 'aria_politeness');

        $this->assertEquals('assertive', $property, 'failed to set assertive politeness on ARIA live region');
    }

    /**
     * Test setting class properties from an options array
     *
     * @since 2.0.0
     *
     * @covers ::setBaseOptions
     * @small
     *
     * @depends testSetWidth
     * @depends testSetHeight
     * @depends testSetLimit
     * @depends testSetChrome
     * @depends testSetAriaLive
     *
     * @return void
     */
    public function testSetBaseOptions()
    {
        $width = 400;
        $height = 400;
        $chrome = array('noheader' => true, 'nofooter' => true);
        $aria = 'assertive';

        $this->widget->setBaseOptions(array(
            'width' => $width,
            'height' => $height,
            'chrome' => $chrome,
            'aria-polite' => $aria
        ));

        $this->assertEquals($width, self::getProperty($this->widget, 'width'), 'Did not set width from options array');
        $this->assertEquals($height, self::getProperty($this->widget, 'height'), 'Did not set height from options array');
        $this->assertEquals($chrome, self::getProperty($this->widget, 'chrome'), 'Did not set chrome array from options array');
        $this->assertEquals($aria, self::getProperty($this->widget, 'aria_politeness'), 'Did not set ARIA politeness from options array');
    }

    /**
     * Test setting both a limit and a height
     *
     * @since 2.0.0
     *
     * @covers ::setBaseOptions
     * @small
     *
     * @depends testSetLimit
     * @depends testSetHeight
     *
     * @return void
     */
    public function testSetBaseOptionsLimitHeight()
    {
        $limit = 12;
        $height = 400;

        $this->widget->setBaseOptions(array(
            'height' => $height,
            'limit'  => $limit
        ));

        $this->assertEquals($limit, self::getProperty($this->widget, 'limit'), 'Did not set limit from options array');
        $this->assertNull(self::getProperty($this->widget, 'height'), 'Set height from options array when limit set');
    }

    /**
     * Test converting class properties into a filtered array
     *
     * @since 2.0.0
     *
     * @covers ::toArray
     * @small
     *
     * @return void
     */
    public function testToArray()
    {
        $width = 400;
        $height = 400;
        $chrome = array('noheader' => true, 'nofooter' => true);
        $aria = 'assertive';

        self::setProperty($this->widget, 'width', $width);
        self::setProperty($this->widget, 'height', $height);
        self::setProperty($this->widget, 'chrome', $chrome);
        self::setProperty($this->widget, 'aria_politeness', $aria);

        $options = $this->widget->toArray();

        $this->assertNotEmpty($options, 'No values returned from class array conversion');
        $this->assertArrayHasKey('width', $options, 'No width value returned in array');
        $this->assertEquals($width, self::getProperty($this->widget, 'width'));
        $this->assertArrayHasKey('height', $options, 'No height value returned in array');
        $this->assertEquals($height, self::getProperty($this->widget, 'height'));
        $this->assertArrayHasKey('chrome', $options, 'No chrome value returned in array');
        $this->assertEquals($chrome, self::getProperty($this->widget, 'chrome'));
        $this->assertArrayHasKey('aria-polite', $options, 'No aria-polite value returned in array');
        $this->assertEquals($aria, self::getProperty($this->widget, 'aria_politeness'));

        // test limit overriding height
        $limit = 12;
        self::setProperty($this->widget, 'limit', $limit);
        $options = $this->widget->toArray();
        $this->assertArrayNotHasKey('height', $options, 'Height value returned in array when limit set');
        $this->assertArrayHasKey('tweet-limit', $options, 'No limit value returned in array');
        $this->assertEquals($limit, self::getProperty($this->widget, 'limit'));
    }
}
