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

namespace Twitter\Tests\Widgets\Embeds\Tweet;

/**
 * @coversDefaultClass \Twitter\Widgets\Embeds\Tweet\Base
 */
final class Base extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Valid Tweet ID expected to pass constructor methods
     *
     * @since 2.0.0
     *
     * @type string
     */
    const VALID_TWEET_ID = '656832713781936128';

    /**
     * Set up a widget object for each test
     *
     * @since 1.0.0
     *
     * @type \Twitter\Widgets\Embeds\Tweet\Base
     */
    protected $widget;

    /**
     * Initialize a new Widgets\Base object for use in each test
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->widget = $this->getMockBuilder('\Twitter\Widgets\Embeds\Tweet\Base')->setConstructorArgs(array(self::VALID_TWEET_ID))->getMockForAbstractClass();
    }

    /**
     * Test Tweet ID validity
     *
     * @since 2.0.0
     *
     * @dataProvider idProvider
     *
     * @covers ::isValidID
     *
     * @param string $id             Tweet ID
     * @param bool   $expected_valid expected validity of the passed ID
     * @param string $message        error message to display
     *
     * @return void
     */
    public function testIsValidID($id, $expected_valid, $message = '')
    {
        $class = $this->widget;
        $validity = $class::isValidID($id);
        if ($expected_valid) {
            $this->assertTrue($validity, $message);
        } else {
            $this->assertFalse($validity, $message);
        }
    }

    /**
     * Test setting a Tweet ID
     *
     * @since 2.0.0
     *
     * @depends testIsValidID
     *
     * @covers ::setId
     *
     * @return void
     */
    public function testSetID()
    {
        self::setProperty($this->widget, 'id', null);
        $this->widget->setID('foo');
        $this->assertNull(self::getProperty($this->widget, 'id'), 'Set an invalid ID');
        $this->widget->setID(self::VALID_TWEET_ID);
        $this->assertEquals(self::VALID_TWEET_ID, self::getProperty($this->widget, 'id'), 'Failed to set a valid ID');
    }

    /**
     * Tweet ID values for testing
     *
     * @since 2.0.0
     *
     * @return array value to test, expected validity, error message
     */
    public static function idProvider()
    {
        return array(
            array( self::VALID_TWEET_ID, true,  'Failed to accept a valid ID string' ),
            array( 'foo',                false, 'Accepted a non-numeric identifier' ),
        );
    }

    /**
     * Test retrieving the id property through a getter method
     *
     * @since 2.0.0
     *
     * @covers ::getID
     *
     * @return void
     */
    public function testGetID()
    {
        self::setProperty($this->widget, 'id', self::VALID_TWEET_ID);
        $this->assertEquals(self::VALID_TWEET_ID, $this->widget->getID(), 'Failed to retrieve Tweet ID property');
    }

    /**
     * Test building an absolute URL for the Tweet
     *
     * @since 2.0.0
     *
     * @covers ::getURL
     *
     * @return void
     */
    public function testGetURL()
    {
        self::setProperty($this->widget, 'id', self::VALID_TWEET_ID);
        $this->assertEquals('https://twitter.com/_/status/'.self::VALID_TWEET_ID, $this->widget->getURL(), 'Failed to build URL');
    }
}
