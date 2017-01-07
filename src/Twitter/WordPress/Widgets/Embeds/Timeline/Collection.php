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

namespace Twitter\WordPress\Widgets\Embeds\Timeline;

/**
 * Add embedded collection as a WordPress widget
 *
 * @link http://codex.wordpress.org/Widgets_API WordPress widgets API
 *
 * @since 2.0.0
 */
class Collection extends \Twitter\WordPress\Widgets\Embeds\Timeline
{
	/**
	 * Class of the related PHP object builder and validator
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const TIMELINE_CLASS = '\Twitter\Widgets\Embeds\Timeline\Collection';

	/**
	 * Class of the related shortcode handler
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_CLASS = '\Twitter\WordPress\Shortcodes\Embeds\Timeline\Collection';

	/**
	 * Describe the functionality offered by the widget
	 *
	 * @since 2.0.0
	 *
	 * @return string description of the widget functionality
	 */
	public static function getDescription()
	{
		return __( 'Tweets organized into a collection', 'twitter' );
	}

	/**
	 * Fields specific to the timeline datasource configured in the widget
	 *
	 * @since 2.0.0
	 *
	 * @param array $instance widget instance
	 *
	 * @return void
	 */
	protected function dataSourceFormElements( $instance )
	{
		?><p><label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php echo esc_html( _x( 'Collection ID', 'Unique identifier for a Twitter collection', 'twitter' ) . ':' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" type="text" pattern="[0-9]{1,}" inputmode="verbatim" spellcheck="false" value="<?php echo esc_attr( $instance['id'] ); ?>"<?php
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		?>></p><?php
	}

	/**
	 * Update a widget instance
	 *
	 * @since 2.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 *
	 * @return bool|array settings to save or false to cancel saving
	 */
	public function update( $new_instance, $old_instance )
	{
		$new_instance = (array) $new_instance;

		// collection ID required
		if ( ! ( isset( $new_instance['id'] ) && $new_instance['id'] ) ) {
			return false;
		}

		$instance = array();
		$title = sanitize_text_field( $new_instance['title'] );
		if ( $title ) {
			$instance['title'] = $title;
		}
		unset( $new_instance['title'] );

		// process widget parameters as if they were parsed shortcode attributes
		$shortcode_class = static::SHORTCODE_CLASS;
		$timeline_class  = static::TIMELINE_CLASS;
		if ( ! ( method_exists( $timeline_class, 'fromArray' ) && method_exists( $shortcode_class, 'shortcodeAttributesToTimelineKeys' ) ) ) {
		    return false;
		}
		$timeline = $timeline_class::fromArray( $shortcode_class::shortcodeAttributesToTimelineKeys( $new_instance ) );
		if ( ! ( $timeline && method_exists( $timeline, 'getID' ) && $timeline->getID() && method_exists( $timeline, 'toArray' ) ) ) {
			return false;
		}

		$data_attributes = $timeline->toArray();
		if ( isset( $data_attributes['tweet-limit'] ) ) {
			$data_attributes['limit'] = $data_attributes['tweet-limit'];
		}

		return array_merge( $instance, $data_attributes );
	}
}
