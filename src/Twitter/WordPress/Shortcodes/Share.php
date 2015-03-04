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

namespace Twitter\WordPress\Shortcodes;

/**
 * Display a Tweet Web Intent and queue JavaScript for conversion to a Tweet button
 *
 * @since 1.0.0
 */
class Share
{

	/**
	 * Shortcode tag to be matched
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_TAG = 'twitter_share';

	/**
	 * Accepted shortcode attributes and their default values
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	public static $SHORTCODE_DEFAULTS = array( 'in_reply_to' => '', 'text' => '', 'url' => '', 'hashtags' => array(), 'via' => '', 'related' => array(), 'size' => '', 'align' => '', 'count' => '', 'counturl' => '' );

	/**
	 * Attach handlers for Tweet button
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		add_shortcode( static::SHORTCODE_TAG, array( __CLASS__, 'shortcodeHandler' ) );
	}

	/**
	 * Get any Tweet values stored for an individual post
	 *
	 * @since 1.0.0
	 *
	 * @return array post meta Tweet values or empty array if no values stored
	 */
	public static function getPostMeta()
	{
		$post = get_post();

		if ( ! ( $post && isset( $post->ID ) ) ) {
			return array();
		}

		$post_values = get_post_meta(
			$post->ID,
			\Twitter\WordPress\Admin\Post\TweetIntent::META_KEY,
			true // single value
		);
		if ( ! is_array( $post_values ) ) {
			return array();
		}
		return $post_values;
	}

	/**
	 * Add post meta values to a Tweet button options array
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Tweet button options array {
	 *   @type string option name
	 *   @type string|array option value
	 * }
	 *
	 * @return array Tweet button options array {
	 *   @type string option name
	 *   @type string|array option value
	 * }
	 */
	protected static function addPostMetaOptions( $options )
	{
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		$post_meta = static::getPostMeta();
		if ( empty( $post_meta ) ) {
			return $options;
		}

		// allow shortcode text to override post text
		// example: multiple Tweet buttons in a post
		if ( ! ( isset( $options['text'] ) && trim( $options['text'] ) ) ) {
			if ( isset( $post_meta['text'] ) ) {
				$text = trim( $post_meta['text'] );
				if ( $text ) {
					$options['text'] = $text;
				}
				unset( $text );
			}
		}

		// allow shortcode hashtags to override post hashtags
		// example: multiple Tweet buttons in a post
		if ( ! isset( $options['hashtags'] ) || empty( $options['hashtags'] ) ) {
			if ( isset( $post_meta['hashtags'] ) && is_array( $post_meta['hashtags'] ) && ! empty( $post_meta['hashtags'] ) ) {
				$options['hashtags'] = $post_meta['hashtags'];
			}
		}

		return $options;
	}

	/**
	 * Convert shortcode parameters, attributes, and defaults into a clean set of Tweet parameters
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes set of shortcode attribute-value pairs or positional content matching the WordPress shortcode regex {
	 *   @type string|int attribute name or positional int
	 *   @type mixed shortcode value
	 * }
	 *
	 * @return array cleaned up options ready for comparison {
	 *   @type string option name
	 *   @type string|bool option value
	 * }
	 */
	public static function sanitizeShortcodeParameters( $attributes = array() )
	{
		if ( ! is_array( $attributes ) ) {
			return array();
		}

		$options = array();

		if ( isset( $attributes['in_reply_to'] ) ) {
			$tweet_id = \Twitter\WordPress\Shortcodes\EmbeddedTweet::sanitizeTweetID( (string) $attributes['in_reply_to'] );
			if ( $tweet_id ) {
				$options['in_reply_to'] = $tweet_id;
			}
			unset( $tweet_id );
		}

		if ( isset( $attributes['text'] ) && is_string( $attributes['text'] ) ) {
			$options['text'] = $attributes['text'];
		}

		foreach ( array( 'url', 'counturl' ) as $url_param ) {
			if ( ! ( isset( $attributes[ $url_param ] ) && $attributes[ $url_param ] ) ) {
				continue;
			}

			// filter the URL
			$url = esc_url_raw( trim( $attributes[ $url_param ] ), array( 'http', 'https' ) );
			if ( $url ) {
				$options[ $url_param ] = $url;
			}
			unset( $url );
		}

		if ( isset( $attributes['related'] ) ) {
			$intent = \Twitter\Intents\Tweet::fromArray( array( 'related' => $attributes['related'] ) );
			if ( $intent ) {
				$related = $intent->getRelated();
				if ( ! empty( $related ) ) {
					$options['related'] = $related;
				}
				unset( $related );
			}
			unset( $intent );
		}

		if ( isset( $attributes['via'] ) ) {
			$via = (new \Twitter\Intents\Tweet())->setVia( $attributes['via'] )->getVia();
			if ( $via ) {
				$options['via'] = $via;
			}
			unset( $via );
		}

		if ( isset( $attributes['hashtags'] ) ) {
			$intent = \Twitter\Intents\Tweet::fromArray( array( 'hashtags' => $attributes['hashtags'] ) );
			if ( $intent ) {
				$hashtags = $intent->getHashtags();
				if ( ! empty( $hashtags ) ) {
					$options['hashtags'] = $hashtags;
				}
				unset( $hashtags );
			}
			unset( $intent );
		}

		if ( isset( $attributes['align'] ) && is_string( $attributes['align'] ) && $attributes['align'] ) {
			$align = strtolower( trim( $attributes['align'] ) );
			if ( array_key_exists( $align, \Twitter\Widgets\TweetButton::$ALLOWED_ALIGN_VALUES ) ) {
				$options['align'] = $align;
			}
			unset( $align );
		}

		if ( isset( $attributes['count'] ) && is_string( $attributes['count'] ) ) {
			$count = strtolower( trim( $attributes['count'] ) );
			if ( array_key_exists( $count, \Twitter\Widgets\TweetButton::$ALLOWED_COUNT_VALUES ) ) {
				$options['count'] = $count;
			}
			unset( $count );
		}

		// large is the only option
		if ( isset( $attributes['size'] ) ) {
			if ( is_string( $attributes['size'] ) && in_array( strtolower( $attributes['size'] ), array( 'large', 'l' ) ) ) {
				$options['size'] = 'large';
			}
		}

		return $options;
	}

