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
 * @coversDefaultClass \Twitter\WordPress\Shortcodes\EmbeddedTweet
 */
final class EmbeddedTweet extends \WP_UnitTestCase {
	/**
	 * Test handling a regex match for a Tweet URL
	 *
	 * @since 1.0.0
	 *
	 * @covers ::linkHandler
	 * @small
	 *
	 * @dataProvider linkRegexResultMismatchProvider
	 *
	 * @param array $matches test value
	 *
	 * @return void
	 */
	public function testLinkHandlerMismatch( $matches ) {
		$this->assertEquals(
			'',
			\Twitter\WordPress\Shortcodes\EmbeddedTweet::linkHandler( $matches, array(), '', array() ),
			'Failed to reject a bad regex match response'
		);
	}

	/**
	 * Provide regex matches values which should all fail to extract a Tweet ID
	 *
	 * @since 1.0.0
	 *
	 * @return array array of test values
	 */
	public static function linkRegexResultMismatchProvider() {
		return array(
			array( false ),
			array( '' ),
			array( array() ),
			array( array( '', '', '', '' ) ),
		);
	}

	/**
	 * Test sanitizing a provided Tweet ID
	 *
	 * @since 1.0.0
	 *
	 * @covers ::sanitizeTweetID
	 * @small
	 *
	 * @dataProvider sanitizeTweetIDProvider
	 *
	 * @param string $tweet_id Tweet ID test value
	 *
	 * @return void
	 */
	public function testSanitizeTweetID( $tweet_id ) {
		$this->assertEquals( '20', \Twitter\WordPress\Shortcodes\EmbeddedTweet::sanitizeTweetID( $tweet_id ), 'Failed to clean up valid Tweet input' );
	}

	/**
	 * Test extracting a Tweet ID from a shortcode through id attribute or positional
	 *
	 * @since 1.0.0
	 *
	 * @covers ::sanitizeShortcodeParameters
	 * @small
	 *
	 * @dataProvider sanitizeTweetIDProvider
	 *
	 * @param string $tweet_id Tweet ID test value
	 *
	 * @return void
	 */
	public function testSanitizeShortcodeParametersID( $tweet_id ) {
		$expected = '20';

		// test setting by id attribute
		$options = \Twitter\WordPress\Shortcodes\EmbeddedTweet::sanitizeShortcodeParameters( array( 'id' => $tweet_id ) );
		$this->assertTrue( ( isset( $options['id'] ) && $options['id'] === $expected ), 'Failed to extract ID from attribute' );

		$options = \Twitter\WordPress\Shortcodes\EmbeddedTweet::sanitizeShortcodeParameters( array( $tweet_id ) );
		$this->assertTrue( ( isset( $options['id'] ) && $options['id'] === $expected ), 'Failed to extract ID from positional' );
	}

	/**
	 * Provide Tweet values which should all evaluate to a Tweet ID of 20
	 *
	 * @since 1.0.0
	 *
	 * @return array array of test values
	 */
	public static function sanitizeTweetIDProvider() {
		return array(
			array( 'https://twitter.com/jack/status/20' ),
			array( 'https://twitter.com/jack/statuses/20' ),
			array( '20' ),
			array( ' 20 ' ),
		);
	}

	/**
	 * Test hiding cards / photos / videos through a shortcode parameter
	 *
	 * @since 1.0.0
	 *
	 * @covers ::sanitizeShortcodeParameters
	 * @small
	 *
	 * @dataProvider hideCardsProvider
	 *
	 * @param array $attributes shortcode attributes {
	 *   @type string attribute
	 *   @type mixed value
	 * }
	 *
	 * @return void
	 */
	public function testSanitizeShortcodeParametersCards( $attributes )  {
		$options = \Twitter\WordPress\Shortcodes\EmbeddedTweet::sanitizeShortcodeParameters( $attributes );
		$this->assertTrue( ( isset( $options['cards'] ) && false === $options['cards'] ), 'Failed to set a cards value of false' );
	}

	/**
	 * Shortcode attributes which should all trigger a cards option equal to false
	 *
	 * @since 1.0.0
	 *
	 * @return array array of test values
	 */
	public static function hideCardsProvider() {
		return array(
			array( array( 'cards' => false ) ),
			array( array( 'cards' => 'false' ) ),
			array( array( 'cards' => 'FALSE' ) ),
			array( array( 'cards' => 0 ) ),
			array( array( 'cards' => '0' ) ),
			array( array( 'cards' => 'no' ) ),
			array( array( 'cards' => 'NO' ) ),
			array( array( 'cards' => 'off' ) ),
			array( array( 'cards' => 'OFF' ) ),
			array( array( 'hide_media' => true ) ),
			array( array( 'hide_media' => 'true' ) ),
			array( array( 'hide_media' => 'TRUE' ) ),
			array( array( 'hide_media' => 1 ) ),
			array( array( 'hide_media' => '1' ) ),
			array( array( 'hide_media' => 'yes' ) ),
			array( array( 'hide_media' => 'YES' ) ),
			array( array( 'hide_media' => 'on' ) ),
			array( array( 'hide_media' => 'ON' ) ),
			array( array( 'cards' => false, 'hide_media' => false ) ),
		);
	}

