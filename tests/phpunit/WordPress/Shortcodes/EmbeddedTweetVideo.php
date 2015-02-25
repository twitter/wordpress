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
 * @coversDefaultClass \Twitter\WordPress\Shortcodes\EmbeddedTweetVideo
 */
final class EmbeddedTweetVideo extends \PHPUnit_Framework_TestCase {

	/**
	 * Test extracting a Tweet ID from a shortcode through id attribute
	 *
	 * @since 1.0.0
	 *
	 * @covers ::sanitizeShortcodeParameters
	 * @small
	 *
	 * @dataProvider tweetIDProvider
	 *
	 * @param string $tweet_id Tweet ID test value
	 *
	 * @return void
	 */
	public function testSanitizeShortcodeParametersID( $tweet_id ) {
		$expected = '560070183650213889';

		$options = \Twitter\WordPress\Shortcodes\EmbeddedTweetVideo::sanitizeShortcodeParameters( array( 'id' => $tweet_id ) );
		$this->assertTrue( ( isset( $options['id'] ) && $options['id'] === $expected ), 'Failed to extract ID from attribute' );
	}

	/**
	 * Provide Tweet values which should all evaluate to a Tweet ID of 20
	 *
	 * @since 1.0.0
	 *
	 * @return array array of test values
	 */
	public static function tweetIDProvider() {
		return array(
			array( 'https://twitter.com/twitter/status/560070183650213889' ),
			array( 'https://twitter.com/twitter/statuses/560070183650213889' ),
			array( '560070183650213889' ),
			array( ' 560070183650213889 ' ),
		);
	}

	/**
	 * Test disabling a Tweet Video status overlay through a shortcode option
	 *
	 * @since 1.0.0
	 *
	 * @covers ::sanitizeShortcodeParameters
	 * @small
	 *
	 * @dataProvider hideStatusProvider
	 *
	 * @param array $attributes shortcode attributes {
	 *   @type string attribute
	 *   @type mixed value
	 * }
	 *
	 * @return void
	 */
	public function testSanitizeShortCodeParametersStatus( $attributes ) {
		$options = \Twitter\WordPress\Shortcodes\EmbeddedTweetVideo::sanitizeShortcodeParameters( $attributes );
		$this->assertTrue( ( isset( $options['status'] ) && false === $options['status'] ), 'Failed to set a status value of false' );
	}

	/**
	 * Shortcode attributes which should all trigger a status option equal to false
	 *
	 * @since 1.0.0
	 *
	 * @return array array of test values
	 */
	public static function hideStatusProvider() {
		return array(
			array( array( 'status' => false ) ),
			array( array( 'status' => 'false' ) ),
			array( array( 'status' => 'FALSE' ) ),
			array( array( 'status' => 0 ) ),
			array( array( 'status' => '0' ) ),
			array( array( 'status' => 'no' ) ),
			array( array( 'status' => 'NO' ) ),
			array( array( 'status' => 'off' ) ),
			array( array( 'status' => 'OFF' ) ),
			array( array( 'hide_tweet' => true ) ),
			array( array( 'hide_tweet' => 'true' ) ),
			array( array( 'hide_tweet' => 'TRUE' ) ),
			array( array( 'hide_tweet' => 1 ) ),
			array( array( 'hide_tweet' => '1' ) ),
			array( array( 'hide_tweet' => 'yes' ) ),
			array( array( 'hide_tweet' => 'YES' ) ),
			array( array( 'hide_tweet' => 'on' ) ),
			array( array( 'hide_tweet' => 'ON' ) ),
			array( array( 'status' => false, 'hide_tweet' => false ) ),
		);
	}

	/**
	 * Test building a unique cache key component for shortcode customizations
	 *
	 * @since 1.0.0
	 *
	 * @covers ::getOEmbedCacheKeyCustomParameters
	 * @small
	 *
	 * @return void
	 */
	public function testGetOEmbedCacheKeyCustomParameters() {
		$this->assertEquals(
			'h',
			\Twitter\WordPress\Shortcodes\EmbeddedTweetVideo::getOEmbedCacheKeyCustomParameters( array( 'hide_tweet' => true ) ),
			'Failed to set a unique cache key for the hide Tweet customization'
		);
	}
}
