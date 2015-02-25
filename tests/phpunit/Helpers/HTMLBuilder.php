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

namespace Twitter\Tests\Helpers;

/**
 * @coversDefaultClass \Twitter\Helpers\HTMLBuilder
 */
final class HTMLBuilder extends \PHPUnit_Framework_TestCase
{
    /**
     * Test proper escaping of a class name before it is inserted as a class attribute value
     *
     * @since 1.0.0
     *
     * @covers ::escapeClassName
     * @small
     *
     * @dataProvider escapeableHTMLAttributeProvider
     *
     * @param string $test_string string to test
     * @param string $expected_result expected escaped string
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testEscapeClassName($test_string, $expected_result, $message = '')
    {
        $this->assertEquals(
            $expected_result,
            \Twitter\Helpers\HTMLBuilder::escapeClassName($test_string),
            $message
        );
    }

    /**
     * Test proper escaping of a HTML attribute
     *
     * @since 1.0.0
     *
     * @covers ::escapeAttributeValue
     * @small
     *
     * @dataProvider escapeableHTMLAttributeProvider
     *
     * @param string $test_string string to test
     * @param string $expected_result expected escaped string
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testEscapeAttributeValue($test_string, $expected_result, $message = '')
    {
        $this->assertEquals(
            $expected_result,
            \Twitter\Helpers\HTMLBuilder::escapeAttributeValue($test_string),
            $message
        );
    }

    /**
     * Define multiple strings to be evalauted as HTML attributes
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test string, expected result, and error string
     * }
     */
    public static function escapeableHTMLAttributeProvider()
    {
        return array(
            array( 'twitter-share-button', 'twitter-share-button', 'failed to allow valid class name' ),
            array( 'class onload="bad thing"', 'class onload=&quot;bad thing&quot;', 'failed to escape double-quoted attribute value' )
        );
    }

    /**
     * Test escaping a passed string expected to only contain an element's inner text
     *
     * @since 1.0.0
     *
     * @covers ::escapeInnerText
     * @small
     *
     * @dataProvider escapableHTMLInnerTextProvider
     *
     * @param string $test_string string to test
     * @param string $expected_result expected escaped string
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testEscapeInnerText($test_string, $expected_result, $message = '')
    {
        $this->assertEquals(
            $expected_result,
            \Twitter\Helpers\HTMLBuilder::escapeInnerText($test_string),
            $message
        );
    }

    /**
     * Define multiple strings to be evaluated as HTML inner text
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test string, expected result, and error string
     * }
     */
    public static function escapableHTMLInnerTextProvider()
    {
        return array(
            array( 'fox <script>alert(\'jumps\')</script> over the dog', 'fox &lt;script&gt;alert(\'jumps\')&lt;/script&gt; over the dog', 'HTML element inside string not properly escaped' )
        );
    }

    /**
     *
     * @since 1.0.0
     *
     * @covers ::escapeURL
     * @small
     *
     * @dataProvider escapableURLProvider
     *
     * @param string $test_url passed URL to test
     * @param string $expected_result expected escaped string
     * @param string $message error message to display on fail
     *
     * @return void
     */
    public function testEscapeURL($test_url, $expected_result, $message = '')
    {
        $this->assertEquals(
            $expected_result,
            \Twitter\Helpers\HTMLBuilder::escapeURL($test_url),
            $message
        );
    }

    /**
     * Define multiple passed URL values to be escaped
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type array test string, expected result, and error string
     * }
     */
    public static function escapableURLProvider()
    {
        return array(
            array( 'https://twitter.com/" onload="alert(\'hello\')"', 'https://twitter.com/&quot; onload=&quot;alert(&#039;hello&#039;)&quot;', 'failed to escape potentially harmful markup around URL' )
        );
    }
}
