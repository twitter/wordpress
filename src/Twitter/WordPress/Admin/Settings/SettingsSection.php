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

namespace Twitter\WordPress\Admin\Settings;

/**
 * Expected structure of a settings section included in the Twitter settings page
 *
 * @since 1.0.1
 */
interface SettingsSection
{
	/**
	 * Reference the feature by name
	 *
	 * @since 1.0.1
	 *
	 * @return string translated feature name
	 */
	public static function featureName();

	/**
	 * Register settings, hook an onload handler
	 *
	 * @since 1.0.1
	 *
	 * @param string $hook_suffix hook suffix of an existing settings page
	 *
	 * @return bool settings section loaded
	 */
	public static function addToSettingsPage( $hook_suffix );

	/**
	 * Set up settings section
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function onload();

	/**
	 * Add settings section and fields
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function defineSection();
}
