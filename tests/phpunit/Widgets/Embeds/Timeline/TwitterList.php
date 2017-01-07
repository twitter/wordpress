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
 * @coversDefaultClass \Twitter\Widgets\Embeds\Timeline\TwitterList
 */
final class TwitterList extends \Twitter\Tests\TestWithPrivateAccess
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
        $classname = '\Twitter\Widgets\Embeds\Timeline\TwitterList';
        $mock = $this->getMockBuilder($classname)->disableOriginalConstructor()->getMock();

        $screen_name = 'twitter';
        $slug = 'official-twitter-accounts';

        // set expectations for the constructor call
        $mock->expects($this->once())
          ->method('setScreenName')
          ->with(
              $this->equalTo($screen_name)
          );
        /*$mock->expects($this->once())
		  ->method('setSlug')
		  ->with(
		    $this->equalTo($slug)
		  );*/

        $reflected_class = new \ReflectionClass($classname);
        $constructor = $reflected_class->getConstructor();
        $constructor->invoke($mock, $screen_name, $slug);
    }

    /**
     * Test if a slug is considered valid
     *
     * @since 2.0.0
     *
     * @covers ::isValidSlug
     * @small
     *
     * @dataProvider slugProvider
     *
     * @param string $slug              slug to test
     * @param bool   $expected_validity expected validity
     * @param string $message           error message to display
     *
     *
     */
    public function testIsValidSlug($slug, $expected_validity, $message = '')
    {
        $is_valid = \Twitter\Widgets\Embeds\Timeline\TwitterList::isValidSlug($slug);
        if ($expected_validity) {
            $this->assertTrue($is_valid, $message);
        } else {
            $this->assertFalse($is_valid, $message);
        }
    }

    /**
     * Slugs and expected validity
     *
     * @since 2.0.0
     *
     * @return array slug, expected validity, error message
     */
    public static function slugProvider()
    {
        return array(
            array( 'official-twitter-accounts', true,  'Rejected a dashed slug'                   ),
            array( '9lives',                    false, 'Accepted a slug beginning with a digit'   ),
            array( 'Ke$ha',                     false, 'Accepted a slug with a special character' ),
        );
    }

    /**
     * Test setting a slug property
     *
     * @since 2.0.0
     *
     * @covers ::setSlug
     * @depends testIsValidSlug
     *
     * @return void
     */
    public function testSetSlug()
    {
        $slug = 'official-twitter-accounts';
        $list = new \Twitter\Widgets\Embeds\Timeline\TwitterList('', '');

        $list->setSlug($slug);
        $property = self::getProperty($list, 'slug');
        $this->assertEquals($slug, $property, 'Slug not set');
    }

    /**
     * Test creating a new list object from an associative array
     *
     * @since 2.0.0
     *
     * @covers ::fromArray
     *
     * @return void
     */
    public function testFromArray()
    {
        $screen_name = 'twitter';
        $slug = 'official-twitter-accounts';

        $list = \Twitter\Widgets\Embeds\Timeline\TwitterList::fromArray(array(
            'screen_name' => $screen_name,
            'slug'        => $slug
        ));

        $this->assertEquals($screen_name, self::getProperty($list, 'screen_name'), 'Username not set from array');
        $this->assertEquals($slug, self::getProperty($list, 'slug'), 'Slug not set from array');
    }

    /**
     * Test converting a list object to an associative array
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
        $slug = 'official-twitter-accounts';

        $list = new \Twitter\Widgets\Embeds\Timeline\TwitterList($screen_name, $slug);
        $data = $list->toArray();

        $this->assertArrayHasKey('screen-name', $data, 'Username not returned in array');
        $this->assertEquals($screen_name, $data['screen-name'], 'Expected username not returned');
        $this->assertArrayHasKey('slug', $data, 'Slug not returned in array');
        $this->assertEquals($slug, $data['slug'], 'Expected slug not returned');
    }
}
