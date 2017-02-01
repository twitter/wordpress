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

namespace Twitter\Tests\Helpers\Validators;

/**
 * @coversDefaultClass \Twitter\Helpers\Validators\WebsiteTag
 */
final class WebsiteTag extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test validity of a website tag
	 *
	 * @since 2.0.0
	 *
	 * @covers ::isValid
	 * @small
	 *
	 * @dataProvider websiteTagProvider
	 *
	 * @param string $test_value website tag
	 * @param bool   $expected_valid expected validity of the provided test value
	 * @param string $message (optional) message to display on test failure
	 *
	 * @return void
	 */
	public function testIsValid( $test_value, $expected_valid, $message = '')
	{
		$is_valid = \Twitter\Helpers\Validators\WebsiteTag::isValid($test_value);
		if ( $expected_valid ) {
			$this->assertTrue( $is_valid, $message );
		} else {
			$this->assertFalse( $is_valid, $message );
		}
	}

	/**
	 * Test cleaning a provided website tag
	 *
	 * @since 2.0.0
	 *
	 * @covers ::sanitize
	 * @small
	 *
	 * @dataProvider websiteTagProvider
	 *
	 * @param string $test_value     website tag
	 * @param bool   $expected_valid expected validity of the provided test value
	 * @param string $message        (optional) message to display on test failure
	 *
	 * @return void
	 */
	public function testSanitize( $test_value, $expected_valid, $message = '' )
	{
		$clean_value = \Twitter\Helpers\Validators\WebsiteTag::sanitize($test_value);
		if ( $expected_valid ) {
			$this->assertEquals( strtolower($clean_value), $clean_value, $message );
		} else {
			$this->assertEquals( '', $clean_value, $message );
		}
	}

	/**
	 * Provide possible website tag values and expected validity
	 *
	 * @since 2.0.0
	 *
	 * @return array website tag value, 
	 */
	public static function websiteTagProvider()
	{
		return array(
			array( 'a1b2c',  true,  'Failed to accept simple website tag' ),
			array( 'A1B2C',  true,  'Failed to accept simple website tag with uppercase alpha characters' ),
			array( 'a1b2c3', false, 'Accepted a value beyond the maximum allowed length of a website tag' ),
			array( 'a1&b2',  false, 'Accepted a website tag with an ampersand' ),
		);
	}
}
