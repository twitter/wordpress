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
 * Initialize hooks related to WordPress admin interface settings
 *
 * @since 1.0.0
 */
class Loader
{
	/**
	 * Add hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init()
	{
		add_action( 'admin_menu', array( '\Twitter\WordPress\Admin\Settings\SinglePage', 'menuItem' ) );
		add_filter( 'plugin_action_links', array( __CLASS__, 'pluginActionLinks' ), 10, 2 );
	}

	/**
	 * Link to settings from the plugin listing page
	 *
	 * @since 1.0.0
	 *
	 * @param array  $links links displayed under the plugin
	 * @param string $file  plugin main file path relative to plugin dir
	 *
	 * @return array links array passed in, possibly with our settings link added
	 */
	public static function pluginActionLinks( $links, $file )
	{
		if ( plugin_basename( \Twitter\WordPress\PluginLoader::getPluginMainFile() === $file ) ) {
			array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php' ) . '?' . http_build_query( array( 'page' => \Twitter\WordPress\Admin\Settings\SinglePage::PAGE_SLUG ) ) ) . '">' . __( 'Settings' ) . '</a>' );
		}

		return $links;
	}
}
