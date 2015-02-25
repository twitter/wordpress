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
 * @coversDefaultClass \Twitter\Cards\SummaryLargeImage
 */
final class SummaryLargeImage extends \Twitter\Tests\TestWithPrivateAccess
{
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
        $card = new \Twitter\Cards\SummaryLargeImage();

        // generate the array
        $properties = $card->toArray();
        $this->assertEquals($properties['card'], 'summary_large_image', 'Summary large image card type not set');
    }
}
