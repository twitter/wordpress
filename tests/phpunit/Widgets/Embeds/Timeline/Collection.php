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
 * @coversDefaultClass \Twitter\Widgets\Embeds\Timeline\Collection
 */
final class Collection extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Collection ID for testing
     *
     * @since 2.0.0
     *
     * @type string
     */
    const VALID_COLLECTION_ID = '539487832448843776';

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
        $classname = '\Twitter\Widgets\Embeds\Timeline\Collection';
        $mock = $this->getMockBuilder($classname)->disableOriginalConstructor()->getMock();

        // set expectations for the constructor call
        $mock->expects($this->once())
          ->method('setID')
          ->with(
              $this->equalTo(static::VALID_COLLECTION_ID)
          );

        $reflected_class = new \ReflectionClass($classname);
        $constructor = $reflected_class->getConstructor();
        $constructor->invoke($mock, static::VALID_COLLECTION_ID);
    }

    /**
     * Test verifying a collection ID
     *
     * @since 2.0.0
     *
     * @dataProvider idProvider
     *
     * @covers ::isValidSnowflakeID
     *
     * @param string $id                Twitter collection id
     * @param bool   $expected_validity expected validity
     * @param string $message           error message to display
     *
     * @return void
     */
    public function testIsValidSnowflakeID($id, $expected_validity, $message = '')
    {
        $result = \Twitter\Widgets\Embeds\Timeline\Collection::isValidSnowflakeID($id);

        if ($expected_validity) {
            $this->assertTrue($result, $message);
        } else {
            $this->assertFalse($result, $message);
        }
    }

    /**
     * Provide Twitter collection identifiers for testing
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
            array('539487832448843776', true,  'Failed to accept a valid collection ID' ),
            array('twitter',            false, 'Failed to reject a non-numeric ID'      ),
        );
    }

    /**
     * Test setting a collection ID
     *
     * @since 2.0.0
     *
     * @depends testIsValidSnowflakeID
     * @covers ::setID
     *
     * @return void
     */
    public function testSetID()
    {
        $id = '1234';
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Collection($id);
        $timeline->setID('twitter');
        $this->assertEquals($id, self::getProperty($timeline, 'id'), 'Set an ID from an invalid value');

        $timeline->setID(static::VALID_COLLECTION_ID);
        $this->assertEquals(static::VALID_COLLECTION_ID, self::getProperty($timeline, 'id'), 'Failed to set a valid collection ID');
    }

    /**
     * Test getting a collection identifier
     *
     * @since 2.0.0
     *
     * @covers ::getID
     *
     * @return void
     */
    public function testGetID()
    {
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Collection('');
        $this->assertEquals('', $timeline->getID(), 'Failed to return an empty string for expected null ID value');

        self::setProperty($timeline, 'id', static::VALID_COLLECTION_ID);
        $this->assertEquals(static::VALID_COLLECTION_ID, $timeline->getID(), 'Failed to return set ID class variable');
    }

    /**
     * Test getting a collection URL
     *
     * @since 2.0.0
     *
     * @covers ::getURL
     *
     * @return void
     */
    public function testGetURL()
    {
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Collection(static::VALID_COLLECTION_ID);
        $this->assertEquals(\Twitter\Widgets\Embeds\Timeline\Collection::BASE_URL . static::VALID_COLLECTION_ID, $timeline->getURL(), 'Failed to generate collection URL');
    }

    /**
     * Test setting grid widget type
     *
     * @since 2.0.0
     *
     * @covers ::setGridTemplate
     *
     * @return void
     */
    public function testSetGridTemplate()
    {
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Collection('');
        $timeline->setGridTemplate();
        $this->assertEquals(\Twitter\Widgets\Embeds\Timeline\Collection::WIDGET_TYPE_GRID, self::getProperty($timeline, 'widget_type'), 'Failed to set grid widget type');
    }

    /**
     * Test creating a collection object from an associative parameter array
     *
     * @since 2.0.0
     *
     * @covers ::fromArray
     *
     * @return void
     */
    public function testFromArray()
    {
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Collection::fromArray(array()), 'Failed to reject empty array');
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Collection::fromArray(array('foo'=>'bar')), 'Failed to reject array without an ID');
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Collection::fromArray(array('id'=>'')), 'Failed to reject collection ID of empty string');
        $this->assertNull(\Twitter\Widgets\Embeds\Timeline\Collection::fromArray(array('id'=>42)), 'Failed to reject an int ID');

        $width = 400;
        $timeline = \Twitter\Widgets\Embeds\Timeline\Collection::fromArray(array('id'=>static::VALID_COLLECTION_ID, 'width'=>$width));
        $this->assertEquals(static::VALID_COLLECTION_ID, self::getProperty($timeline, 'id'), 'Failed to set ID from associative parameter array');
        $this->assertEquals($width, self::getProperty($timeline, 'width'), 'Failed to set base parameter from associative array');
    }

    /**
     * Test converting a collection object to a data-* style associative array
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
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Collection(static::VALID_COLLECTION_ID);
        self::setProperty($timeline, 'width', $width);
        $data = $timeline->toArray();

        $this->assertArrayHasKey('id', $data, 'Collection array does not include an ID');
        $this->assertEquals(static::VALID_COLLECTION_ID, $data['id'], 'Collection ID in associative array does not match set object property');
        $this->assertArrayHasKey('width', $data, 'Collection array does not include a set base timeline value');
        $this->assertEquals($width, $data['width'], 'Collection array width value does not match set object property');
    }

    /**
     * Test converting a collection object with a widget_type set to grid to a data array
     *
     * Many set properties are not used in a grid view and should be ommitted from the resulting array
     *
     * @since 2.0.0
     *
     * @covers ::toArray
     *
     * @return void
     */
    public function testToArrayWidgetTypeGrid()
    {
        $width = 400;
        $limit = 10;
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Collection(static::VALID_COLLECTION_ID);
        self::setProperty($timeline, 'widget_type', \Twitter\Widgets\Embeds\Timeline\Collection::WIDGET_TYPE_GRID);
        self::setProperty($timeline, 'width', $width);
        self::setProperty($timeline, 'limit', $limit);
        self::setProperty($timeline, 'theme', \Twitter\Widgets\Embeds\Timeline\Collection::$THEME_DARK);
        self::setProperty($timeline, 'aria_politeness', \Twitter\Widgets\Embeds\Timeline\Collection::ARIA_POLITE_ASSERTIVE);
        self::setProperty($timeline, 'link_color', '000000');
        self::setProperty($timeline, 'border_color', 'FFFFFF');
        self::setProperty($timeline, 'chrome', array(
            \Twitter\Widgets\Embeds\Timeline\Collection::CHROME_NOHEADER => true,
            \Twitter\Widgets\Embeds\Timeline\Collection::CHROME_NOFOOTER => true,
        ));
        $data = $timeline->toArray();

        $this->assertArrayHasKey('id', $data, 'Collection array does not include an ID');
        $this->assertEquals(static::VALID_COLLECTION_ID, $data['id'], 'Collection ID in associative array does not match set object property');
        $this->assertArrayHasKey('widget-type', $data, 'Widget type not set');
        $this->assertEquals(\Twitter\Widgets\Embeds\Timeline\Collection::WIDGET_TYPE_GRID, $data['widget-type'], 'Widget type of grid not set');
        $this->assertArrayHasKey('width', $data, 'Collection array does not include a set base timeline value');
        $this->assertEquals($width, $data['width'], 'Collection array width value does not match set object property');
        $this->assertArrayHasKey('limit', $data, 'Collection array does not include a limit value');
        $this->assertEquals($limit, $data['limit'], 'Collection array limit does not include a set object property');

        $this->assertArrayNotHasKey('theme', $data, 'Theme is not a supported parameter for grid layout');
        $this->assertArrayNotHasKey('aria-polite', $data, 'Grid layout is not an ARIA live region; polite parameter not supported');
        $this->assertArrayNotHasKey('link-color', $data, 'Link color is not a supported parameter for grid layout');
        $this->assertArrayNotHasKey('border-color', $data, 'Border color is not a supported parameter for grid layout');
        $this->assertArrayHasKey('chrome', $data, 'Collection array does not include chrome customizations');
        $this->assertEquals(array(\Twitter\Widgets\Embeds\Timeline\Collection::CHROME_NOFOOTER), $data['chrome'], 'Valid chrome preference of nofooter not preserved for grid layout');

        // retest for the height behavior of a standard widget type
        self::setProperty($timeline, 'limit', null);
        self::setProperty($timeline, 'height', 300);
        $data = $timeline->toArray();
        $this->assertArrayNotHasKey('height', $data, 'Height is not a supported parameter of a grid layout');
    }

    /**
     * Test converting a collection object into oEmbed query parameters
     *
     * @since 2.0.0
     *
     * @depends testGetURL
     * @covers ::toOEmbedParameterArray
     *
     * @return void
     */
    public function testToOEmbedParameterArray()
    {
        $width = 400;
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Collection(static::VALID_COLLECTION_ID);
        self::setProperty($timeline, 'width', $width);
        $data = $timeline->toOEmbedParameterArray();

        $this->assertArrayHasKey('url', $data, 'Collection oEmbed parameter array does not include a URL');
        $this->assertEquals($timeline->getURL(), $data['url'], 'Collection URL in oEmbed parameter associative array does not match set object property');
        $this->assertArrayHasKey('maxwidth', $data, 'Collection oEmbed parameter array does not include a set base timeline value');
        $this->assertEquals($width, $data['maxwidth'], 'Collection oEmbed parameter array maxwidth value does not match set object property');
    }

    /**
     * Test converting a collection object with a widget_type set to grid to an oEmbed query parameter array
     *
     * @since 2.0.0
     *
     * @depends testGetURL
     * @covers ::toOEmbedParameterArray
     *
     * @return void
     */
    public function testToOembedParamterArrayWidgetTypeGrid()
    {
        $width = 400;
        $limit = 10;
        $timeline = new \Twitter\Widgets\Embeds\Timeline\Collection(static::VALID_COLLECTION_ID);
        self::setProperty($timeline, 'widget_type', \Twitter\Widgets\Embeds\Timeline\Collection::WIDGET_TYPE_GRID);
        self::setProperty($timeline, 'width', $width);
        self::setProperty($timeline, 'limit', $limit);
        self::setProperty($timeline, 'theme', \Twitter\Widgets\Embeds\Timeline\Collection::$THEME_DARK);
        self::setProperty($timeline, 'aria_politeness', \Twitter\Widgets\Embeds\Timeline\Collection::ARIA_POLITE_ASSERTIVE);
        self::setProperty($timeline, 'link_color', '000000');
        self::setProperty($timeline, 'border_color', 'FFFFFF');
        self::setProperty($timeline, 'chrome', array(
            \Twitter\Widgets\Embeds\Timeline\Collection::CHROME_NOHEADER => true,
            \Twitter\Widgets\Embeds\Timeline\Collection::CHROME_NOFOOTER => true,
        ));
        $data = $timeline->toOEmbedParameterArray();

        $this->assertArrayHasKey('url', $data, 'Collection oEmbed parameter array does not include a URL');
        $this->assertEquals($timeline->getURL(), $data['url'], 'Collection URL in oEmbed parameter associative array does not match set object property');
        $this->assertArrayHasKey('widget_type', $data, 'Widget type not set');
        $this->assertEquals(\Twitter\Widgets\Embeds\Timeline\Collection::WIDGET_TYPE_GRID, $data['widget_type'], 'Widget type of grid not set');
        $this->assertArrayHasKey('maxwidth', $data, 'Collection oEmbed parameter array does not include a set base timeline value');
        $this->assertEquals($width, $data['maxwidth'], 'Collection oEmbed parameter array maxwidth value does not match set object property');

        $this->assertArrayNotHasKey('theme', $data, 'Theme is not a supported parameter for grid layout');
        $this->assertArrayNotHasKey('aria_polite', $data, 'Grid layout is not an ARIA live region; polite parameter not supported');
        $this->assertArrayNotHasKey('link_color', $data, 'Link color is not a supported parameter for grid layout');
        $this->assertArrayNotHasKey('border_color', $data, 'Border color is not a supported parameter for grid layout');
        $this->assertArrayHasKey('chrome', $data, 'Collection oEmbed parameter array does not include chrome customizations');
        $this->assertEquals(array(\Twitter\Widgets\Embeds\Timeline\Collection::CHROME_NOFOOTER), $data['chrome'], 'Valid chrome preference of nofooter not preserved for grid layout oEmbed parameters');

        // retest for the height behavior of a standard widget type
        self::setProperty($timeline, 'limit', null);
        self::setProperty($timeline, 'height', 300);
        $data = $timeline->toArray();
        $this->assertArrayNotHasKey('maxheight', $data, 'Height is not a supported parameter of a grid layout');
    }
}
