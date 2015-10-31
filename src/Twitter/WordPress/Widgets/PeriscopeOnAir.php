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

namespace Twitter\WordPress\Widgets;

/**
 * Add Periscope On Air button as a WordPress widget
 *
 * @link http://codex.wordpress.org/Widgets_API WordPress widgets API
 *
 * @since 1.3.0
 */
class PeriscopeOnAir extends \WP_Widget
{

	/**
	 * Widget base ID
	 *
	 * Widget identifiers will derive from the base based on their positioning. e.g. periscope-on-air-1
	 *
	 * @since 1.3.0
	 *
	 * @type string
	 */
	const BASE_ID = 'periscope-on-air';

	/**
	 * Register widget with WordPress
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(
			static::BASE_ID, // Base ID
			__( 'Periscope On Air Button', 'twitter' ), // name
			array(
				'description' => __( 'Lets a viewer discover your Periscope account and on air status', 'twitter' ) // args
			)
		);
	}

	/**
	 * Front-end display of widget
	 *
	 * @since 1.3.0
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 *
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		// no Periscope username target
		if ( empty( $instance['username'] ) ) {
			return;
		}

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		unset( $instance['title'] );

		$button_html = \Twitter\WordPress\Shortcodes\PeriscopeOnAir::shortcodeHandler( $instance );
		if ( ! $button_html ) {
			return;
		}

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// escaped in markup builder
		// @codingStandardsIgnoreStart WordPress.XSS.EscapeOutput
		echo $button_html;
		// @codingStandardsIgnoreEnd WordPress.XSS.EscapeOutput

		echo $args['after_widget'];
	}

	/**
	 * Settings update form
	 *
	 * @since 1.3.0
	 *
	 * @param array $instance Current settings
	 *
	 * @return void
	 */
	public function form( $instance )
	{
		$instance = wp_parse_args(
			(array) $instance,
			array_merge(
				array( 'title' => '' ),
				\Twitter\WordPress\Shortcodes\PeriscopeOnAir::$SHORTCODE_DEFAULTS
			)
		);

		$close_void_element = \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html( __( 'Title:', 'twitter' ) ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( trim( strip_tags( $instance['title'] ) ) ); ?>"<?php echo $close_void_element; ?>></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php echo esc_html( _x( 'Periscope username', 'Prompt requesting entry of a Periscope username', 'twitter' ) ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" pattern="[a-zA-Z0-9_]{1,20}" value="<?php echo esc_attr( $instance['username'] ); ?>"<?php echo $close_void_element; ?>></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php echo esc_html( __( 'Button size:', 'twitter' ) ); ?></label>
		<fieldset id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>">
			<label><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" value="small"<?php checked( 'large' != $instance['size'] ); echo $close_void_element; ?>> <?php echo esc_html( _x( 'small', 'small size button', 'twitter' ) ); ?> </label>
			<label><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" value="large"<?php checked( 'large' == $instance['size'] ); echo $close_void_element; ?>> <?php echo esc_html( _x( 'large', 'large size button', 'twitter' ) ); ?> </label>
		</fieldset></p>
<?php
	}

	/**
	 * Update a widget instance
	 *
	 * @since 1.3.0
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
		$title = trim( strip_tags( $new_instance['title'] ) );
		if ( $title ) {
			$instance['title'] = $title;
		}
		unset( $new_instance['title'] );

		$on_air = \Twitter\Widgets\PeriscopeOnAir::fromArray( $new_instance );
		if ( ! $on_air ) {
			return false;
		}

		$filtered_options = $on_air->toArray();
		$username = $on_air->getUsername();
		if ( $username ) {
			$filtered_options['username'] = $username;
		}
		unset( $username );

		return array_merge( $instance, $filtered_options );
	}
}
