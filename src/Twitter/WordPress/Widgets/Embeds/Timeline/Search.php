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

namespace Twitter\WordPress\Widgets\Embeds\Timeline;

/**
 * Add embedded search timeline as a WordPress widget
 *
 * @see http://codex.wordpress.org/Widgets_API WordPress widgets API
 *
 * @since 2.0.0
 */
class Search extends \Twitter\WordPress\Widgets\Embeds\Timeline
{
	/**
	 * Class of the related PHP object builder and validator
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const TIMELINE_CLASS = '\Twitter\Widgets\Embeds\Timeline\Search';

	/**
	 * Class of the related shortcode handler
	 *
	 * @since 2.0.0
	 *
	 * @type string
	 */
	const SHORTCODE_CLASS = '\Twitter\WordPress\Shortcodes\Embeds\Timeline\Search';

	/**
	 * Describe the functionality offered by the widget
	 *
	 * @since 2.0.0
	 *
	 * @return string description of the widget functionality
	 */
	public static function getDescription()
	{
		return __( 'Recent Tweets matching a Twitter search', 'twitter' );
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
		$close_html_element = \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		?><p><label for="<?php echo esc_attr( $this->get_field_id( 'widget_id' ) ); ?>"><?php echo esc_html( __( 'Twitter widget ID', 'twitter' ) . ':' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_id' ) ); ?>" type="text" pattern="[0-9]+" inputmode="verbatim" spellcheck="false" value="<?php echo esc_attr( $instance['widget_id'] ); ?>" required<?php
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $close_html_element;
		?>><br<?php
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $close_html_element;
		?>><small><?php
			printf(
				esc_html( _x( 'Create a new widget ID at %s', 'Create a widget identifier at a URL', 'twitter' ) ),
				'<a href="' . esc_url( 'https://twitter.com/settings/widgets/new/search', array( 'https', 'http' ) )  . '">' . esc_html( 'twitter.com/settings/widgets' ) . '</a>'
			);
		?></small></p><p><label for="<?php echo esc_attr( $this->get_field_id( 'terms' ) ); ?>"><?php echo esc_html( __( 'Search terms', 'twitter' ) . ':' ); ?></label><input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'terms' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'terms' ) ); ?>" type="text" maxlength="450" value="<?php echo esc_attr( $instance['terms'] ); ?>"<?php
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $close_html_element;
		?>></p>
		<?php
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
		$instance = array();
		$new_instance = (array) $new_instance;
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
		if ( ! ($timeline && $timeline->getWidgetID() && method_exists( $timeline, 'toArray' ) ) ) {
			return false;
		}

		$data_attributes = $timeline->toArray();
		// convert data-* dashes to shortcode underscores
		if ( isset( $data_attributes['widget-id'] ) ) {
			$data_attributes['widget_id'] = $data_attributes['widget-id'];
			unset( $data_attributes['widget-id'] );
		}
		$data_attributes['terms'] = $timeline->getSearchTerms();

		return array_merge( $instance, $data_attributes );
	}
}
