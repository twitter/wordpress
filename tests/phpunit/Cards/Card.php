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
 * @coversDefaultClass \Twitter\Cards\Card
 */
final class Card extends \Twitter\Tests\TestWithPrivateAccess
{
    /**
     * Set up a card object for each test
     *
     * @since 1.0.0
     *
     * @type \Twitter\Cards\Card
     */
    protected $card;

    /**
     * Initialize a new Card object for use in each test
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->card = new \Twitter\Cards\Card('summary');
    }

    /**
     * Test cleaning up the title before storing
     *
     * @since 1.0.0
     *
     * @covers ::sanitizeTitle
     * @small
     *
     * @dataProvider sanitizeTitleProvider
     *
     * @param string $title passed title
     * @param string $expected_sanitized expected result of sanitization
     * @param string $message error message to display on negative assertion
     *
     * @return void
     */
    public function testSanitizeTitle($title, $expected_sanitized, $message = '')
    {
        $this->assertEquals(
            $expected_sanitized,
            \Twitter\Cards\Card::sanitizeTitle($title),
            $message
        );
    }

    /**
     * Provide titles for testing by the sanitizer
     *
     * @since 1.0.0
     *
     * @return array title values {
     *   @type array provided title, expected sanitized value, error message
     * }
     */
    public static function sanitizeTitleProvider()
    {
        return array(
            array( 'Hello world', 'Hello world', 'Failed to sanitize basic title' ),
            array( ' Hello world ', 'Hello world', 'Failed to trim whitespace' ),
        );
    }

    /**
     * Test setting a Twitter Card title
     *
     * @since 1.0.0
     *
     * @covers ::setTitle
     * @small
     *
     * @return void
     */
    public function testSetTitle()
    {
        $title = 'Hello world';
        $this->card->setTitle($title);
        $this->assertEquals($title, self::getProperty($this->card, 'title'), 'Failed to set title');
    }

    /**
     * Test setting a site
     *
     * @since 1.0.0
     *
     * @covers ::setSite
     * @small
     *
     * @uses \Twitter\Cards\Components\Account
     *
     * @return void
     */
    public function testSetSite()
    {
        $this->assertNotNull(\Twitter\Cards\Components\Account::fromScreenName('twitter'), 'Failed to set site');
    }
}
