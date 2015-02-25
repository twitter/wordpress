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
 * @coversDefaultClass \Twitter\Cards\Components\Account
 */
final class Account extends \Twitter\Tests\TestWithPrivateAccess
{

    /**
     * Screen name used in tests
     *
     * @since 1.0.0
     *
     * @type string
     */
    const SCREEN_NAME = 'twitter';

    /**
     * ID used in tests
     *
     * @since 1.0.0
     *
     * @type string
     */
    const ID = '20';

    /**
     * Set up an account object for each test
     *
     * @since 1.0.0
     *
     * @type \Twitter\Cards\Components\Account
     */
    protected $account;

    /**
     * Initialize a new Account object for use in each test
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->account = new \Twitter\Cards\Components\Account();
    }

    /**
     * Test setting screen name
     *
     * @since 1.0.0
     *
     * @covers ::setScreenName
     * @small
     *
     * @dataProvider screenNameProvider
     *
     * @param string $test_value
     * @param string $expected_result expected result after setting
     * @param string $message error message to display on negative assertion
     *
     * @return string set screen_name property
     */
    public function testSetScreenName($test_value, $expected_result, $message = '')
    {
        $this->account->setScreenName($test_value);

        $screen_name_property = self::getProperty($this->account, 'screen_name');

        $this->assertEquals($expected_result, $screen_name_property, $message);

        return $screen_name_property;
    }

    /**
     * Screen name test values
     *
     * @since 1.0.0
     *
     * @return array screen names {
     *   @type array test value, expected result, message
     * }
     */
    public static function screenNameProvider()
    {
        return array(
            array( self::SCREEN_NAME, self::SCREEN_NAME, 'Failed to set screen_name' ),
            array( '@' . self::SCREEN_NAME, self::SCREEN_NAME, 'Failed to trim leading @' ),
        );
    }

    /**
     * Test if the account object has a screen_name
     *
     * @since 1.0.0
     *
     * @covers ::hasScreenName
     * @small
     *
     * @return void
     */
    public function testHasScreenName()
    {
        $this->assertFalse($this->account->hasScreenName(), 'An empty object should have no screen_name');

        self::setProperty($this->account, 'screen_name', self::SCREEN_NAME);
        $this->assertTrue($this->account->hasScreenName());
    }

    /**
     * Test setting a Twitter account ID
     *
     * @since 1.0.0
     *
     * @covers ::setID
     * @small
     *
     * @return void
     */
    public function testSetID()
    {
        $this->account->setID(self::ID);
        $this->assertEquals(self::ID, self::getProperty($this->account, 'id'), 'unable to set account ID');
    }

    /**
     * Test if the account object has a stored ID
     *
     * @since 1.0.0
     *
     * @covers ::hasID
     * @small
     *
     * @return void
     */
    public function testHasID()
    {
        $this->assertFalse($this->account->hasID(), 'An empty object should have no id');

        self::setProperty($this->account, 'id', self::ID);
        $this->assertTrue($this->account->hasID());
    }

    /**
     * Test converting a Twitter account into Twitter Card representation
     *
     * @since 1.0.0
     *
     * @covers ::asCardProperties
     * @small
     *
     * @return void
     */
    public function testAsCardProperties()
    {
        self::setProperty($this->account, 'screen_name', self::SCREEN_NAME);
        $this->assertEquals('@'.self::SCREEN_NAME, $this->account->asCardProperties(), 'did not return a twitter account value');

        // an account with both screen_name and id set should output the same response as just id set
        self::setProperty($this->account, 'id', self::ID);
        $this->assertEquals(array('id'=>self::ID), $this->account->asCardProperties(), 'did not create a structured property containing an account ID');
    }

    /**
     * Test creating a new Account object by passing a screen_name to a static method
     *
     * @since 1.0.0
     *
     * @covers ::fromScreenName
     * @depends testSetScreenName
     * @small
     *
     * @return void
     */
    public function testFromScreenName()
    {
        $this->account = \Twitter\Cards\Components\Account::fromScreenName(self::SCREEN_NAME);
        $this->assertNotNull($this->account, 'could not create a new Account object from passed screen_name');
        $this->assertEquals(self::SCREEN_NAME, self::getProperty($this->account, 'screen_name'), 'Failed to create a new Account object from a passed screen_name');
    }

    /**
     * Test creating a new Account object by passing an ID to a static method
     *
     * @since 1.0.0
     *
     * @covers ::fromID
     * @depends testSetID
     * @small
     *
     * @param string $screen_name Twitter screen_name returned by testSetScreenName
     *
     * @return void
     */
    public function testFromID()
    {
        $this->account = \Twitter\Cards\Components\Account::fromID(self::ID);
        $this->assertNotNull($this->account, 'could not create a new Account object from passed id');
        $this->assertEquals(self::ID, self::getProperty($this->account, 'id'), 'Failed to create a new Account object from a passed id');
    }
}
