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

namespace Twitter\WordPress\Cards;

/**
 * Generate Twitter Card data based on the current WordPress context
 *
 * @since 1.0.0
 */
class Generator
{

	/**
	 * Card types supported by the Twitter plugin for WordPress
	 *
	 * Maps short names to Twitter Card builder class names
	 *
	 * @since 1.0.0
	 *
	 * @type array Twitter Card options {
	 *   @type string Twitter Card type
	 *   @type string Twitter Card class name
	 * }
	 */
	public static $SUPPORTED_CARDS = array(
		'summary'             => '\Twitter\Cards\Summary',
		'summary_large_image' => '\Twitter\Cards\SummaryLargeImage',
	);

	/**
	 * Is the passed card type a Twitter Card type supported by the plugin
	 *
	 * @since 1.0.0
	 *
	 * @param string $card_type Twitter Card type
	 *
	 * @return bool true if card type supported by plugin
	 */
	public static function isSupportedCardType( $card_type )
	{
		return ( is_string( $card_type ) && $card_type && isset( static::$SUPPORTED_CARDS[ $card_type ] ) );
	}

	/**
	 * Initialize a Twitter Card object for a given card type string
	 *
	 * @since 1.0.0
	 *
	 * @param string $card_type Twitter Card global type
	 *
	 * @return \Twitter\Cards\Card|null Twitter Card object
	 */
	public static function getCardForType( $card_type )
	{
		if ( static::isSupportedCardType( $card_type ) ) {
			return (new static::$SUPPORTED_CARDS[ $card_type ]);
		}

		return null;
	}

	/**
	 * Set up a card object for the query type
	 *
	 * Allows sites to override the Twitter Card type for multiple query types through a filter
	 * Set up card attributes common to all queries
	 *
	 * @param string|null     $query_type short name for current query type, similar to WP_Query flags
	 * @param int|string|null $object_id  current object identifier: post ID, author ID, etc. depending on query type
	 * @param string          $card_type  default Twitter Card type for the query
	 *
	 * @return \Twitter\Cards\Card|null Twitter Card object or null if minimum requirements not met
	 */
	public static function getCardObject( $query_type = null, $object_id = null, $card_type = 'summary' )
	{
		if ( ! static::isSupportedCardType( $card_type ) ) {
			return null;
		}
		if ( ! ( is_string( $query_type ) && $query_type ) ) {
			$query_type = null;
		}

		/**
		 * Filter the Twitter Card template to be applied for the given query type and object
		 *
		 * @since 1.0.0
		 *
		 * @link https://dev.twitter.com/cards/types Twitter Card types
		 *
		 * @param string           $card_type  Twitter Card type
		 * @param string|null      $query_type current rendering context. similar to WP_Query flags (home, archive, author, post)
		 * @param int|string|null  $object_id  current object identifier: post ID, author ID, etc. depending on query type
		 */
		$card_type = apply_filters( 'twitter_card_type', $card_type, $query_type, $object_id );

		$card = static::getCardForType( $card_type );
		if ( ! ( $card && is_a( $card, '\Twitter\Cards\Card' ) ) ) {
			return null;
		}

		return static::addSiteAttribution( $card, ( ('post' === $query_type) ? $object_id : null ) );
	}

	/**
	 * Add site attibution to the passed card if a Twitter username is stored for the current site
	 *
	 * @since 1.0.0
	 *
	 * @param \Twitter\Cards\Card $card    Twitter Card object
	 * @param int|string          $post_id WP_Post->ID or proprietary post identifier
	 *
	 * @return \Twitter\Cards\Card|null Twitter Card object or null if passed card object is invalid
	 */
	public static function addSiteAttribution( $card, $post_id = null )
	{
		if ( ! is_a( $card, '\Twitter\Cards\Card' ) ) {
			return null;
		}
		if ( ! method_exists( $card, 'setSite' ) ) {
			return $card;
		}

		$site_username = \Twitter\WordPress\Site\Username::getSiteAttribution( $post_id );
		if ( $site_username ) {
			$card->setSite( \Twitter\Cards\Components\Account::fromScreenName( $site_username ) );
		}

		return $card;
	}

	/**
	 * Output Twitter Card markup for the current display context
	 *
	 * @since 1.0.0
	 *
	 * @return \Twitter\Cards\Card|null Twitter Card object or null if no card available or condition not met for query type
	 */
	public static function get()
	{
		if ( is_home() || is_front_page() ) {
			return static::buildHomepageCard();
		} else if ( is_singular() ) {
			return static::buildPostCard();
		} else if ( is_author() ) {
			return static::buildAuthorCard();
		} else if ( is_archive() ) {
			return static::buildArchiveCard();
		}

		return null;
	}

