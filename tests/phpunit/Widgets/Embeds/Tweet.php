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
 * @coversDefaultClass \Twitter\Widgets\Embeds\Tweet
 */
final class Tweet extends \Twitter\Tests\TestWithPrivateAccess
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
        $classname = '\Twitter\Widgets\Embeds\Tweet';
        $mock = $this->getMockBuilder($classname)->disableOriginalConstructor()->getMock();

        // set expectations for the constructor call
        $mock->expects($this->once())
          ->method('setID')
          ->with(
              $this->equalTo(self::VALID_TWEET_ID)
          );

        $reflected_class = new \ReflectionClass($classname);
        $constructor = $reflected_class->getConstructor();
        $constructor->invoke($mock, self::VALID_TWEET_ID);
    }

    /**
     * Test setting a width
     *
     * @since 2.0.0
     *
     * @covers ::setWidth
     * @dataProvider widthProvider
     *
     * @param int|string $width          value to test
     * @param bool       $expected_valid expected validity of the passed width
     * @param string     $message        error message
     */
    public function testSetWidth($width, $expected_valid, $message)
    {
        $tweet = new \Twitter\Widgets\Embeds\Tweet(self::VALID_TWEET_ID);
        $tweet->setWidth($width);
        $property = self::getProperty($tweet, 'width');
        if ($expected_valid) {
            $this->assertEquals($width, $property, $message);
        } else {
            $this->assertNull($property, $message);
        }
    }

    /**
     * Width values to test for validity
     *
     * @since 2.0.0
     *
     * @return array width value, expected valid, message
     */
    public static function widthProvider()
    {
        return array(
            array( 42,    false, 'Accepted a width below the allowed range' ),
            array( 400,   true,  'Rejected a width in the allowed range' ),
            array( '400', false,  'Accepted a passed string' ),
            array( 1234, false, 'Accepted a width above the allowed range' ),
        );
    }

    /**
     * Test resetting a Tweet configuration to show cards (default true back to true)
     *
     * @since 2.0.0
     *
     * @covers ::showCards
     *
     * @return void
     */
    public function testShowCards()
    {
        $property = 'cards';
        $tweet = new \Twitter\Widgets\Embeds\Tweet(self::VALID_TWEET_ID);
        self::setProperty($tweet, $property, false);
        $tweet->showCards();
        $this->assertTrue(self::getProperty($tweet, $property));
    }

    /**
     * Test setting a Tweet configuration to hide cards
     *
     * @since 2.0.0
     *
     * @covers ::hideCards
     *
     * @return void
     */
    public function testHideCards()
    {
        $tweet = new \Twitter\Widgets\Embeds\Tweet(self::VALID_TWEET_ID);
        $tweet->hideCards();
        $this->assertFalse(self::getProperty($tweet, 'cards'));
    }

    /**
     * Test resetting a Tweet configuration to hide a parent Tweet (default true back to true)
     *
     * @since 2.0.0
     *
     * @covers ::showParentTweet
     *
     * @return void
     */
    public function testShowParentTweet()
    {
        $property = 'conversation';
        $tweet = new \Twitter\Widgets\Embeds\Tweet(self::VALID_TWEET_ID);
        self::setProperty($tweet, $property, false);
        $tweet->showParentTweet();
        $this->assertTrue(self::getProperty($tweet, $property));
    }

    /**
     * Test setting a Tweet configuration to hide a possible parent Tweet
     *
     * @since 2.0.0
     *
     * @covers ::hideParentTweet
     *
     * @return void
     */
    public function testHideParentTweet()
    {
        $tweet = new \Twitter\Widgets\Embeds\Tweet(self::VALID_TWEET_ID);
        $tweet->hideParentTweet();
        $this->assertFalse(self::getProperty($tweet, 'conversation'));
    }

    /**
     * Test setting an align property through a setter method with validity checks
     *
     * @since 2.0.0
     *
     * @dataProvider alignProvider
     * @covers ::setAlign
     *
     * @param string $align    value to test
     * @param string $expected expected stored value
     * @param string $message  error message
     *
     * @return void
     */
    public function testSetAlign($align, $expected, $message = '')
    {
        $tweet = new \Twitter\Widgets\Embeds\Tweet(self::VALID_TWEET_ID);
        $tweet->setAlign($align);
        $this->assertEquals($expected, self::getProperty($tweet, 'align'), $message);
    }

    /**
     * Possible align values and expected stored results with validity
     *
     * @since 2.0.0
     *
     * @return array align value to test, expected result, error message
     */
    public static function alignProvider()
    {
        return array(
            array( 'left', 'left', 'Failed to set valid align value' ),
            array( '  left  ', 'left', 'Failed to set valid align value with whitespace padding' ),
            array( 'LEFT', 'left', 'Failed to set valid align value provided in all caps' ),
            array( 'top', 'none', 'Set unsupported align value' ),
        );
    }

    /**
     * Test creating a Tweet object from an associative array
     *
     * @since 2.0.0
     *
     * @covers ::fromArray
     *
     * @return void
     */
    public function testFromArray()
    {
        $width = 400;
        $align = 'left';
        $theme = 'dark';
        $lang = 'es';
        $options = array(
            'id'           => self::VALID_TWEET_ID,
            'width'        => $width,
            'cards'        => false,
            'conversation' => false,
            'align'        => $align,
            'theme'        => $theme,
            'lang'         => $lang,
        );

        $tweet = \Twitter\Widgets\Embeds\Tweet::fromArray($options);
        $this->assertNotNull($tweet, 'Tweet object creation from array failed');
        $this->assertEquals($width, self::getProperty($tweet, 'width'));
        $this->assertFalse(self::getProperty($tweet, 'cards'));
        $this->assertFalse(self::getProperty($tweet, 'conversation'));
        $this->assertEquals($align, self::getProperty($tweet, 'align'));
        $this->assertEquals($theme, self::getProperty($tweet, 'theme'));
        $this->assertEquals($lang, self::getProperty($tweet, 'lang'));
    }

    /**
     * Test converting Tweet object into an associative array with default values stripped from the response
     *
     * @since 2.0.0
     *
     * @covers ::toArray
     *
     * @return void
     */
    public function testToArray()
    {
        $lang = 'es';
        $width = 400;
        $tweet = new \Twitter\Widgets\Embeds\Tweet(self::VALID_TWEET_ID);
        self::setProperty($tweet, 'width', $width);
        self::setProperty($tweet, 'cards', false);
        self::setProperty($tweet, 'conversation', false);
        self::setProperty($tweet, 'align', \Twitter\Widgets\Embeds\Tweet::ALIGN_LEFT);
        self::setProperty($tweet, 'theme', \Twitter\Widgets\Embeds\Tweet::$THEME_DARK);
        self::setProperty($tweet, 'lang', $lang);

        $data = $tweet->toArray();
        $this->assertArrayHasKey('width', $data, 'Width not returned');
        $this->assertEquals($width, $data['width'], 'Width not as set');
        $this->assertArrayHasKey('cards', $data, 'Cards override not returned');
        $this->assertEquals('false', $data['cards'], 'Failed to return false string for cards override');
        $this->assertArrayHasKey('conversation', $data, 'Parent Tweet override not returned');
        $this->assertEquals('false', $data['conversation'], 'Failed to return false string for conversation override');
        $this->assertArrayHasKey('align', $data, 'Align value not returned');
        $this->assertEquals(\Twitter\Widgets\Embeds\Tweet::ALIGN_LEFT, $data['align'], 'Align value not returned as expected');
        $this->assertArrayHasKey('theme', $data, 'Theme value not returned');
        $this->assertEquals(\Twitter\Widgets\Embeds\Tweet::$THEME_DARK, $data['theme'], 'Dark theme not returned');
        $this->assertArrayHasKey('lang', $data, 'Explicit language not returned');
        $this->assertEquals($lang, $data['lang'], 'Did not return language override');
    }

    /**
     * Test returning an oEmbed query parameter array
     *
     * @since 2.0.0
     *
     * @covers ::toOEmbedParameterArray
     *
     * @return void
     */
    public function testToOEmbedParameterArray()
    {
        $lang = 'es';
        $width = 400;
        $tweet = new \Twitter\Widgets\Embeds\Tweet(self::VALID_TWEET_ID);
        self::setProperty($tweet, 'width', $width);
        self::setProperty($tweet, 'cards', false);
        self::setProperty($tweet, 'conversation', false);
        self::setProperty($tweet, 'align', \Twitter\Widgets\Embeds\Tweet::ALIGN_LEFT);
        self::setProperty($tweet, 'theme', \Twitter\Widgets\Embeds\Tweet::$THEME_DARK);
        self::setProperty($tweet, 'lang', $lang);

        $parameters = $tweet->toOEmbedParameterArray();
        $this->assertArrayHasKey('maxwidth', $parameters, 'Width not returned');
        $this->assertEquals($width, $parameters['maxwidth'], 'Width not as set');
        $this->assertArrayHasKey('hide_media', $parameters, 'Cards override not returned');
        $this->assertFalse($parameters['hide_media'], 'Failed to return false bool for cards override');
        $this->assertArrayHasKey('hide_thread', $parameters, 'Parent Tweet override not returned');
        $this->assertFalse($parameters['hide_thread'], 'Failed to return false bool for conversation override');
        $this->assertArrayHasKey('align', $parameters, 'Align value not returned');
        $this->assertEquals(\Twitter\Widgets\Embeds\Tweet::ALIGN_LEFT, $parameters['align'], 'Align value not returned as expected');
        $this->assertArrayHasKey('theme', $parameters, 'Theme value not returned');
        $this->assertEquals(\Twitter\Widgets\Embeds\Tweet::$THEME_DARK, $parameters['theme'], 'Dark theme not returned');
        $this->assertArrayHasKey('lang', $parameters, 'Explicit language not returned');
        $this->assertEquals($lang, $parameters['lang'], 'Did not return language override');
    }
}
