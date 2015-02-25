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
 * @coversDefaultClass \Twitter\Cards\Components\Creator
 */
final class Creator extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Mocked Creator trait
     *
     * @since 1.0.0
     *
     * @type PHPUnit_Framework_MockObject_MockObject
     */
    protected $creator;

    /**
     * Create a mocked trait for testing
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->creator = $this->getMockForTrait('\Twitter\Cards\Components\Creator');
    }

    /**
     * Test setting the creator property
     *
     * @since 1.0.0
     *
     * @covers ::setCreator
     * @small
     *
     * @return void
     */
    public function testSetCreator()
    {
        $this->creator->setCreator(
            $this->getMockBuilder('\Twitter\Cards\Components\Account')->getMock()
        );
        $this->assertNotNull(self::getProperty($this->creator, 'creator'), 'Failed to set creator property');
    }
}
