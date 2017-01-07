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
 * @coversDefaultClass \Twitter\Widgets\Base
 */
final class Base extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Set up a widget object for each test
     *
     * @since 1.0.0
     *
     * @type \Twitter\Widgets\Base
     */
    protected $widget;

    /**
     * Initialize a new Widgets\Base object for use in each test
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->widget = $this->getMockForAbstractClass('\Twitter\Widgets\Base');
    }

    /**
     * Test setting DNT and outputting the result
     *
     * @since 1.0.0
     *
     * @covers ::doNotTrack
     * @small
     *
     * @return void
     */
    public function testDoNotTrack()
    {
        $this->widget->doNotTrack();

        $this->assertTrue(self::getProperty($this->widget, 'dnt'), 'Failed to set DNT');
    }

    /**
     * Test resetting DNT and no outputted attribute
     *
     * @since 1.0.0
     *
     * @covers ::allowTracking
     * @small
     *
     * @return void
     */
    public function testAllowTracking()
    {
        $this->widget->doNotTrack();
        $this->widget->allowTracking();

        $this->assertFalse(self::getProperty($this->widget, 'dnt'), 'Failed to reset DNT');
    }

    /**
     * Test setting a Twitter language code
     *
     * @since 1.0.0
     *
     * @covers ::setLanguage
     * @small
     *
     * @dataProvider languagesProvider
     *
     * @param string $language_code language code to test
     * @param bool $is_valid validity expectation of the passed language
     *
     * @return void
     */
    public function testSetLanguage($language_code, $is_valid)
    {
        $this->widget->setLanguage($language_code);
        $this->languageExists($language_code, $is_valid);
    }

    /**
     * Check if a language exists after expected setter
     *
     * @since 1.0.0
     *
     * @param string $language_code language code to test
     * @param bool $is_valid validity expectation of the passed language
     *
     * @return void
     */
    protected function languageExists($language_code, $is_valid, $message = '')
    {
        $property = self::getProperty($this->widget, 'lang');

        if ($is_valid) {
            $this->assertEquals($language_code, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Test language codes
     *
     * @since 1.0.0
     *
     * @return array language codes with expected validity {
     *   @type array language code, validity
     * }
     */
    public static function languagesProvider()
    {
        return array(
            array( 'es', true, 'Did not accept Spanish as a valid language' ),
            array( 'eo', false, 'Accepted Esperanto as a valid language' ),
            array( ''  , false, 'Failed to properly handle empty string' )
        );
    }

    /**
     * Test setting DNT from an options array
     *
     * @since 1.0.0
     *
     * @covers ::setBaseOptions
     * @small
     *
     * @dataProvider \Twitter\Tests\CommonProviders::truthyProvider
     *
     * @param string|int|bool $test_value truthy value
     * @param string $message error message
     *
     * @return void
     */
    public function testSetDNTOptionsArray($test_value, $message = '')
    {
        $key = 'dnt';
        $this->widget->setBaseOptions(array( $key => $test_value ));

        $this->assertTrue(self::getProperty($this->widget, 'dnt'), $message);
    }

    /**
     * Test setting a Twitter language code through an options array setter
     *
     * @since 1.0.0
     *
     * @covers ::setBaseOptions
     * @small
     *
     * @dataProvider languagesProvider
     *
     * @param string $language_code language code to test
     * @param bool $is_valid validity expectation of the passed language
     *
     * @return void
     */
    public function testSetLanguageThroughPassedArray($language_code, $is_valid)
    {
        $this->widget->setBaseOptions(array( 'lang' => $language_code ));
        $this->languageExists($language_code, $is_valid);
    }
}
