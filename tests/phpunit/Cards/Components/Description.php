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

namespace Twitter\Tests\Cards\Components;

/**
 * @coversDefaultClass \Twitter\Cards\Components\Description
 */
final class Description extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Mocked Description trait
     *
     * @since 1.0.0
     *
     * @type PHPUnit_Framework_MockObject_MockObject
     */
    protected $description;

    /**
     * Create a mocked trait for testing
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->description = $this->getMockForTrait('\Twitter\Cards\Components\Description');
    }

    /**
     * Test sanitizing a passed Twitter Card description
     *
     * @since 1.0.0
     *
     * @covers ::sanitizeDescription
     * @small
     *
     * @dataProvider descriptionProvider
     *
     * @return string sanitized description
     */
    public function testSanitizeDescription($test_value, $expected_result, $message = '')
    {
        $sanitized_description = $this->description->sanitizeDescription($test_value);
        $this->assertEquals($expected_result, $sanitized_description, $message);

        return $sanitized_description;
    }

    /**
     * Provide description test values
     *
     * @since 1.0.0
     *
     * @return array descriptions {
     *   @type array test value, expected result, error message
     * }
     */
    public static function descriptionProvider()
    {
        $test_value = 'hello world';
        return array(
            array( $test_value, $test_value, 'failed to handle valid description' ),
            array( ' ' . $test_value . ' ', $test_value, 'failed to trim a description' ),
        );
    }

    /**
     * Test setting the description property
     *
     * @since 1.0.0
     *
     * @covers ::setDescription
     * @depends testSanitizeDescription
     * @small
     *
     * @return void
     */
    public function testSetDescription()
    {
        $description = 'The quick brown fox jumps over the lazy dog';
        $this->description->setDescription($description);
        $this->assertEquals($description, self::getProperty($this->description, 'description'), 'Failed to set description');
    }
}
