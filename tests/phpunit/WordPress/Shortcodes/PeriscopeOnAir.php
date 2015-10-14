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

namespace Twitter\Tests\WordPress\Shortcodes;

/**
 * @group shortcode
 * @coversDefaultClass \Twitter\WordPress\Shortcodes\PeriscopeOnAir
 */
final class PeriscopeOnAir extends \WP_UnitTestCase {

	/**
	 * Test extracting a Periscope username from a shortcode through username attribute
	 *
	 * @since 1.3.0
	 *
	 * @covers ::sanitizeShortcodeParameters
	 * @small
	 *
	 * @dataProvider usernameProvider
	 *
	 * @param string $username Twitter username test value
	 *
	 * @return void
	 */
	public function testSanitizeShortcodeParametersScreenName( $username ) {
		$expected = 'twitter';

		$options = \Twitter\WordPress\Shortcodes\PeriscopeOnAir::sanitizeShortcodeParameters( array( 'username' => $username ) );
		$this->assertTrue( ( isset( $options['username'] ) && $options['username'] === $expected ), 'Failed to extract screen name from attribute' );
	}

	/**
	 * Periscope username provider
	 *
	 * @since 1.3.0
	 *
	 * @return array array of arrays of Periscope usernames
	 */
	public static function usernameProvider() {
		return array(
			array( 'twitter' ),
			array( '@twitter' ),
			array( ' twitter ' ),
		);
	}
}
