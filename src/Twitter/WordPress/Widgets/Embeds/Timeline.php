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

namespace Twitter\WordPress\Widgets\Embeds;

/**
 * Add embedded profile timeline as a WordPress widget
 *
 * @link http://codex.wordpress.org/Widgets_API WordPress widgets API
 *
 * @since 2.0.0
 */
abstract class Timeline extends \Twitter\WordPress\Widgets\Widget implements \Twitter\WordPress\Widgets\WidgetInterface
{
	/**
	 * Register widget with WordPress
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct()
	{
		$shortcode_class = static::SHORTCODE_CLASS;
		parent::__construct(
			$shortcode_class::HTML_CLASS,    // Base ID
			$shortcode_class::featureName(), // name
			array(
				'description' => static::getDescription(),
			)
		);
	}

	/**
	 * Get the base ID used to identify widgets of this type installed in a widget area
	 *
	 * @since 2.0.1
	 *
	 * @return string widget base ID
	 */
	public static function getBaseID()
	{
		$shortcode_class = static::SHORTCODE_CLASS;
		return $shortcode_class::HTML_CLASS;
	}

	/**
	 * Get displayed options for chrome configuration with label
	 *
	 * @since 2.0.0
	 *
	 * @return array chrome options
	 */
	public static function getChromeFormOptions()
	{
		return array(
			\Twitter\Widgets\Embeds\Timeline::CHROME_NOHEADER    => _x( 'Hide header', 'Hide introductory section before the timeline', 'twitter' ),
			\Twitter\Widgets\Embeds\Timeline::CHROME_NOFOOTER    => _x( 'Hide footer', 'Hide summary section after the timeline', 'twitter' ),
			\Twitter\Widgets\Embeds\Timeline::CHROME_NOBORDERS   => _x( 'Hide borders', 'Hide the visual line between two sections', 'twitter' ),
			\Twitter\Widgets\Embeds\Timeline::CHROME_NOSCROLLBAR => _x( 'Hide scroll bar', 'Hide the visual indicator of additional content available above or below the current position', 'twitter' ),
			\Twitter\Widgets\Embeds\Timeline::CHROME_TRANSPARENT => _x( 'Transparent background', 'Remove color shown behind the widget', 'twitter' ),
		);
	}

	/**
	 * Front-end display of widget
	 *
	 * @since 2.0.0
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 *
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		$shortcode_class = static::SHORTCODE_CLASS;
		if ( ! method_exists( $shortcode_class, 'shortcodeHandler' ) ) {
		    return;
		}
		$timeline_html = $shortcode_class::shortcodeHandler( $instance );
		if ( ! $timeline_html ) {
			return;
		}

		// Allow HTML markup set by author, site
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'];

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		if ( $title ) {
			// Allow HTML markup set by author, site
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $args['before_title'];

			// Allow HTML in title. Link to Twitter datasource might be common use
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $title;

			// Allow HTML markup set by author, site
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $args['after_title'];
		}

		// escaped in markup builder
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput
		echo $timeline_html;

		// Allow HTML markup set by author, site
		// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo $args['after_widget'];
	}

	/**
	 * Settings update form
	 *
	 * @since 2.0.0
	 *
	 * @param array $instance Current settings
	 *
	 * @return void
	 */
	public function form( $instance )
	{
		$shortcode_class = static::SHORTCODE_CLASS;
		if ( ! method_exists( $shortcode_class, 'getShortcodeDefaults' ) ) {
			return;
		}
		$instance = wp_parse_args(
			(array) $instance,
			array_merge(
				array( 'title' => '' ),
				$shortcode_class::getShortcodeDefaults()
			)
		);

		$this->titleFormElements( $instance );
		$this->dataSourceFormElements( $instance );
		$this->timelineFormElements( $instance );
	}

	/**
	 * Widget form applicable to all timelines
	 *
	 * @since 2.0.0
	 *
	 * @param array $instance widget instance
	 *
	 * @return void
	 */
	protected function timelineFormElements( $instance )
	{
		$close_void_element = \Twitter\WordPress\Helpers\HTMLBuilder::closeVoidHTMLElement();
		?><p><label for="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>"><?php echo esc_html( \Twitter\WordPress\Admin\Settings\Embeds\Theme::featureName() . ':' ); ?></label></p><?php
		$theme_choices = \Twitter\WordPress\Admin\Settings\Embeds\Theme::themeChoices();
		?><fieldset id="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>"><?php
foreach ( $theme_choices as $value => $label ) {
		?>
		<p><label><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'theme' ) ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php
			checked( $value, $instance['theme'] );
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $close_void_element;
		?>><?php echo esc_html( $label ); ?></label></p>
		<?php } ?>
		</fieldset>

		<?php
		$chrome = static::getChromeFormOptions();
		if ( ! ( isset( $instance['chrome'] ) && is_array( $instance['chrome'] ) ) ) {
			$instance['chrome'] = array();
		}
		?><label for="<?php echo esc_attr( $this->get_field_id( 'chrome' ) ); ?>"><?php echo esc_html( _x( 'Layout options', 'Visual layout choices', 'twitter' ) . ':' ); ?></label>
		<fieldset id="<?php echo esc_attr( $this->get_field_id( 'chrome' ) ); ?>"><?php
		foreach ( $chrome as $value => $label ) {
			?><p><label><input type="checkbox" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'chrome' ) ); ?>[]" value="<?php echo esc_attr( $value ); ?>"<?php
				checked( in_array( $value, $instance['chrome'], /* strict */ true ) );
				// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
				echo $close_void_element;
			?>><?php echo esc_html( $label ); ?></label></p><?php
		}
		?></fieldset>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php echo esc_html( _x( 'Limit', 'Limit the number of Tweets displayed', 'twitter' ) . ':' ); ?></label>
		<input type="number" class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" min="<?php echo esc_attr( \Twitter\Widgets\Embeds\Timeline::MIN_LIMIT ); ?>" max="<?php echo esc_attr( \Twitter\Widgets\Embeds\Timeline::MAX_LIMIT ); ?>" step="1" value="<?php echo esc_attr( $instance['limit'] ); ?>" size="3"<?php
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $close_void_element;
		?>>
		</p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php echo esc_html( _x( 'Width', 'Horizontal dimension measured in whole pixels', 'twitter' ) . ':' ); ?></label><input type="number" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" class="small-text" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" step="1" min="<?php echo esc_attr( \Twitter\Widgets\Embeds\Timeline::MIN_WIDTH ); ?>" max="<?php echo esc_attr( \Twitter\Widgets\Embeds\Timeline::MAX_WIDTH ); ?>" step="10" value="<?php echo esc_attr( $instance['width'] ); ?>" size="4"<?php
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $close_void_element;
		?>></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php echo esc_html( _x( 'Height', 'Vertical dimension measured in whole pixels', 'twitter' ) . ':' ); ?></label><input type="number" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" class="small-text" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" step="1" min="<?php echo esc_attr( \Twitter\Widgets\Embeds\Timeline::MIN_HEIGHT ); ?>" step="10" value="<?php echo esc_attr( $instance['height'] ); ?>" size="4"<?php
			// @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo $close_void_element;
		?>></p><?php
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
	abstract protected function dataSourceFormElements( $instance );
}
