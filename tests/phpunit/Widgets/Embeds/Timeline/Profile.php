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

namespace Twitter\Tests\Widgets\Embeds\Timeline;

/**
 * @coversDefaultClass \Twitter\Widgets\Embeds\Timeline\Profile
 */
final class Profile extends \Twitter\Tests\TestWithPrivateAccess
{
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
        $classname = '\Twitter\Widgets\Embeds\Timeline\Profile';
        $mock = $this->getMockBuilder($classname)->disableOriginalConstructor()->getMock();

        $screen_name = 'twitter';

        // set expectations for the constructor call
        $mock->expects($this->once())
          ->method('setScreenName')
          ->with(
              $this->equalTo($screen_name)
          );

        $reflected_class = new \ReflectionClass($classname);
        $constructor = $reflected_class->getConstructor();
        $constructor->invoke($mock, $screen_name);
    }

    /**
     * Test setting a screen name
     *
     * @since 2.0.0
     *
     * @depends testConstructor
     * @dataProvider screenNameProvider
     *
     * @covers ::setScreenName
     *
     * @param string $screen_name Twitter handle
     * @param bool   $is_valid    expected validity
     * @param string $message     error message to display
     *
     * @return void
     */
    public function testSetScreenName($screen_name, $is_valid, $message = '')
    {
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Profile($screen_name);
        $property = self::getProperty($timeline, 'screen_name');

        if ($is_valid) {
            $this->assertEquals('twitter', $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Provide Twitter handles for testing
     *
     * @since 2.0.0
     *
     * @param array {
     *   @param array test cases
     * }
     */
    public static function screenNameProvider()
    {
        return array(
            array('twitter',   true,  'Failed to accept a username of twitter'                ),
            array(' twitter ', true,  'Failed to accept username with whitespace padding'     ),
            array('@twitter',  true,  'Failed to accept a username prepended with an at-sign' ),
            array('Ke$ha',     false, 'Accepted invalid username'                             )
        );
    }

    /**
     * Test initializing and setting an object from a passed associative array
     *
     * @since 2.0.0
     *
     * @covers ::fromArray
     *
     * @return void
     */
    public function testFromArray()
    {
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Profile::fromArray(42), 'Did not reject an int passed into an array builder');

        $screen_name = 'twitter';
        $timeline = \Twitter\Widgets\Embeds\Timeline\Profile::fromArray(array('screen_name'=>$screen_name));
        $this->assertInstanceOf('\Twitter\Widgets\Embeds\Timeline\Profile', $timeline, 'Did not generate a new profile timeline');
        $this->assertEquals($screen_name, self::getProperty($timeline, 'screen_name'));
    }

    /**
     * Test converting a valid timeline object into an associative array
     *
     * @since 2.0.0
     *
     * @covers ::toArray
     *
     * @return void
     */
    public function testToArray()
    {
        $screen_name = 'twitter';

        $timeline = new \Twitter\Widgets\Embeds\Timeline\Profile($screen_name);

        $timeline_array = $timeline->toArray();
        $this->assertArrayHasKey('screen-name', $timeline_array);
        $this->assertEquals($screen_name, $timeline_array['screen-name'], 'Set Twitter handle not returned in array conversion');

        $timeline = new \Twitter\Widgets\Embeds\Timeline\Profile(42);
        $timeline->setWidth(400);
        $this->assertEmpty($timeline->toArray());
    }
}
