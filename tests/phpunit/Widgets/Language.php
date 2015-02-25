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
 * @coversDefaultClass \Twitter\Widgets\Language
 */
final class Language extends \PHPUnit_Framework_TestCase
{

    /**
     * Check language validity checker
     *
     * @since 1.0.0
     *
     * @covers ::isSupportedLanguage
     * @small
     *
     * @dataProvider languagesProvider
     *
     * @param string $language_code language code to test
     * @param bool $is_valid validity expectation of the passed language
     * @param string $message error message
     *
     * @return void
     */
    public function testIsSupportedLanguage($language_code, $is_valid, $message = '')
    {
        if ($is_valid) {
            $this->assertTrue(\Twitter\Widgets\Language::isSupportedLanguage($language_code), $message);
        } else {
            $this->assertFalse(\Twitter\Widgets\Language::isSupportedLanguage($language_code), $message);
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
}