	/**
	 * Test hiding an in_reply_to Tweet through a shortcode parameter
	 *
	 * @since 1.0.0
	 *
	 * @covers ::sanitizeShortcodeParameters
	 * @small
	 *
	 * @dataProvider hideConversationProvider
	 *
	 * @param array $attributes shortcode attributes {
	 *   @type string attribute
	 *   @type mixed value
	 * }
	 *
	 * @return void
	 */
	public function testSanitizeShortcodeParametersConversation( $attributes )  {
		$options = \Twitter\WordPress\Shortcodes\EmbeddedTweet::sanitizeShortcodeParameters( $attributes );
		$this->assertTrue( ( isset( $options['conversation'] ) && false === $options['conversation'] ), 'Failed to set a conversation value of false' );
	}

	/**
	 * Shortcode attributes which should all trigger a conversation option equal to false
	 *
	 * @since 1.0.0
	 *
	 * @return array array of test values
	 */
	public static function hideConversationProvider() {
		return array(
			array( array( 'conversation' => false ) ),
			array( array( 'conversation' => 'false' ) ),
			array( array( 'conversation' => 'FALSE' ) ),
			array( array( 'conversation' => 0 ) ),
			array( array( 'conversation' => '0' ) ),
			array( array( 'conversation' => 'no' ) ),
			array( array( 'conversation' => 'NO' ) ),
			array( array( 'conversation' => 'off' ) ),
			array( array( 'conversation' => 'OFF' ) ),
			array( array( 'hide_thread' => true ) ),
			array( array( 'hide_thread' => 'true' ) ),
			array( array( 'hide_thread' => 'TRUE' ) ),
			array( array( 'hide_thread' => 1 ) ),
			array( array( 'hide_thread' => '1' ) ),
			array( array( 'hide_thread' => 'yes' ) ),
			array( array( 'hide_thread' => 'YES' ) ),
			array( array( 'hide_thread' => 'on' ) ),
			array( array( 'hide_thread' => 'ON' ) ),
			array( array( 'conversation' => false, 'hide_thread' => false ) ),
		);
	}

	/**
	 * Test setting a shortcode align value
	 *
	 * @since 1.0.0
	 *
	 * @covers ::sanitizeShortcodeParameters
	 * @small
	 *
	 * @dataProvider alignProvider
	 *
	 * @param string $align_value align shortcode parameter to test
	 * @param string $expected    expected sanitized result
	 * @param string $message     message to display on error
	 *
	 * @return void
	 */
	public function testSanitizeShortcodeParametersAlign( $align_value, $expected, $message = '' ) {
		$options = \Twitter\WordPress\Shortcodes\EmbeddedTweet::sanitizeShortcodeParameters( array( 'align' => $align_value ) );
		$this->assertEquals(
			$expected,
			$options['align'],
			$message
		);
	}

	/**
	 * Shortcode align values and their expected saved values
	 *
	 * @since 1.0.0
	 *
	 * @return array array of test values
	 */
	public static function alignProvider() {
		return array(
			array( 'left', 'left', 'Failed to accept a left align value' ),
			array( 'LEFT', 'left', 'Failed to accept an ALL CAPS align value' ),
			array( ' left ', 'left', 'Failed to trim spaces from align value' ),
			array( 'center', 'center', 'Failed to accept a center align value' ),
			array( 'right', 'right', 'Failed to accept a right align value' ),
		);
	}

	/**
	 * Test setting a combination of default and invalid shortcode values which should result in no saved options
	 *
	 * @since 1.0.0
	 *
	 * @covers ::sanitizeShortcodeParameters
	 * @small
	 *
	 * @return void
	 */
	public function testSanitizeShortcodeParametersNoEffect() {
		$this->assertEmpty(
			\Twitter\WordPress\Shortcodes\EmbeddedTweet::sanitizeShortcodeParameters( array(
				'id' => ' ',
				'align' => 'top',
				'cards' => true,
				'hide_media' => false,
				'conversation' => true,
				'hide_thread' => false,
			) ),
			'Failed to reject parameters which should have no effect on sanitized options'
		);
	}

	/**
	 * Test building base oEmbed query parameters
	 *
	 * @since 1.0.0
	 *
	 * @covers ::getBaseOEmbedParams
	 * @small
	 *
	 * @return void
	 */
	public function testGetBaseOEmbedParams() {
		$this->assertEmpty( \Twitter\WordPress\Shortcodes\EmbeddedTweet::getBaseOEmbedParams( '' ), 'Failed to reject an empty passed Tweet ID' );
	}

	/**
	 * Test building a unique string for shortcode parameters
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
			'mtr',
			\Twitter\WordPress\Shortcodes\EmbeddedTweet::getOEmbedCacheKeyCustomParameters( array(
				'hide_media'  => true,
				'hide_thread' => true,
				'align'       => 'right',
			) ),
			'Failed to build the expected cache key component from shortcode customizations'
		);
	}

	/**
	 * Test building a unique cache key for requested query parameters
	 *
	 * @since 1.0.0
	 *
	 * @covers ::oEmbedCacheKey
	 * @small
	 *
	 * @return void
	 */
	public function testOEmbedCacheKey() {
		$this->assertEquals(
			'tweet_20',
			\Twitter\WordPress\Shortcodes\EmbeddedTweet::oEmbedCacheKey( array(
				'id' => '20',
			) ),
			'Unexpected cache key'
		);
	}
}
