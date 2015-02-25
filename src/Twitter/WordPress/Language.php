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

namespace Twitter\WordPress;

/**
 * Match WordPress locale to a Twitter language
 *
 * @since 1.0.0
 */
class Language extends \Twitter\Widgets\Language
{

	/**
	 * Convert a WordPress locale into a Twitter-supported language code
	 *
	 * @since 1.0.0
	 *
	 * @return string Twitter language code or empty string if no suitable match found
	 */
	public static function localeToTwitterLang()
	{
		/**
		 * Filter the locale used to display translated text inside Twitter widgets
		 *
		 * @since 1.0.0
		 *
		 * @link https://dev.twitter.com/web/overview/languages supported languages
		 * @see \Twitter\Widgets\Language::$SUPPORTED_LANGUAGES
		 *
		 * @param string $locale locale returned by get_locale()
		 */
		$locale = apply_filters( 'twitter_locale', get_locale() );

		if ( ! ( is_string( $locale ) && $locale ) ) {
			return '';
		}
		$locale = strtolower( $locale );

		if ( 'tl' === $locale ) {
			return 'fil';
		} else if ( 'ms' === $locale ) {
			return 'msa';
		}

		// handle regional
		if ( 'zh_cn' === $locale ) {
			return 'zh-cn';
		} else if ( 'zh_tw' === $locale ) {
			return 'zh-tw';
		}

		if ( isset( static::$SUPPORTED_LANGUAGES[ $locale ] ) ) {
			return $locale;
		}

		if ( strlen( $locale ) > 2 ) {
			$locale = substr( $locale, 0, 2 );
			if ( isset( static::$SUPPORTED_LANGUAGES[ $locale ] ) ) {
				return $locale;
			}
			if ( 'ms' === $locale ) {
				return 'msa';
			}
		}

		return '';
	}
}
