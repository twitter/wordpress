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
 * Discover images in a WordPress post eligible for inclusion as a Twitter Card image
 *
 * Rejects images under the minimum dimensions specified for the card type. Rejection allows the search for a suitable image to continue, up to the total number of images used in the card template.
 *
 * @since 1.0.0
 */
class ImageHandler
{

	/**
	 * 1 MB in decimal units
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	const MAX_FILESIZE = 1000000;

	/**
	 * Maximum number of images supported by the Twitter Cards template
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $limit = 1;

	/**
	 * Minimum required width of a Twitter Card image
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $min_width = 120;

	/**
	 * Minimum required height of a Twitter Card image
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $min_height = 120;

	/**
	 * Store discovered images for the post
	 *
	 * @since 1.0.0
	 *
	 * @var array images {
	 *   @type string image URL
	 *   @type \Twitter\Cards\Components\Image
	 * }
	 */
	protected $images = array();

	/**
	 * Number of images currently stored in $images
	 *
	 * @since 1.0.0
	 *
	 * @var int number of stored images
	 */
	protected $images_count = 0;

	/**
	 * Maximum number of images displayed in the Twitter Card template
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit maximum number of images displayed in the Twitter Card template
	 *
	 * @return \Twitter\WordPress\Cards\ImageHandler support chaining
	 */
	public function setLimit( $limit )
	{
		if ( is_int( $limit ) && $limit > 0 ) {
			// set max range to extended entities limit
			if ( $limit > 25 ) {
				$limit = 25;
			}

			$this->limit = $limit;
		}

		return $this;
	}

	/**
	 * Have we stored the maximum number of images displayed in the Twitter Card template?
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if no more images needed, else False
	 */
	public function isFull()
	{
		return ( $this->images_count === $this->limit );
	}

	/**
	 * Set the minimum image width of the current card
	 *
	 * @since 1.0.0
	 *
	 * @param int $width minimum width of the Twitter Cards image in whole pixels
	 *
	 * @return \Twitter\WordPress\Cards\ImageHandler support chaining
	 */
	public function setMinWidth( $width )
	{
		if ( is_int( $width ) && $width >= 120 ) {
			$this->min_width = $width;
		}
		return $this;
	}

	/**
	 * Set the minimum image height of hte current card
	 *
	 * @since 1.0.0
	 *
	 * @param int $height minimum height of the Twitter Cards image in whole pixels
	 *
	 * @return \Twitter\WordPress\Cards\ImageHandler support chaining
	 */
	public function setMinHeight( $height )
	{
		if ( is_int( $height ) && $height >= 120 ) {
			$this->min_height = $height;
		}
		return $this;
	}

	/**
	 * Get a flattened array of Twitter Card images
	 *
	 * @since 1.0.0
	 *
	 * @return array Twitter Card images {
	 *   @type \Twitter\Cards\Components\Image Twitter Card image
	 * }
	 */
	public function getTwitterCardImages()
	{
		if ( empty( $this->images ) ) {
			return array();
		}

		return array_values( $this->images );
	}

	/**
	 * Possibly add a new image for a given WordPress attachment ID
	 *
	 * @since 1.0.0
	 *
	 * @param int $attachment_id WordPress attachment ID
	 *
	 * @return bool was an image added to the array?
	 */
	public function addImageByAttachmentID( $attachment_id )
	{
		if ( ! $attachment_id ) {
			return false;
		}

		$image = $this->attachmentToTwitterImage( $attachment_id );
		if ( ! $image ) {
			return false;
		}

		$image_url = $image->getURL();
		if ( ! $image_url || isset( $this->images[ $image_url ] ) ) {
			return false;
		}

		$this->images[ $image_url ] = $image;
		$this->images_count++;

		return true;
	}

