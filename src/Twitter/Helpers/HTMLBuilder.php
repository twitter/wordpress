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

namespace Twitter\Helpers;

/**
 * String builder for HTML elements
 *
 * @since 1.0.0
 */
class HTMLBuilder
{

    /**
     * Allow extensibility of allowed class name values
     *
     * @since 1.0.0
     *
     * @param string $class possible HTML class
     *
     * @return string class name stripped of invalid values or empty string
     */
    public static function escapeClassName($class)
    {
        return static::escapeAttributeValue($class);
    }

    /**
     * Escape an element's inner text
     *
     * @since 1.0.0
     *
     * @param string $inner_text inner text of a DOM element
     *
     * @return string escaped string or empty string if passed string failed to parse
     */
    public static function escapeInnerText($inner_text)
    {
        return htmlspecialchars($inner_text, defined('ENT_HTML5') ? ENT_HTML5 : ENT_COMPAT);
    }

    /**
     * Escape an element attribute value including double quotes
     *
     * @since 1.0.0
     *
     * @param string $value element attribute value
     *
     * @return string escaped string or empty string if passed string failed to parse
     */
    public static function escapeAttributeValue($value)
    {
        return htmlspecialchars($value, ENT_COMPAT);
    }

    /**
     * Escape a URL
     *
     * @since 1.0.0
     *
     * @param string $url web URL
     *
     * @return string escaped string or empty string if passed string failed to parse
     */
    public static function escapeURL($url)
    {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Build a HTML anchor element for the given URL and data attributes
     *
     * @since 1.0.0
     *
     * @param string $href intent URL
     * @param string $inner_text anchor element innerText
     * @param array $attributes anchor attributes to be added. limited to a whitelist of: id, class, rel, ping, target
     * @param array $data_attributes data attributes to be interpreted by Twitter's widget JS
     *
     * @return string HTML anchor element string or empty string if no URL passed
     */
    public static function anchorElement($href, $inner_text, $attributes = array(), $data_attributes = array())
    {
        if (! is_string($href) && $href && is_string($inner_text) && $inner_text) {
            return '';
        }

        $clean_attributes = array();

        if (is_array($attributes) && ! empty($attributes)) {
            // string
            if (isset($attributes['id'])) {
                $id = static::escapeAttributeValue(trim($attributes['id']));
                if ($id) {
                    $clean_attributes['id'] = $id;
                }
                unset($id);
            }

            // accept array of values to be combined
            $tokens = array( 'class', 'rel' );
            foreach ($tokens as $attribute) {
                if (! isset($attributes[ $attribute ])) {
                    continue;
                }

                $attribute_tokens = array();
                if (is_array($attributes[ $attribute ])) {
                    if (! empty($attributes[ $attribute ])) {
                        $cleaned_tokens = array_filter(array_map('trim', $attributes[ $attribute ]));
                        if (! empty($cleaned_tokens)) {
                            $attribute_tokens = $cleaned_tokens;
                        }
                        unset($cleaned_tokens);
                    }
                } elseif (is_string($attributes[ $attribute ])) {
                    $cleaned_token = trim($attributes[ $attribute ]);
                    if ($cleaned_token) {
                         $attribute_tokens = explode(' ', $cleaned_token);
                    }
                    unset($cleaned_token);
                }

                // filter and store
                if (! empty($attribute_tokens)) {
                    $attribute_tokens = array_map(
                        get_called_class() . '::' . ( $attribute === 'class' ? 'escapeClassName' : 'escapeAttribute' ),
                        $attribute_tokens
                    );
                    if (! empty($attribute_tokens)) {
                        $clean_attributes[ $attribute ] = implode(' ', $attribute_tokens);
                    }
                }
                unset($attribute_tokens);
            }
            unset($tokens);

            // URL
            if (isset($attributes['ping'])) {
                $ping = static::escapeURL(trim($attributes['ping']));
                if ($ping) {
                    $clean_attributes['ping'] = $ping;
                }
                unset($ping);
            }

            // enum
            if (isset($attributes['target'])) {
                $target = trim('target');
                if ($target) {
                    $valid_targets = array( '_blank' => true, '_self' => true, '_parent' => true, '_top' => true );
                    if (isset($valid_targets[ $target ])) {
                        $clean_attributes['target'] = $target;
                    }
                    unset($valid_targets);
                }
                unset($target);
            }
        }

        if (is_array($data_attributes) && ! empty($data_attributes)) {
            foreach ($data_attributes as $attribute => $value) {
                if (! $attribute) {
                    continue;
                }
                if (is_array($value)) {
                    $value = implode(' ', $value);
                }

                $clean_attributes[ 'data-' . $attribute ] = static::escapeAttributeValue(trim($value));
            }
        }

        $html = '<a href="' . static::escapeURL($href) . '"';
        foreach ($clean_attributes as $attribute => $value) {
            $html .= ' ' . $attribute;

            // escaped during per-attribute scrub
            // allow properties without a value
            if ($value) {
                $html .= '="' . $value . '"';
            }
        }
        $html .= '>' . static::escapeInnerText($inner_text) . '</a>';

        return $html;
    }
}