	/**
	 * Twitter Card markup for the site homepage
	 *
	 * @since 1.0.0
	 *
	 * @return \Twitter\Cards\Card|null Twitter Card or null if minimum requirements not met
	 */
	public static function buildHomepageCard()
	{
		$query_type = 'home';
		$card = static::getCardObject( $query_type );
		if ( ! $card ) {
			return null;
		}

		/**
		 * Filter the title displayed in a Twitter Card template
		 *
		 * A title is only passed through this filter when not explicitly provided by the website for display in a Twitter Card template.
		 * This distinction is meant to simplify possible overrides by other plugins.
		 * Act on the `twitter_card` filter to always override this value late in the card builder process.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title      default title
		 * @param string $query_type current context, similar to a WP_Query
		 * @param string $object_id  object identifier for the query type, if applicable (post ID, author ID)
		 */
		$title = apply_filters( 'twitter_card_title', get_bloginfo( 'name' ) ?: '', $query_type, null );
		if ( $title ) {
			$card->setTitle( \Twitter\WordPress\Cards\Sanitize::sanitizePlainTextString( $title ) );
		}
		unset( $title );

		if ( method_exists( $card, 'setDescription' ) ) {
			/**
			 * Filter the description displayed in a Twitter Card template
			 *
			 * A title is only passed through this filter when not explicitly provided by the website for display in a Twitter Card template.
			 * This distinction is meant to simplify possible overrides by other plugins.
			 * Act on the `twitter_card` filter to always override this value late in the card builder process.
			 *
			 * @since 1.0.0
			 *
			 * @param string $description default description
			 * @param string $query_type  current context, similar to a WP_Query
			 * @param string $object_id   object identifier for the query type, if applicable (post ID, author ID)
			 */
			$description = apply_filters( 'twitter_card_description', get_bloginfo( 'description' ) ?: '', $query_type, null );
			if ( $description ) {
				$card->setDescription( \Twitter\WordPress\Cards\Sanitize::sanitizeDescription( $description ) );
			}
			unset( $description );
		}

		return $card;
	}

	/**
	 * Build a card for an author view
	 *
	 * @since 1.0.0
	 *
	 * @return \Twitter\Cards\Card|null Twitter Card object or null if minimum requirements not met
	 */
	public static function buildAuthorCard()
	{
		$author = get_queried_object();
		if ( ! ( $author && isset( $author->ID ) ) ) {
			return null;
		}

		$query_type = 'author';
		$card = static::getCardObject( $query_type, $author->ID );
		if ( ! $card ) {
			return null;
		}

		/** This filter is documented in ::buildHomepageCard */
		$title = apply_filters( 'twitter_card_title', get_the_author_meta( 'display_name', $author->ID ) ?: '', $query_type, $author->ID );
		if ( $title ) {
			$card->setTitle( \Twitter\WordPress\Cards\Sanitize::sanitizePlainTextString( $title ) );
		}
		unset( $title );

		if ( method_exists( $card, 'setDescription' ) ) {
			/** This filter is documented in ::buildHomepageCard */
			$description = apply_filters( 'twitter_card_description', get_the_author_meta( 'description', $author->ID ) ?: '', $query_type, $author->ID );
			if ( $description ) {
				$card->setDescription( \Twitter\WordPress\Cards\Sanitize::sanitizeDescription( $description ) );
			}
			unset( $description );
		}
		if ( method_exists( $card, 'setCreator' ) ) {
			$author_twitter_username = \Twitter\WordPress\User\Meta::getTwitterUsername( $author->ID );
			if ( $author_twitter_username ) {
				$card->setCreator( \Twitter\Cards\Components\Account::fromScreenName( $author_twitter_username ) );
			}
			unset( $author_twitter_username );
		}

		return $card;
	}

	/**
	 * Build a card for an archive view
	 *
	 * @since 1.0.0
	 *
	 * @return \Twitter\Cards\Card|null Twitter Card object or null if minimum requirements not met
	 */
	public static function buildArchiveCard()
	{
		$query_type = 'archive';
		$card = static::getCardObject( $query_type );
		if ( ! $card ) {
			return null;
		}

		// WP 4.1+ functions
		if ( function_exists( 'get_the_archive_title' ) ) {
			/** This filter is documented in ::buildHomepageCard */
			$title = apply_filters( 'twitter_card_title', get_the_archive_title(), $query_type, null );
			if ( $title ) {
				$card->setTitle( \Twitter\WordPress\Cards\Sanitize::sanitizePlainTextString( $title ) );
			}
			unset( $title );
		}
		if ( method_exists( $card, 'setDescription' ) && function_exists( 'get_the_archive_description' ) ) {
			/** This filter is documented in ::buildHomepageCard */
			$description = apply_filters( 'twitter_card_description', get_the_archive_description(), $query_type, null );
			if ( $description ) {
				$card->setDescription( \Twitter\WordPress\Cards\Sanitize::sanitizeDescription( $description ) );
			}
			unset( $description );
		}

		return $card;
	}

