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
 * Share common template functionality across Twitter setting pages
 *
 * @since 1.0.0
 */
trait Template
{

	/**
	 * Load the settings page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settingsPage()
	{
		if ( ! isset( $this->hook_suffix ) ) {
			return;
		}

		do_action( 'add-' . $this->hook_suffix . '-section' );

		echo '<div class="wrap">';

		echo '<header><h2>' . esc_html( static::featureName() ) . '</h2></header>';
		// handle general messages such as settings updated up top
		// place individual settings errors alongside their fields
		settings_errors( 'general' );

		echo '<form method="post" action="' . esc_url( admin_url( 'options.php' ), array( 'https', 'http' ) ) . '">';
		settings_fields( $this->hook_suffix );
		do_settings_sections( $this->hook_suffix );
		submit_button();
		echo '</form>';

		echo '</div>';
	}

}
