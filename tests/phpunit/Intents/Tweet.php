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

namespace Twitter\Tests\Intents;

/**
 * @coversDefaultClass \Twitter\Intents\Tweet
 */
final class Tweet extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Initialized Tweet intent
     *
     * @since 1.0.0
     *
     * @type \Twitter\Intents\Tweet
     */
    protected $intent;

    /**
     * Initialize a Tweet intent object before each test
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->intent = new \Twitter\Intents\Tweet();
    }

    /**
     * Test disabling validation
     *
     * @since 1.0.0
     *
     * @covers ::disableValidation
     * @small
     *
     * @return void
     */
    public function testDisableValidation()
    {
        $this->intent->disableValidation();
        $this->assertValidationDisabled();
    }

    /**
     * Test if the validate_inputs property has been successfully set to false
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function assertValidationDisabled()
    {
        $this->assertFalse(self::getProperty($this->intent, 'validate_inputs'), 'failed to disable validation');
    }

    /**
     * Test enabling validation
     *
     * @since 1.0.0
     *
     * @covers ::enableValidation
     * @small
     *
     * @return void
     */
    public function testEnableValidation()
    {
        $this->intent->disableValidation();
        $this->intent->enableValidation();

        $this->assertTrue(self::getProperty($this->intent, 'validate_inputs'), 'failed to reset validation to true');
    }

    /**
     * Test setting in_reply_to
     *
     * @since 1.0.0
     *
     * @covers ::setInReplyTo
     * @small
     *
     * @return void
     */
    public function testSetInReplyTo()
    {
        $status_id = '20';
        $this->intent->setInReplyTo($status_id);

        $this->assertInReplyToSet($status_id, 'failed to set in_reply_to');
    }

    /**
     * Test if the in_reply_to property has been successfully set to expected result
     *
     * @since 1.0.0
     *
     * @param string $expected_result expected value of in_reply_to
     * @param string $message error message
     *
     * @return void
     */
    protected function assertInReplyToSet($expected_result, $message = '')
    {
        $this->assertEquals($expected_result, self::getProperty($this->intent, 'in_reply_to'), $message);
    }

    /**
     * Test setting text
     *
     * @since 1.0.0
     *
     * @covers ::setText
     * @small
     *
     * @dataProvider textProvider
     *
     * @return void
     */
    public function testSetText($test_value, $expected_result, $message = '')
    {
        $this->intent->setText($test_value);
        $this->assertTextSet($expected_result, $message);
    }

    /**
     * Test if the text property has been successfully set to expected result
     *
     * @since 1.0.0
     *
     * @param string $expected_result expected value of text
     * @param string $message error message
     *
     * @return void
     */
    protected function assertTextSet($expected_result, $message = '')
    {
        $this->assertEquals($expected_result, self::getProperty($this->intent, 'text'), $message);
    }

    /**
     * Supply text values to be tested
     *
     * @since 1.0.0
     *
     * @return array text values to test {
     *   @type array test value, expected value, message
     }
     */
    public static function textProvider()
    {
        return array(
            array( 'Hello world', 'Hello world', 'failed to set text' ),
            array( ' Hello world ', 'Hello world', 'failed to trim text before setting' ),
        );
    }

    /**
     * Test URL tester
     *
     * @since 1.0.0
     *
     * @covers ::isHTTPURL
     * @small
     *
     * @dataProvider urlProvider
     *
     * @return void
     */
    public function testIsHTTPURL($url, $expected_validity, $message = '')
    {
        if ($expected_validity) {
            $this->assertTrue(\Twitter\Intents\Tweet::isHTTPURL($url), $message);
        } else {
            $this->assertFalse(\Twitter\Intents\Tweet::isHTTPURL($url), $message);
        }
    }

    /**
     * Test setting URL
     *
     * @since 1.0.0
     *
     * @covers ::setURL
     * @small
     *
     * @dataProvider urlProvider
     *
     * @return void
     */
    public function testSetURL($url, $expected_validity, $message = '')
    {
        $this->intent->setURL($url);

        $property = self::getProperty($this->intent, 'url');
        if ($expected_validity) {
            $this->assertEquals($url, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * List URLs for testing
     *
     * @since 1.0.0
     *
     * @return array URL values to test {
     *   @type array text URL, message
     * }
     */
    public static function urlProvider()
    {
        return array(
            array( 'http://example.com/', true, 'did not accept HTTP scheme as a valid URL' ),
            array( 'https://twitter.com/', true, 'did not accept HTTPS scheme as a valid URL' ),
            array( '/foo/bar/', false, 'did not reject a relative path' ),
        );
    }

    /**
     * Test getting a via username
     *
     * @since 1.0.1
     *
     * @covers ::getVia
     * @small
     *
     * @return void
     */
    public function testGetVia()
    {
        // empty string
        $this->assertEquals('', $this->intent->getVia(), 'Failed to return an empty string for null via');

        $username = 'twitter';
        self::setProperty($this->intent, 'via', $username);
        $this->assertEquals($username, $this->intent->getVia(), 'Failed to retrieve via');
    }

    /**
     * Test setting a via username
     *
     * @since 1.0.0
     *
     * @covers ::setVia
     * @small
     *
     * @dataProvider screennameProvider
     *
     * @param string $screen_name screen_name to test
     * @param string|null $expected_result expected
     * @param string $message error message to display
     *
     * @return void
     */
    public function testSetVia($screen_name, $expected_result, $message = '')
    {
        $this->intent->setVia($screen_name);

        $property = self::getProperty($this->intent, 'via');
        if ($expected_result) {
            $this->assertEquals($expected_result, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * List screen_names for testing
     *
     * @since 1.0.0
     *
     * @return array screen_name values to test {
     *   @type array screen_name, expected validity, message
     * }
     */
    public static function screennameProvider()
    {
        return array(
            array( 'twitter', 'twitter', 'Failed to set screen_name' ),
            array( '@twitter', 'twitter', 'Failed to set screen_name with @ prefix' ),
            array( 'foo-bar', null, 'Allowed invalid screen_name' ),
        );
    }

    /**
     * Test an attempt to set multiple related screen_name strings representing the same screen_name
     *
     * @since 1.0.0
     *
     * @covers ::addRelated
     * @small
     *
     * @return void
     */
    public function testMultipleIdenticalRelatedScreenNames()
    {
        $screen_names = array(
            'twitter',
            '@twitter',
            'TWITTER',
        );

        array_walk($screen_names, array( $this->intent, 'addRelated' ));
        $this->assertCount(1, self::getProperty($this->intent, 'related'), 'Failed to collapse multiple representations of a screen_name into a single representative screen_name');
    }

    /**
     * Test setting a related Twitter screen_name
     *
     * @since 1.0.0
     *
     * @covers ::addRelated
     * @small
     *
     * @dataProvider screennameProvider
     *
     * @param string $screen_name screen_name to test
     * @param string|null $expected_result expected
     * @param string $message error message to display
     *
     * @return void
     */
    public function testAddRelated($screen_name, $expected_result, $message = '')
    {
        $this->intent->addRelated($screen_name);

        $related = self::getProperty($this->intent, 'related');
        $related = array_keys($related);
        $related = reset($related);
        $this->assertEquals(strtolower($expected_result), $related, $message);
    }

    /**
     * Test adding a label for a related screen_name
     *
     * @since 1.0.0
     *
     * @covers ::addRelated
     * @small
     *
     * @return void
     */
    public function testAddRelatedLabel()
    {
        $label = 'Twitter main account';
        $this->intent->addRelated('twitter', $label);

        $related = self::getProperty($this->intent, 'related');
        $related = array_values($related);
        $related = reset($related);
        $this->assertEquals($label, $related, 'Failed to set a related screen_name label');
    }

    /**
     * Test setting multiple hashtags that all should evaluate to the same comparison hashtag
     *
     * @since 1.0.0
     *
     * @covers ::addHashtag
     * @small
     *
     * @return void
     */
    public function testMultipleIdenticalHashtags()
    {
        $hashtags = array(
            'foo',  // lowercase
            '#foo', // trim me
            'FOO',  // caps
        );

        array_walk($hashtags, array( $this->intent, 'addHashtag' ));

        $this->assertCount(1, self::getProperty($this->intent, 'hashtags'), 'Failed to collapse multiple representations of a hashtag into a single representative hashtag');
    }

    /**
     * Test setting a single hashtag
     *
     * @since 1.0.0
     *
     * @covers ::addHashtag
     * @small
     *
     * @dataProvider hashtagProvider
     *
     * @return void
     */
    public function testAddHashtag($hashtag, $expected_result, $message = '')
    {
        $this->intent->addHashtag($hashtag);

        $hashtags = self::getProperty($this->intent, 'hashtags');
        $hashtags = array_values($hashtags);
        $hashtags = reset($hashtags);
        $this->assertEquals($expected_result, $hashtags, $message);
    }

    /**
     * Hashtags to test
     *
     * @since 1.0.0
     *
     * @return array list of hashtags {
     *   @type array hashtag, expected set result error message
     * }
     */
    public static function hashtagProvider()
    {
        return array(
            array( 'foo', 'foo', 'Failed to set a basic hashtag' ),
            array( '#foo', 'foo', 'failed to trim hashtag symbol' ),
        );
    }

    /**
     * Test disabling validation from the options array setter
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @dataProvider \Twitter\Tests\CommonProviders::falseyProvider
     *
     * @return void
     */
    public function testDisableValidationFromArray($test_value, $message = '')
    {
        $this->intent = \Twitter\Intents\Tweet::fromArray(array( 'validate' => $test_value ));
        $this->assertValidationDisabled();
    }

    /**
     * Test setting in_reply_to Tweet ID from array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testSetInReplyToFromArray()
    {
        $status_id = '20';
        $this->intent = \Twitter\Intents\Tweet::fromArray(array( 'in_reply_to' => $status_id ));
        $this->assertInReplyToSet($status_id, 'failed to set in_reply_to from array');
    }

    /**
     * Test setting Tweet text from array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testSetTextFromArray()
    {
        $text = 'Hello world';
        $this->intent = \Twitter\Intents\Tweet::fromArray(array( 'text' => $text ));
        $this->assertTextSet($text, 'Failed to set text from array');
    }

    /**
     * Test setting URL from array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testSetURLFromArray()
    {
        $url = 'http://example.com/';
        $this->assertEquals(
            $url,
            self::getProperty(\Twitter\Intents\Tweet::fromArray(array( 'url' => $url )), 'url'),
            'Failed to set URL from array'
        );
    }

    /**
     * Test setting via screen_name from array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testSetViaFromArray()
    {
        $via = 'twitter';
        $this->assertEquals(
            $via,
            self::getProperty(\Twitter\Intents\Tweet::fromArray(array( 'via' => $via )), 'via'),
            'Failed to set via from array'
        );
    }

    /**
     * Test setting hashtags when a hashtag array is passed in an options array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testHashtagsArrayFromArray()
    {
        $this->assertCount(
            3,
            self::getProperty(
                \Twitter\Intents\Tweet::fromArray(array( 'hashtags' => array( 'one', 'two', 'three' ) )),
                'hashtags'
            )
        );
    }

    /**
     * Test setting hashtags when a hashtag CSV string is passed in an options array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testHashtagsCSVStringFromArray()
    {
        $this->assertCount(
            3,
            self::getProperty(
                \Twitter\Intents\Tweet::fromArray(array( 'hashtags' => 'one,two,three' )),
                'hashtags'
            ),
            'Failed to add three unique hashtags from a CSV string'
        );
    }

    /**
     * Test setting related scren_names when a related array is passed in an options array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testRelatedArrayFromArray()
    {
        $this->intent = \Twitter\Intents\Tweet::fromArray(array( 'related' => array(
            'twitter' => '',
            'twitterdev' => '',
            'twitterapi' => '',
        ) ));

        $this->assertCount(
            3,
            self::getProperty($this->intent, 'related'),
            'Failed to add three unique screen_names passed as an array as related accounts'
        );
    }

    /**
     * Test setting related screen_names when a related CSV string is passed in an options array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testRelatedCSVStringFromArray()
    {
        $this->intent = \Twitter\Intents\Tweet::fromArray(array( 'related' => 'twitter,twitterdev,twitterapi' ));

        $this->assertCount(
            3,
            self::getProperty($this->intent, 'related'),
            'Failed to add three unique screen_names passed as a CSV string as related accounts'
        );
    }

    /**
     * Test setting related screen_name with label from a related array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testRelatedLabelArrayFromArray()
    {
        $related = array( 'twitter' => 'Twitter: main account' );
        $this->intent = \Twitter\Intents\Tweet::fromArray(array( 'related' => $related ));

        $this->assertEquals(
            $related,
            self::getProperty($this->intent, 'related'),
            'Failed to add a screen_name and label passed as an array'
        );
    }

    /**
     * Test setting related screen_name with label from a related array
     *
     * @since 1.0.0
     *
     * @covers ::fromArray
     * @small
     *
     * @return void
     */
    public function testRelatedLabelCSVStringFromArray()
    {
        $related = 'twitter:Twitter: main account,twitterdev';

        $this->intent = \Twitter\Intents\Tweet::fromArray(array( 'related' => $related ));
        $property = self::getProperty($this->intent, 'related');

        $this->assertCount(
            2,
            $property,
            'Failed to set two related screen_names from a related CSV string with a label'
        );

        $this->assertTrue(
            ( isset($property['twitter']) && $property['twitter'] === 'Twitter: main account' ),
            'Failed to set a related account label from a CSV string'
        );
        $this->assertArrayHasKey(
            'twitterdev',
            $property,
            'Failed to set a related account screen_name provided after a label in a CSV string'
        );
    }
}
