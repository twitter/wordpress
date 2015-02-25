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

namespace Twitter\Tests\Cards;

/**
 * @coversDefaultClass \Twitter\Cards\Gallery
 */
final class Gallery extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Generate an array of images to mock returned values of MultipleImages trait
     *
     * @since 1.0.0
     *
     * @return array {
     *   @type string image URL
     *   @type \Twitter\Cards\Components\Image mocked image object
     * }
     */
    public function mockImages()
    {
        $images = array();

        for ($i=0; $i<4; $i++) {
            $image_url = sprintf('https://example.com/i%u.jpg', $i);
            $image = $this->getMockBuilder('\Twitter\Cards\Components\Image')->disableOriginalConstructor()->setMethods(array('asCardProperties'))->getMock();
            $image->method('asCardProperties')->willReturn($image_url);

            $images[$image_url] = $image;
            unset($image_url);
            unset($image);
        }

        return $images;
    }

    /**
     * Test converting the card into an array
     *
     * @since 1.0.0
     *
     * @covers ::toArray()
     * @small
     *
     * @return void
     */
    public function testToArray()
    {
        // set up mocked images
        $images = $this->mockImages();
        $card = $this->getMockBuilder('\Twitter\Cards\Gallery')->setMethods(array('getImages'))->getMock();
        $card->method('getImages')->willReturn($images);

        // mock an account for stored creator
        $creator_screen_name = '@twitter';
        $creator = $this->getMockBuilder('\Twitter\Cards\Components\Account')->setMethods(array('asCardProperties'))->getMock();
        $creator->method('asCardProperties')->willReturn($creator_screen_name);
        self::setProperty($card, 'creator', $creator);
        unset($creator);

        // generate the array
        $properties = $card->toArray();
        $this->assertEquals($properties['card'], 'gallery', 'Gallery card type not set');
        $this->assertTrue((isset($properties['creator']) && $properties['creator'] === $creator_screen_name), 'Failed to output stored creator screen name');

        $image_count = count($images);
        $position = 0;
        foreach ($images as $image_url => $image) {
            $this->assertEquals($properties['image'.$position], $image_url, 'Gallery does not contain multiple image card data');
            $position++;
        }
    }
}
