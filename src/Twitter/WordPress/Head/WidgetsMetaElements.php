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

namespace Twitter\WordPress\Head;

/**
 * Output Twitter for Websites <meta> elements
 *
 * @since 1.0.0
 */
class WidgetsMetaElements
{
	/**
	 * Get any stored widget theme options
	 *
	 * @since 1.0.0
	 *
	 * @return array|bool array of set options or false if no option exists
	 */
	public static function getWidgetOptions()
	{
		return get_option( \Twitter\WordPress\Admin\Settings\Theme::OPTION_NAME );
	}

	/**
	 * Opt-out of using visits to this website to influence Twitter tailored content and other suggestions for Twitter users
	 *
	 * Applies when Twitter's widgets.js appears on the page to enhance a button or widget
	 *
	 * @since 1.0.0
	 *
	 * @link https://dev.twitter.com/web/overview/privacy Twitter for Websites privacy
	 *
	 * @return bool opt out of Twitter tailored audiences
	 */
	public static function optOutOfTwitterTailoredAudiences()
	{
		/**
		 * Do not use website visits to tailor content and suggestions for the Twitter audience
		 *
		 * This setting does not apply to Twitter's advertising tracker, opt.js, which you may choose to include on the page to build a target audience or track conversions.
		 *
		 * @since 1.0.0
		 *
		 * @link https://dev.twitter.com/web/overview/privacy Twitter for Websites privacy
		 *
		 * @param bool $override opt out of Twitter tailoring
		 */
		return apply_filters( 'twitter_dnt', false );
	}

	/**
	 * Convert stored option associative array into a new associative array of meta element name-content pairs for consumption by Twitter's widget JS
	 *
	 * @since 1.0.0
	 *
	 * @param array $options widget options stored by the site
	 *
	 * @return array associative array
	 */
	public static function widgetOptionsToMetaElementPairs( array $options )
	{
		if ( empty( $options ) ) {
			return array();
		}

		$widgets_prefix = 'widgets:';

		$meta = array();

		// Suppress Content Security Policy warning
		if ( isset( $options['csp'] ) ) {
			$meta[ $widgets_prefix . 'csp' ] = 'on';
		}

		// dark theme
		if ( isset( $options['theme'] ) && 'dark' === $options['theme'] ) {
			$meta[ $widgets_prefix . 'theme' ] = 'dark';
		}

		// colors
		foreach ( array( 'link-color', 'border-color' ) as $color_option ) {
			if ( isset( $options[ $color_option ] ) && $options[ $color_option ] ) {
				$meta[ $widgets_prefix . $color_option ] = '#' . $options[ $color_option ];
			}
		}

		return $meta;
	}

	/**
	 * Build HTML meta elements to be included as children of <head>
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML meta elements
	 */
	public static function buildMetaElements()
	{
		$meta = array();

		$widget_options = static::getWidgetOptions();
		if ( ! empty( $widget_options ) ) {
			$meta = static::widgetOptionsToMetaElementPairs( $widget_options );
		}
		unset( $widget_options );

		// opt out of tailored audiences
		if ( static::optOutOfTwitterTailoredAudiences() ) {
			$meta['dnt'] = 'on';
		}

		// powered by Twitter plugin for WordPress
		// please leave for stats
		$meta['partner'] = 'tfwp';

		if ( empty( $meta ) ) {
			return '';
		}

		$html = '';
		foreach ( $meta as $name => $content ) {
			$html .= \Twitter\WordPress\Head\MetaElement::fromNameContentPair( $name, $content );
		}
		return $html;
	}

	/**
	 * Output a HTML string if wiidget theme or other preferences exist
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function outputMetaElements()
	{
		// escaped in markup builder
		// @codingStandardsIgnoreStart WordPress.XSS.EscapeOutput
		echo static::buildMetaElements();
		// @codingStandardsIgnoreEnd WordPress.XSS.EscapeOutput
	}
}
