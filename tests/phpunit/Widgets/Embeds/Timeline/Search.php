<?php
/*
The MIT License (MIT)

Copyright (c) 2017 Twitter Inc.

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
 * @coversDefaultClass \Twitter\Widgets\Embeds\Timeline\Search
 */
final class Search extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Widget ID for testing
     *
     * @since 2.0.0
     *
     * @type string
     */
    const VALID_WIDGET_ID = '600756918018179072';

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
        $classname = '\Twitter\Widgets\Embeds\Timeline\Search';
        $mock = $this->getMockBuilder($classname)->disableOriginalConstructor()->getMock();

        // set expectations for the constructor call
        $mock->expects($this->once())
          ->method('setWidgetID')
          ->with(
              $this->equalTo(self::VALID_WIDGET_ID)
          );

        $reflected_class = new \ReflectionClass($classname);
        $constructor = $reflected_class->getConstructor();
        $constructor->invoke($mock, self::VALID_WIDGET_ID);
    }

    /**
     * Test constructor with optional search terms parameter included
     *
     * @since 2.0.0
     *
     * @covers ::__construct
     *
     * @return void
     */
    public function testConstructorWithSearchTerms()
    {
        $classname = '\Twitter\Widgets\Embeds\Timeline\Search';
        $mock = $this->getMockBuilder($classname)->disableOriginalConstructor()->getMock();

        // set expectations for the optional search terms component of the constructor call
        $search_terms = 'foo bar';
        $mock->expects($this->once())
          ->method('setSearchTerms')
          ->with(
              $this->equalTo($search_terms)
          );

        $reflected_class = new \ReflectionClass($classname);
        $constructor = $reflected_class->getConstructor();
        $constructor->invoke($mock, self::VALID_WIDGET_ID, $search_terms);
    }

    /**
     * Test extracting a widget ID from a Twitter.com widget settings URL
     *
     * @since 2.0.0
     *
     * @covers ::getWidgetIDFromSettingsURL
     *
     * @return void
     */
    public function testGetWidgetIDFromSettingsURL()
    {
        $this->assertEquals(
            self::VALID_WIDGET_ID,
            \Twitter\Widgets\Embeds\Timeline\Search::getWidgetIDFromSettingsURL('https://twitter.com/settings/widgets/' . self::VALID_WIDGET_ID . '/edit'),
            'Failed to extract a widget ID from a Twitter.com widget settings URL'
        );
        $this->assertEquals(
            '',
            \Twitter\Widgets\Embeds\Timeline\Search::getWidgetIDFromSettingsURL('https://twitter.com/TwitterDev'),
            'Empty string not returned for invalid Twitter.com widget settings URL'
        );
    }

    /**
     * Test verifying a widget ID
     *
     * @since 2.0.0
     *
     * @dataProvider idProvider
     *
     * @covers ::isValidWidgetID
     *
     * @param string $id                Twitter widget id
     * @param bool   $expected_validity expected validity
     * @param string $message           error message to display
     *
     * @return void
     */
    public function testIsValidWidgetID($id, $expected_validity, $message = '')
    {
        $result = \Twitter\Widgets\Embeds\Timeline\Search::isValidWidgetID($id);

        if ($expected_validity) {
            $this->assertTrue($result, $message);
        } else {
            $this->assertFalse($result, $message);
        }
    }

    /**
     * Provide Twitter widget identifiers for testing
     *
     * @since 2.0.0
     *
     * @param array {
     *   @param array test cases
     * }
     */
    public static function idProvider()
    {
        return array(
            array(self::VALID_WIDGET_ID, true,  'Failed to accept a valid widget ID' ),
            array('twitter',             false, 'Failed to reject a non-numeric ID'  ),
        );
    }

    /**
     * Test setting a widget identifier
     *
     * @since 2.0.0
     *
     * @depends testIsValidWidgetID
     * @covers ::setWidgetID
     *
     * @return void
     */
    public function testSetWidgetID()
    {
        $id = '1234';
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Search($id);
        $timeline->setWidgetID('twitter');
        $this->assertEquals($id, self::getProperty($timeline, 'widget_id'), 'Set an ID from an invalid value');

        $timeline->setWidgetID(self::VALID_WIDGET_ID);
        $this->assertEquals(self::VALID_WIDGET_ID, self::getProperty($timeline, 'widget_id'), 'Failed to set a valid widget ID');
    }

    /**
     * Test getting a widget identifier
     *
     * @since 2.0.0
     *
     * @covers ::getWidgetID
     *
     * @return void
     */
    public function testGetWidgetID()
    {
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Search('');
        $this->assertEquals('', $timeline->getWidgetID(), 'Failed to return an empty string for expected null ID value');

        self::setProperty($timeline, 'widget_id', self::VALID_WIDGET_ID);
        $this->assertEquals(self::VALID_WIDGET_ID, $timeline->getWidgetID(), 'Failed to return set ID class variable');
    }

    /**
     * Test setting search terms via setter
     *
     * @since 2.0.0
     *
     * @covers ::setSearchTerms
     *
     * @return void
     */
    public function testSetSearchTerms()
    {
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Search('');
        $timeline->setSearchTerms('');
        $this->assertNull(self::getProperty($timeline, 'search_terms'), 'Failed to reject empty search terms');

        $search_terms = 'foo bar';
        $timeline->setSearchTerms($search_terms);
        $this->assertEquals($search_terms, self::getProperty($timeline, 'search_terms'), 'Failed to set valid search terms');
    }

    /**
     * Test getting a search timeline URL
     *
     * @since 2.0.0
     *
     * @covers ::getURL
     *
     * @return void
     */
    public function testGetURL()
    {
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Search('');
        $search_terms = 'foo bar';
        $timeline->setSearchTerms($search_terms);
        $this->assertEquals(\Twitter\Widgets\Embeds\Timeline\Search::BASE_URL . '?q=' . rawurlencode($search_terms), $timeline->getURL(), 'Failed to generate search URL');
    }

    /**
     * Test creating a search timeline object from an associative parameter array
     *
     * @since 2.0.0
     *
     * @covers ::fromArray
     *
     * @return void
     */
    public function testFromArray()
    {
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Search::fromArray(array()), 'Failed to reject empty array');
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Search::fromArray(array('foo'=>'bar')), 'Failed to reject array without an ID');
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Search::fromArray(array('widget_id'=>'')), 'Failed to reject widget ID of empty string');
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Search::fromArray(array('widget_id'=>42)), 'Failed to reject an int ID');

        $width = 400;
        $timeline = \Twitter\Widgets\Embeds\Timeline\Search::fromArray(array('widget_id'=>static::VALID_WIDGET_ID, 'width'=>$width));
        $this->assertEquals(self::VALID_WIDGET_ID, self::getProperty($timeline, 'widget_id'), 'Failed to set widget ID from associative parameter array');
        $this->assertEquals($width, self::getProperty($timeline, 'width'), 'Failed to set base parameter from associative array');

        $timeline = \Twitter\Widgets\Embeds\Timeline\Search::fromArray(array('widget_id'=>'https://twitter.com/settings/widgets/' . self::VALID_WIDGET_ID . '/edit'));
        $this->assertEquals(self::VALID_WIDGET_ID, self::getProperty($timeline, 'widget_id'), 'Failed to set widget ID from a Twitter.com widget settings URL');
    }

    /**
     * Test converting a search timeline object to a data-* style associative array
     *
     * @since 2.0.0
     *
     * @covers ::toArray
     *
     * @return void
     */
    public function testToArray()
    {
        $width = 400;
        $theme = 'light';
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Search(self::VALID_WIDGET_ID);
        self::setProperty($timeline, 'width', $width);
        self::setProperty($timeline, 'theme', $theme);
        $data = $timeline->toArray();

        $this->assertArrayHasKey('widget-id', $data, 'Search timeline array does not include a widget ID');
        $this->assertEquals(self::VALID_WIDGET_ID, $data['widget-id'], 'Widget ID in associative array does not match set object property');
        $this->assertArrayHasKey('width', $data, 'Search timeline array does not include a set base timeline value');
        $this->assertEquals($width, $data['width'], 'Search timeline array width value does not match set object property');
        $this->assertArrayHasKey('theme', $data, 'Search timeline array does not include a theme value');
        $this->assertEquals($theme, $data['theme'], 'Search timeline array does not contain default theme value');
    }

    /**
     * Test converting a search timeline object into oEmbed query parameters. Not supported
     *
     * @since 2.0.0
     *
     * @covers ::toOEmbedParameterArray
     *
     * @return void
     */
    public function testToOEmbedParameterArray()
    {
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Search(self::VALID_WIDGET_ID);
        self::setProperty($timeline, 'width', 400);
        $this->assertEmpty($timeline->toOEmbedParameterArray(), 'Supplied oEmbed parameters for a timeline without oEmbed support');
    }
}
