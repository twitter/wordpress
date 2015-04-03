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
 * @coversDefaultClass \Twitter\Cards\Components\MultipleImages
 */
final class MultipleImages extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Mocked MultipleImages trait
     *
     * @since 1.0.0
     *
     * @type PHPUnit_Framework_MockObject_MockObject
     */
    protected $multipleimages;

    /**
     * Create a mocked trait for testing
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->multipleimages = $this->getMockForTrait('\Twitter\Cards\Components\MultipleImages');
    }

    /**
     * Test retrieving images property
     *
     * @since 1.0.0
     *
     * @covers ::getImages
     * @small
     *
     * @return void
     */
    public function testGetImages()
    {
        $images = $this->multipleimages->getImages();
        $this->assertTrue(( is_array($images) && empty($images) ), 'Failed to retrieve images property');
    }

    /**
     * Add an image from an image URL
     *
     * @since 1.0.0
     *
     * @covers ::addImage
     * @small
     *
     * @return string test URL
     */
    public function testAddImageFromURLString()
    {
        $url = 'https://example.com/i.jpg';
        $this->multipleimages->addImage($url);

        $this->assertNotEmpty(self::getProperty($this->multipleimages, 'images'), 'Failed to add an image from a URL string');
        $this->assertEquals(1, self::getProperty($this->multipleimages, 'image_count'), 'Failed to update image count');

        return $url;
    }

    /**
     * Test adding a single image from a URL string with passed width and height data
     *
     * @since 1.0.0
     *
     * @covers ::addImage
     * @depends testAddImageFromURLString
     * @small
     *
     * @return void
     */
    public function testSetImageFromURLStringWithDimensions()
    {
        $url = 'https://example.com/i.jpg';
        $width = 640;
        $height = 480;
        $this->multipleimages->addImage($url, $width, $height);

        $this->assertEquals(1, self::getProperty($this->multipleimages, 'image_count'), 'image count does not match expected array size');

        $images = self::getProperty($this->multipleimages, 'images');
        $image = reset($images);
        $this->assertNotNull($image, 'Failed to set image');
        $this->assertEquals($url, self::getProperty($image, 'src'), 'Failed to set URL string');
        $this->assertEquals($width, self::getProperty($image, 'width', 'Failed to set valid width'));
        $this->assertEquals($height, self::getProperty($image, 'height', 'Failed to set valid height'));
    }

    /**
     * Add a single image from an image object
     *
     * @since 1.0.0
     *
     * @covers ::addImage
     * @small
     *
     * @return void
     */
    public function testAddImageFromImageObject()
    {
        $image_url = 'https://example.com/i.jpg';
        $this->multipleimages->addImage(new \Twitter\Cards\Components\Image($image_url));
        $this->assertEquals(1, self::getProperty($this->multipleimages, 'image_count'), 'image count does not match expected array size');
        $images = self::getProperty($this->multipleimages, 'images');
        $image = reset($images);
        unset( $images );
        $src = self::getProperty($image, 'src');
        $this->assertEquals(
            $image_url,
            $src,
            'Failed to add a single image from an image object'
        );
    }

    /**
     * Test setting a single image from a URL object with width and height data
     *
     * @since 1.0.0
     *
     * @covers ::addImage
     * @depends testAddImageFromImageObject
     * @small
     *
     * @return void
     */
    public function testAddImageFromImageObjectWithDimensions()
    {
        $url = 'https://example.com/i.jpg';
        $width = 640;
        $height = 480;
        $this->multipleimages->addImage(
            ( new \Twitter\Cards\Components\Image($url) )
                ->setWidth($width)
                ->setHeight($height)
        );

        $this->assertEquals(1, self::getProperty($this->multipleimages, 'image_count'), 'image count does not match expected array size');

        $images = self::getProperty($this->multipleimages, 'images');
        $image = reset($images);
        unset( $images );
        $this->assertNotNull($image, 'Failed to set image');

        $src_property = self::getProperty($image, 'src');
        $this->assertEquals($url, $src_property, 'Failed to set URL string');
        unset( $src );

        $width_property = self::getProperty($image, 'width');
        $this->assertEquals($width, $width_property, 'Failed to set valid width');
        unset( $width_property );

        $height_property = self::getProperty($image, 'height');
        $this->assertEquals($height, $height_property, 'Failed to set valid height');
    }

    /**
     * Test adding the same image URL twice
     *
     * @since 1.0.0
     *
     * @covers ::addImage
     * @small
     *
     * @return void
     */
    public function testAddSameImageMultipleTimes()
    {
        $url = 'https://example.com/i.jpg';
        $image = new \Twitter\Cards\Components\Image($url);
        $this->multipleimages->addImage($url);
        $this->multipleimages->addImage($image);
        $this->multipleimages->addImage($url);
        $this->multipleimages->addImage($image);

        $this->assertCount(1, self::getProperty($this->multipleimages, 'images'), 'only one image should be stored per image URL');
        $this->assertEquals(1, self::getProperty($this->multipleimages, 'image_count'), 'image count does not match expected array size');
    }
}
