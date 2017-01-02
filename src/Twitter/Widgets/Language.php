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

namespace Twitter\Widgets;

/**
 * Languages supported by Twitter for Websites widgets
 *
 * @since 1.0.0
 *
 * @link https://dev.twitter.com/rest/reference/get/help/languages list of supported languages
 */
class Language
{
    /**
     * A list of languages supported by Twitter in production
     *
     * @since 1.0.0
     *
     * @type array language code => english name
     */
    public static $SUPPORTED_LANGUAGES = array(
        'ar' => 'Arabic',
        'bn' => 'Bengali',
        'cs' => 'Czech',
        'da' => 'Danish',
        'de' => 'German',
        'el' => 'Greek',
        'en' => 'English',
        'es' => 'Spanish',
        'fa' => 'Persian',
        'fi' => 'Finnish',
        'fil' => 'Filipino',
        'fr' => 'French',
        'he' => 'Hebrew',
        'hi' => 'Hindi',
        'hu' => 'Hungarian',
        'id' => 'Indonesian',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'ko' => 'Korean',
        'msa' => 'Malay',
        'nl' => 'Dutch',
        'no' => 'Norwegian',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'sv' => 'Swedish',
        'th' => 'Thai',
        'tr' => 'Turkish',
        'uk' => 'Ukrainian',
        'ur' => 'Urdu',
        'vi' => 'Vietnamese',
        'zh-cn' => 'Simplified Chinese',
        'zh-tw' => 'Traditional Chinese',
    );

    /**
     * Is the passed language a valid Twitter production language code?
     *
     * @since 1.0.0
     *
     * @param string $lang language code
     *
     * @return bool true if passed language exists in list of Twitter production language codes
     */
    public static function isSupportedLanguage($lang)
    {
        return ( is_string($lang) && $lang && isset(static::$SUPPORTED_LANGUAGES[$lang]) );
    }
}
