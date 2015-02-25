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
 * @coversDefaultClass \Twitter\Cards\Product
 */
final class Product extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Test adding details to a product card
     *
     * @since 1.0.0
     *
     * @covers ::addDetail
     * @small
     *
     * @return void
     */
    public function testAddDetail()
    {
        $card = new \Twitter\Cards\Product();

        // set a detail
        $card->addDetail('size', 'large');
        $this->assertEquals(array('size'=>'large'), self::getProperty($card, 'details'), 'Failed to set size detail');

        // overrite the same detail
        $card->addDetail('size', 'small');
        $this->assertEquals(array('size'=>'large'), self::getProperty($card, 'details'), 'Failed to overwrite size detail');
        unset($details);

        $card->addDetail('color', 'blue');
        $this->assertCount(2, self::getProperty($card, 'details'));

        // try adding a detail beyond the detail limit
        $card->addDetail('edition', 'special');
        $this->assertCount(2, self::getProperty($card, 'details'));
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
        // store a simple image
        $image_url = 'https://example.com/i.jpg';
        $card = $this->getMockBuilder('\Twitter\Cards\Product')->setMethods(array('imageCardProperties'))->getMock();
        $card->method('imageCardProperties')->willReturn($image_url);

        // mock an account for stored creator
        $creator_screen_name = '@twitter';
        $creator = $this->getMockBuilder('\Twitter\Cards\Components\Account')->setMethods(array('asCardProperties'))->getMock();
        $creator->method('asCardProperties')->willReturn($creator_screen_name);
        self::setProperty($card, 'creator', $creator);
        unset($creator);

        $description = 'Hello world';
        self::setProperty($card, 'description', $description);

        // details
        $details = array(
            'size' => 'small',
            'color' => 'blue'
        );
        self::setProperty($card, 'details', $details);

        // generate the array
        $properties = $card->toArray();
        $this->assertEquals($properties['card'], 'product', 'Product card type not set');
        $this->assertTrue((isset($properties['description']) && $properties['description'] === $description), 'Failed to output description');
        $this->assertTrue((isset($properties['creator']) && $properties['creator'] === $creator_screen_name), 'Failed to output stored creator screen name');
        $this->assertTrue((isset($properties['image']) && $properties['image'] === $image_url), 'Failed to output image URL');

        $details_position = 1;
        foreach ($details as $label => $data) {
            $label_key = 'label'.$details_position;
            $this->assertArrayHasKey($label_key, $properties, 'Failed to set label');
            $this->assertEquals($label, $properties[$label_key], 'Failed to store label');
            unset($label_key);

            $data_key = 'data'.$details_position;
            $this->assertArrayHasKey($data_key, $properties, 'Failed to set data');
            $this->assertEquals($data, $properties[$data_key], 'Failed to store data');
            unset($data_key);

            $details_position++;
        }
    }
}