	/**
	 * Add explicit Tweet button data related to the post and its author
	 *
	 * @since 1.0.0
	 *
	 * @param array   $options Tweet button options {
	 *   @type string      option name
	 *   @type string|bool option value
	 * }
	 * @param WP_Post $post    post of interest
	 *
	 * @return array Tweet button options with possible additions based on post data {
	 *   @type string      option name
	 *   @type string|bool option value
	 * }
	 */
	protected static function addPostData( $options, $post )
	{
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		// explicitly define post URL
		// maintains Tweet button context on a page listing multiple posts
		if ( ! ( isset( $options['url'] ) && $options['url'] ) ) {
			/**
			 * Filter the URL shared in Tweet text
			 *
			 * All URLs are wrapped in Twitter's t.co link wrapper
			 *
			 * @since 1.0.0
			 *
			 * @param string $url The URL returned by get_permalink() when in the loop
			 */
			$url = apply_filters( 'twitter_url', get_permalink( $post ) );
			if ( $url ) {
				$options['url'] = $url;
			}
			unset( $url );
		}

		$author_id = get_the_author_meta( 'ID' );
		if ( $author_id ) {
			$author_twitter_username = \Twitter\WordPress\User\Meta::getTwitterUsername( $author_id );
			if ( $author_twitter_username ) {
				$author_display_name = trim( get_the_author_meta( 'display_name', $author_id ) );
				if ( ! isset( $options['related'] ) || ! is_array( $options['related'] ) ) {
					$options['related'] = array();
				}
				if ( ! isset( $options['related'][ $author_twitter_username ] ) ) {
					$options['related'][ $author_twitter_username ] = $author_display_name;
				}
				unset( $author_display_name );
			}
			unset( $author_twitter_username );
		}
		unset( $author_id );

		return $options;
	}

	/**
	 * Handle shortcode macro
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attributes shortcode attributes
	 * @param string $content    shortcode content. no effect
	 *
	 * @return string Tweet button HTML or empty string
	 */
	public static function shortcodeHandler( $attributes, $content = null )
	{
		// clean up attribute to shortcode option mappings before passing to filter
		// apply the same filter as shortcode_atts
		/** This filter is documented in wp-includes/shortcodes.php */
		$options = apply_filters(
			'shortcode_atts_' . self::SHORTCODE_TAG,
			array_merge(
				static::$SHORTCODE_DEFAULTS,
				static::sanitizeShortcodeParameters( (array) $attributes )
			),
			static::$SHORTCODE_DEFAULTS,
			$attributes
		);

		// add options shared to post meta
		$options = static::addPostMetaOptions( $options );

		//  add parameters based on per-post render context
		if ( in_the_loop() ) {
			$post = get_post();

			// do not share posts requiring a password to access
			if ( $post && ! empty( $post->post_password ) ) {
				return '';
			}

			// protect sites from themselves
			// do not display Tweet button for non-public content to avoid leaking content
			$post_status_object = get_post_status_object( get_post_status( $post ) );
			if ( ! ( $post_status_object && isset( $post_status_object->public ) && $post_status_object->public ) ) {
				return '';
			}
			unset( $post_status_object );

			// add parameters based on post data
			$options = static::addPostData( $options, $post );

			unset( $post );
		}
		if ( ! ( isset( $options['via'] ) && $options['via'] ) ) {
			// attribute the Tweet to the site Twitter username
			$via_username = \Twitter\WordPress\Site\Username::getViaAttribution( ( in_the_loop() ? get_the_ID() : null ) );
			if ( $via_username ) {
				$options['via'] = $via_username;
			}
			unset( $via_username );
		}

		$button = \Twitter\Widgets\TweetButton::fromArray( $options );
		if ( ! $button ) {
			return '';
		}

		$html = $button->toHTML( _x( 'Tweet', 'Tweet verb. Sharing.', 'twitter' ), '\Twitter\WordPress\Helpers\HTMLBuilder' );
		if ( ! $html ) {
			return '';
		}

		\Twitter\WordPress\JavaScriptLoaders\Widgets::enqueue();
		return '<div class="twitter-share">' . $html . '</div>';
	}
}