	/**
	 * Build a card for a single-post view
	 *
	 * @since 1.0.0
	 *
	 * @return \Twitter\Cards\Card|null Twitter Card object or null
	 */
	public static function buildPostCard()
	{
		$post = get_post();

		if ( ! $post || ! isset( $post->ID ) ) {
			return null;
		}
		setup_postdata( $post );

		// do not publish card markup for password-protected posts
		if ( ! empty( $post->post_password ) ) {
			return null;
		}

		// only publish card markup for public posts
		$post_status_object = get_post_status_object( get_post_status( $post->ID ) );
		if ( ! ( $post_status_object && isset( $post_status_object->public ) && $post_status_object->public ) ) {
			return null;
		}

		// only output Twitter Card markup for public post types
		// don't waste page generation time if the page is not meant to be consumed by TwitterBot
		$post_type = get_post_type( $post );
		if ( ! $post_type ) {
			return null;
		}
		$post_type_object = get_post_type_object( $post_type );
		if ( ! ( $post_type_object && isset( $post_type_object->public ) && $post_status_object->public ) ) {
			return null;
		}

		$query_type = 'post';
		$card = static::getCardObject( $query_type, $post->ID, 'summary' );
		if ( ! $card ) {
			return null;
		}

		$card_class = get_class( $card );
		if ( ! $card_class ) {
			return null;
		}

		// get post-specific overrides
		$cards_post_meta = get_post_meta(
			$post->ID,
			\Twitter\WordPress\Admin\Post\TwitterCard::META_KEY,
			true // single
		);

		// all cards support title
		if ( post_type_supports( $post_type, 'title' ) ) {
			$title = '';
			if ( isset( $cards_post_meta['title'] ) && $cards_post_meta['title'] ) {
				// do not pass an explicitly defined Twitter Card title through the title filter
				$title = $cards_post_meta['title'];
			} else {
				/** This filter is documented in ::buildHomepageCard */
				$title = apply_filters( 'twitter_card_title', get_the_title( $post->ID ), $query_type, $post->ID );
			}
			if ( $title ) {
				$card->setTitle( \Twitter\WordPress\Cards\Sanitize::sanitizePlainTextString( $title ) );
			}
			unset( $title );
		}

		// add description if card supports
		if ( method_exists( $card, 'setDescription' ) && post_type_supports( $post_type, 'excerpt' ) ) {
			$description = '';
			if ( isset( $cards_post_meta['description'] ) ) {
				// do not pass an explicitly defined Twitter Card description through the description filter
				$description = $cards_post_meta['description'];
			} else if ( ! empty( $post->post_excerpt ) ) {
				/** This filter is documented in wp-includes/post-template.php */
				$description = apply_filters( 'get_the_excerpt', $post->post_excerpt );
				/** This filter is documented in ::buildHomepageCard */
				$description = apply_filters( 'twitter_card_description', $description, $query_type, $post->ID );
			} else {
				/** This filter is documented in ::buildHomepageCard */
				$description = apply_filters( 'twitter_card_description', $post->post_content, $query_type, $post->ID );
			}

			$description = \Twitter\WordPress\Cards\Sanitize::sanitizeDescription( $description );
			if ( $description ) {
				$card->setDescription( $description );
			}
			unset( $description );
		}

		if ( defined( $card_class . '::MIN_IMAGE_WIDTH' ) && defined( $card_class . '::MIN_IMAGE_HEIGHT' ) ) {
			if ( method_exists( $card, 'setImage' ) ) {
				// single image card type
				$cards_image_handler = new \Twitter\WordPress\Cards\ImageHandler();
				$cards_image_handler->setLimit( 1 );
				$cards_image_handler->setMinWidth( $card::MIN_IMAGE_WIDTH );
				$cards_image_handler->setMinHeight( $card::MIN_IMAGE_HEIGHT );

				// discover images associated with the post
				$cards_image_handler->addPostImages( $post );

				$images = $cards_image_handler->getTwitterCardImages();
				if ( ! empty( $images ) ) {
					$card->setImage( reset( $images ) );
				}
				unset( $images );

				unset( $cards_image_handler );
			}
		}

		if ( post_type_supports( $post_type, 'author' ) && isset( $post->post_author ) && method_exists( $card, 'setCreator' ) ) {
			$author_twitter_username = \Twitter\WordPress\User\Meta::getTwitterUsername( $post->post_author );
			if ( $author_twitter_username ) {
				$card->setCreator( \Twitter\Cards\Components\Account::fromScreenName( $author_twitter_username ) );
			}
			unset( $author_twitter_username );
		}

		return $card;
	}
}
