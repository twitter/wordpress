<?php
/*
The MIT License (MIT)

Copyright (c) 2016 Twitter Inc.

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

namespace Twitter\Tests\Widgets\Embeds;

/**
 * @coversDefaultClass \Twitter\Widgets\Embeds\Moment
 */
final class Moment extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Moment ID for testing
     *
     * @since 2.0.0
     *
     * @type string
     */
    const VALID_MOMENT_ID = '650667182356082688';

    /**
     * Test expected use of the constructor
     *
     * @since 2.0.0
     *
     * @covers ::__construct
     *
     * @return void
     */
    public function testConstructor()
    {
        $timeline = new \Twitter\Widgets\Embeds\Moment(self::VALID_MOMENT_ID);
        $this->assertEquals(self::VALID_MOMENT_ID, self::getProperty($timeline, 'id'), 'Moment constructor did not set valid ID');
        $this->assertEquals(\Twitter\Widgets\Embeds\Moment::WIDGET_TYPE_GRID, self::getProperty($timeline, 'widget_type'), 'Moment constructor did not set grid widget type');
    }
}