	/**
	 * Add images associated with a WP_Post up to limit.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post post of interest
	 *
	 * @return void
	 */
	public function addPostImages( $post )
	{
		// bad parameter
		if ( ! $post || ! isset( $post->ID ) ) {
			return;
		}

		// does current post type and the current theme support post thumbnails?
		if ( post_type_supports( get_post_type( $post ), 'thumbnail' ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
			if ( $this->addImageByAttachmentID( get_post_thumbnail_id( $post->ID ) ) ) {
				if ( $this->isFull() ) {
					return;
				}
			}
		}

		// image attachments
		$attached_images = get_attached_media( 'image', $post );
		if ( ! empty( $attached_images ) ) {
			foreach ( $attached_images as $attached_image ) {
				if ( ! isset( $attached_image->ID ) ) {
					continue;
				}

				if ( ! $this->addImageByAttachmentID( $attached_image->ID ) ) {
					continue;
				}

				// hit the limit. end the search
				if ( $this->isFull() ) {
					return;
				}
			}
		}
		unset( $attached_images );

		// post galleries
		$galleries = get_post_galleries( $post, /* html */ false );
		foreach ( $galleries as $gallery ) {
			if ( ! ( isset( $gallery['ids'] ) && $gallery['ids'] ) ) {
				continue;
			}

			$gallery_ids = explode( ',', $gallery['ids'] );
			foreach ( $gallery_ids as $attachment_id ) {
				if ( ! $this->addImageByAttachmentID( $attachment_id ) ) {
					continue;
				}

				// hit the limit. end the search
				if ( $this->isFull() ) {
					return;
				}
			}
			unset( $gallery_ids );
		}
		unset( $galleries );
	}

	/**
	 * Convert a WordPress image attachment to a Twitter Card image object
	 *
	 * @since 1.0.0
	 *
	 * @param int    $attachment_id WordPress attachment ID
	 * @param string $size          desired size
	 *
	 * @return \Twitter\Cards\Components\Image|null Twitter Card image
	 */
	public function attachmentToTwitterImage( $attachment_id, $size = 'full' )
	{
		if ( ! ( is_string( $size ) && $size ) ) {
			$size = 'full';
		}

		// request large version of image if full version exceeds filesize limit
		if ( 'full' === $size ) {
			$attached_file = get_attached_file( $attachment_id );
			if ( $attached_file && file_exists( $attached_file ) ) {
				$bytes = filesize( $attached_file );
				if ( $bytes && $bytes > self::MAX_FILESIZE ) {
					/**
					 * Filter the intermediate image size to be provided for Twitter thumbnail when a full-size image exceeds Twitter's filesize limit
					 *
					 * Twitter will consume the largest available image under the filesize limit and generate thumbnails appropriate for Twitter Card display in various dimension and DPI contexts
					 *
					 * @since 1.0.0
					 *
					 * @param string $size          The intermediate size. Default: large
					 * @param int    $attachment_id Attachment identifier
					 */
					$intermediate_size = apply_filters( 'twitter_card_intermediate_image_size', 'large', $attachment_id );
					// check filtered intermediate size to avoid possible infinite loop
					if ( ! $intermediate_size || 'full' === $intermediate_size || ! has_image_size( $intermediate_size ) ) {
						return;
					}
					return $handler->attachmentToTwitterImage( $attachment_id, $intermediate_size );
				}
				unset( $bytes );
			}
			unset( $attached_file );
		}

		list( $url, $width, $height ) = wp_get_attachment_image_src( $attachment_id, $size );

		if ( empty( $url ) ) {
			return;
		}
		$image = new \Twitter\Cards\Components\Image( $url );

		if ( ! empty( $width ) ) {
			$width = absint( $width );
			if ( $width ) {
				if ( $width < $this->min_width ) {
					// reject if image width below required width
					return;
				}
				$image->setWidth( $width );
			}
			// width and height are a resizing hint. must exist as a pair
			if ( ! empty( $height ) ) {
				$height = absint( $height );
				if ( $height ) {
					if ( $height < $this->min_height ) {
						// reject if image height below required height
						return;
					}
					$image->setHeight( $height );
				}
			}
		}

		return $image;
	}
}
