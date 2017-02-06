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

namespace Twitter\WordPress\Widgets;

/**
 * Describe functions expected to be implemented by every widget class
 *
 * @since 2.0.0
 */
interface WidgetInterface
{

	/**
	 * An individual widget constructor should call the \WP_Widget constructor
	 *
	 * @since 2.0.0
	 */
	function __construct();

	/**
	 * Get the base ID used to identify widgets of this type installed in a widget area
	 *
	 * @since 2.0.1
	 *
	 * @return string widget base ID
	 */
	public static function getBaseID();

	/**
	 * Describe the functionality offered by the widget
	 *
	 * @since 2.0.0
	 *
	 * @return string description of the widget functionality
	 */
	public static function getDescription();

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
	function widget( $args, $instance );

	/**
	 * Settings update form
	 *
	 * @since 2.0.0
	 *
	 * @param array $instance Current settings
	 *
	 * @return void
	 */
	public function form( $instance );

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
	public function update( $new_instance, $old_instance );
}
