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

namespace Twitter\Widgets\Embeds;

/**
 * An embed capable of theming
 *
 * @since 2.0.0
 */
trait Theme
{
    /**
     * Light background, dark text
     *
     * @since 2.0.0
     *
     * @type string
     */
    public static $THEME_LIGHT = 'light';

    /**
     * Dark background, light text
     *
     * @since 2.0.0
     *
     * @type string
     */
    public static $THEME_DARK = 'dark';

    /**
     * Embed display theme
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $theme = 'light';

    /**
     * Hexadecimal color of borders separating Tweets or Tweet elements
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $border_color;

    /**
     * Hexadecimal color of links appearing in Tweet text
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $link_color;

    /**
     * Test if the provided theme value is accepted by Twitter for Websites embeds
     *
     * @since 2.0.0
     *
     * @param string $theme dark or light theme
     *
     * @return bool true if valid theme value, else false
     */
    public static function isValidTheme($theme)
    {
        return ( $theme === static::$THEME_LIGHT || $theme === static::$THEME_DARK );
    }

    /**
     * Test if a provided string is a valid hexadecimal color
     *
     * @param string $color hexadecimal color
     *
     * @return bool true if valid hexadecimal color else false
     */
    public static function isValidHexadecimalColor($color)
    {
        if ($color && is_string($color) && 6 === strlen($color)) {
            if (function_exists('ctype_xdigit')) {
                if (ctype_xdigit($color)) {
                    return true;
                }
            } elseif (preg_match('/^[a-f0-9]{6}$/i', $color)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set a widget embed theme
     *
     * @since 2.0.0
     *
     * @param string $theme light or dark
     *
     * @return self support chaining
     */
    public function setTheme($theme)
    {
        if ($theme && is_string($theme)) {
            $theme = trim(strtolower($theme));
            if ($theme && static::isValidTheme($theme)) {
                $this->theme = $theme;
            }
        }

        return $this;
    }

    /**
     * Set a light theme
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function setThemeLight()
    {
        if ($this->theme !== static::$THEME_LIGHT) {
            $this->theme = static::$THEME_LIGHT;
        }

        return $this;
    }

    /**
     * Set a light theme
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function setThemeDark()
    {
        if ($this->theme !== static::$THEME_DARK) {
            $this->theme = static::$THEME_DARK;
        }

        return $this;
    }

    /**
     * Clean and normalize supplied hexadecimal color
     *
     * @since 2.0.0
     *
     * @param string $color hexadecimal color
     *
     * @return string cleaned and normalized hexadecimal color
     */
    protected static function cleanHexadecimalColor($color)
    {
        if ($color && is_string($color)) {
            // remove any leading hash
            $color = ltrim(trim($color), '#');

            if ($color) {
                // convert abbrevated colors
                if (3 === strlen($color)) {
                    $color .= $color;
                }

                $color = strtoupper($color);
                if (static::isValidHexadecimalColor($color)) {
                    return $color;
                }
            }
        }

        return '';
    }

    /**
     * Set the color of links in Tweet text
     *
     * @since 2.0.0
     *
     * @param string $color hexadecimal color
     *
     * @return self support chaining
     */
    public function setLinkColor($color)
    {
        $color = static::cleanHexadecimalColor($color);
        if ($color) {
            $this->link_color = $color;
        }

        return $this;
    }

    /**
     * Set the color of borders separating Tweets or Tweet components
     *
     * @since 2.0.0
     *
     * @param string $color hexadecimal color
     *
     * @return self support chaining
     */
    public function setBorderColor($color)
    {
        $color = static::cleanHexadecimalColor($color);
        if ($color) {
            $this->border_color = $color;
        }

        return $this;
    }

    /**
     * Set theme-related options from an associative array
     *
     * @since 2.0.0
     *
     * @param array $options associative array of options {
     *   @type string option name
     *   @type string|int|bool option value
     * }
     *
     * @return self support chaining
     */
    public function setThemeOptions($options)
    {
        if (isset($options['theme']) && is_string($options['theme']) && static::$THEME_DARK === strtolower($options['theme'])) {
            $this->setThemeDark();
        }

        if (isset($options['link-color']) && $options['link-color'] && is_string($options['link-color'])) {
            $this->setLinkColor($options['link-color']);
        }

        if (isset($options['border-color']) && $options['border-color'] && is_string($options['border-color'])) {
            $this->setBorderColor($options['border-color']);
        }

        return $this;
    }

    /**
     * Convert trait properties into an associative array
     *
     * @since 2.0.0
     *
     * @return array properties as associative array
     */
    public function themeToArray()
    {
        $data = array();

        if (isset($this->theme) && static::$THEME_LIGHT !== $this->theme) {
            $data['theme'] = $this->theme;
        }

        if (isset($this->link_color) && is_string($this->link_color) && 6 === strlen($this->link_color)) {
            $data['link-color'] = '#' . $this->link_color;
        }

        if (isset($this->border_color) && is_string($this->border_color) && 6 === strlen($this->border_color)) {
            $data['border-color'] = '#' . $this->border_color;
        }

        return $data;
    }

    /**
     * Format theme parameters as oEmbed-ready query parameters
     *
     * @since 2.0.0
     *
     * @return array parameter array {
     *   @type string query parameter name
     *   @type string query parameter value
     * }
     */
    public function themeToOEmbedParameterArray()
    {
        $oembed = $this->themeToArray();

        $data_to_oembed = array(
            'link-color' => 'link_color',
            'border-color' => 'border_color',
        );

        foreach ($data_to_oembed as $data_attribute => $oembed_parameter) {
            if (isset($oembed[$data_attribute])) {
                $oembed[$oembed_parameter] = $oembed[$data_attribute];
                unset($oembed[$data_attribute]);
            }
        }

        return $oembed;
    }
}
