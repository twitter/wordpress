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
 * @coversDefaultClass \Twitter\Cards\Components\SingleImage
 */
final class SingleImage extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Mocked SingleImage trait
     *
     * @since 1.0.0
     *
     * @type PHPUnit_Framework_MockObject_MockObject
     */
    protected $singleimage;

    /**
     * Create a mocked trait for testing
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->singleimage = $this->getMockForTrait('\Twitter\Cards\Components\SingleImage');
    }

    /**
     * Set a single image from an image URL
     *
     * @since 1.0.0
     *
     * @covers ::setImage
     * @small
     *
     * @return string test URL
     */
    public function testSetImageFromURLString()
    {
        $url = 'https://example.com/i.jpg';
        $this->singleimage->setImage($url);

        $this->assertNotNull(self::getProperty($this->singleimage, 'image'), 'Failed to set an image from a URL string');

        return $url;
    }

    /**
     * Test setting a single image from a URL string with passed width and height data
     *
     * @since 1.0.0
     *
     * @covers ::setImage
     * @depends testSetImageFromURLString
     * @small
     *
     * @return void
     */
    public function testSetImageFromURLStringWithDimensions()
    {
        $url = 'https://example.com/i.jpg';
        $width = 640;
        $height = 480;
        $this->singleimage->setImage($url, $width, $height);

        $image = self::getProperty($this->singleimage, 'image');
        $this->assertEquals($url, self::getProperty($image, 'src'), 'Failed to set URL string');
        $this->assertEquals($width, self::getProperty($image, 'width', 'Failed to set valid width'));
        $this->assertEquals($height, self::getProperty($image, 'height', 'Failed to set valid height'));
    }

    /**
     * Set a single image from an image object
     *
     * @since 1.0.0
     *
     * @covers ::setImage
     * @small
     *
     * @return void
     */
    public function testSetImageFromImageObject()
    {
        $image_url = 'https://example.com/i.jpg';
        $this->singleimage->setImage(new \Twitter\Cards\Components\Image($image_url));
        $this->assertEquals(
            $image_url,
            self::getProperty(self::getProperty($this->singleimage, 'image'), 'src'),
            'Failed to set a single image from an image object'
        );
    }

    /**
     * Test setting a single image from a URL object with width and height data
     *
     * @since 1.0.0
     *
     * @covers ::setImage
     * @depends testSetImageFromImageObject
     * @small
     *
     * @return void
     */
    public function testSetImageFromImageObjectWithDimensions()
    {
        $url = 'https://example.com/i.jpg';
        $width = 640;
        $height = 480;
        $this->singleimage->setImage(
            ( new \Twitter\Cards\Components\Image($url) )
                ->setWidth($width)
                ->setHeight($height)
        );

        $image = self::getProperty($this->singleimage, 'image');
        $this->assertEquals($url, self::getProperty($image, 'src'), 'Failed to set URL string');
        $this->assertEquals($width, self::getProperty($image, 'width', 'Failed to set valid width'));
        $this->assertEquals($height, self::getProperty($image, 'height', 'Failed to set valid height'));
    }

    /**
     * Test requesting card properties when no image is set/
     *
     * @since 1.0.0
     *
     * @covers imageCardProperties
     */
    public function testEmptyCardProperties()
    {
        $image = $this->getMockBuilder('\Twitter\Cards\Components\Image')->disableOriginalConstructor()->getMock();
        $image->method('asCardProperties')->willReturn('');
        self::setProperty($this->singleimage, 'image', $image);

        $properties_method = self::getMethod($this->singleimage, 'imageCardProperties');
        $this->assertNotNull($properties_method);

        $image_properties = $properties_method->invoke($this->singleimage);
        $this->assertEmpty($image_properties);
    }
}
