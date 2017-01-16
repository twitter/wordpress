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
 * @coversDefaultClass \Twitter\Cards\Components\Image
 */
final class Image extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * src property value set with each new test
     *
     * @since 1.0.0
     *
     * @type string
     */
    const SRC = 'http://example.com/image.jpg';

    /**
     * Initialized image object
     *
     * @since 1.0.0
     *
     * @type \Twitter\Cards\Components\Image
     */
    protected $image;

    /**
     * Create a mocked trait for testing
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->image = new \Twitter\Cards\Components\Image(self::SRC);
    }

    /**
     * Test retrieving the src property set in the class constructor
     *
     * @since 1.0.0
     *
     * @covers ::getURL
     * @small
     *
     * @return void
     */
    public function testGetURL()
    {
        $this->assertEquals(self::SRC, $this->image->getURL(), 'Failed to retrieve image URL set in the class constructor');
    }

    /**
     * Test retrieving the width property
     *
     * @since 1.0.0
     *
     * @covers ::getWidth
     * @small
     *
     * @return void
     */
    public function testGetWidth()
    {
        $this->assertEquals(0, $this->image->getWidth(), 'Failed to return a width value of zero for an unset width');

        $width = 640;
        self::setProperty($this->image, 'width', $width);
        $this->assertEquals($width, $this->image->getWidth(), 'Failed to retrieve width');
    }

    /**
     * Test setting the width property
     *
     * @since 1.0.0
     *
     * @covers ::setWidth
     * @small
     *
     * @return void
     */
    public function testSetWidth()
    {
        $width = 640;
        $this->image->setWidth($width);

        $this->assertEquals($width, self::getProperty($this->image, 'width'), 'Failed to set width');
    }

    /**
     * Test retrieving the height property
     *
     * @since 1.0.0
     *
     * @covers ::getHeight
     * @small
     *
     * @return void
     */
    public function testGetHeight()
    {
        $this->assertEquals(0, $this->image->getHeight(), 'Failed to return a height value of zero for an unset height');

        $height = 480;
        self::setProperty($this->image, 'height', $height);
        $this->assertEquals($height, $this->image->getHeight(), 'Failed to retrieve height');
    }

    /**
     * Test setting the height property
     *
     * @since 1.0.0
     *
     * @covers ::setHeight
     * @small
     *
     * @return void
     */
    public function testSetHeight()
    {
        $height = 480;
        $this->image->setHeight($height);

        $this->assertEquals($height, self::getProperty($this->image, 'height'), 'Failed to set height');
    }

    /**
     * Test possible alternative text values
     *
     * @since 2.0.0
     *
     * @return array test values
     */
    public static function altTextProvider()
    {
        return array(
            array('A flower', true,  'Failed to accept valid alternative text' ),
            array('',         false, 'Failed to reject empty string'           ),
            array(' ',        false, 'Failed to reject whitespace only string' ),
            array(42,         false, 'Failed to reject int as alternative text')
        );
    }

    /**
     * Test setting alternative text
     *
     * @since 2.0.0
     *
     * @covers ::setAlternateText
     * @small
     *
     * @dataProvider altTextProvider
     *
     * @param string $alt      alternative text to set
     * @param bool   $is_valid expected validity
     * @param bool   $message  error message to display on failure
     *
     * @return void
     */
    public function testSetAlternativeText($alt, $is_valid, $message = '')
    {
        $this->image->setAlternativeText($alt);
        $property = self::getProperty($this->image, 'alt');

        if ($is_valid) {
            $this->assertEquals($alt, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Test an image without any set width or height
     *
     * @since 1.0.0
     *
     * @covers ::asCardProperties
     * @small
     *
     * @return void
     */
    public function testAsCardPropertiesSrc()
    {
        $this->assertEquals(self::SRC, $this->image->asCardProperties(), 'Failed to return a basic Twitter Card image property');

        self::setProperty($this->image, 'src', null);
        $this->assertEquals('', $this->image->asCardProperties(), 'Twitter Card image without set image URL should return empty string');
    }

    /**
     * Test an image with set width and height
     *
     * @since 1.0.0
     *
     * @covers ::asCardProperties
     * @small
     *
     * @return void
     */
    public function testAsCardPropertiesWithDimensions()
    {
        $width = 640;
        $height = 480;
        $alt = 'alt text';
        self::setProperty($this->image, 'width', $width);
        self::setProperty($this->image, 'height', $height);
        self::setProperty($this->image, 'alt', $alt);

        $this->assertEquals(
            array(
                'src' => self::SRC,
                'alt' => $alt,
            ),
            $this->image->asCardProperties(),
            'Image with src, width, height should return a card array with src only'
        );
    }
}
