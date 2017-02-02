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

namespace Twitter\Widgets\Embeds;

/**
 * Base properties used across all embedded timelines
 *
 * @since 2.0.0
 */
abstract class Timeline extends \Twitter\Widgets\Base
{
    use \Twitter\Widgets\Embeds\Theme;

    /**
     * The minimum supported display height of an embedded timeline
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MIN_HEIGHT = 200;

    /**
     * The minimum supported display height of an embedded timeline
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MIN_WIDTH = 180;

    /**
     * The maximum supported display width of an embedded timeline
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MAX_WIDTH = 1200;

    /**
     * The minimum number of Tweets supported without pagination
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MIN_LIMIT = 1;

    /**
     * The maximum number of Tweets supported without pagination
     *
     * @since 2.0.0
     *
     * @type int
     */
    const MAX_LIMIT = 20;

    /**
     * Chrome option token for no header display
     *
     * @since 2.0.0
     *
     * @type string
     */
    const CHROME_NOHEADER = 'noheader';

    /**
     * Chrome option token for no header display
     *
     * @since 2.0.0
     *
     * @type string
     */
    const CHROME_NOFOOTER = 'nofooter';

    /**
     * Chrome option token for no borders display
     *
     * @since 2.0.0
     *
     * @type string
     */
    const CHROME_NOBORDERS = 'noborders';

    /**
     * Chrome option token for no embedded timeline scrollbar
     *
     * @since 2.0.0
     *
     * @type string
     */
    const CHROME_NOSCROLLBAR = 'noscrollbar';

    /**
     * Chrome option token for transparent background
     *
     * @since 2.0.0
     *
     * @type string
     */
    const CHROME_TRANSPARENT = 'transparent';

    /**
     * Tweets added to an embedded timeline should only be announced if the user is not currently doing anything
     *
     * @since 2.0.0
     *
     * @type string
     */
    const ARIA_POLITE_POLITE = 'polite';

    /**
     * Tweets added to an embedded timeline are important enough to be announced to the user as soon as possible, but it is not necessary to immediately interrupt the user
     *
     * @since 2.0.0
     *
     * @type string
     */
    const ARIA_POLITE_ASSERTIVE = 'assertive';

    /**
     * Available values for customizing embedded timeline chrome
     *
     * @since 2.0.0
     *
     * @type array {
     *   @type string valid value
     *   @type bool unused
     * }
     */
    public static $CHROME_OPTIONS = array(
        self::CHROME_NOHEADER    => true,
        self::CHROME_NOFOOTER    => true,
        self::CHROME_NOBORDERS   => true,
        self::CHROME_NOSCROLLBAR => true,
        self::CHROME_TRANSPARENT => true
    );

    /**
     * Maximum display width of the widget
     *
     * @since 2.0.0
     *
     * @type int
     */
    protected $width;

    /**
     * Maximum display height of the widget
     *
     * @since 2.0.0
     *
     * @type int
     */
    protected $height;

    /**
     * Display a specific number of Tweets, disabling auto-pagination of possible results
     *
     * @since 2.0.0
     *
     * @type int
     */
    protected $limit;

    /**
     * Display customizations for the widget container
     *
     * @since 2.0.0
     *
     * @type array {
     *   @type string chrome customization
     *   @type bool unused
     * }
     */
    protected $chrome = array();

    /**
     * The politeness / priority of the embedded timeline ARIA live region
     *
     * @since 2.0.0
     *
     * @link https://www.w3.org/TR/wai-aria/states_and_properties#aria-live
     *
     * @type string
     */
    protected $aria_politeness = self::ARIA_POLITE_POLITE;

    /**
     * Tests a supplied width for validity
     *
     * @since 2.0.0
     *
     * @param int $width the maximum display width of an embedded timeline in whole pixels
     *
     * @return bool true if supplied width is an integer in the range of MIN_WIDTH and MAX_WIDTH inclusive
     */
    public static function isValidWidth($width)
    {
        return (is_int($width) && $width>=static::MIN_WIDTH && $width<=static::MAX_WIDTH);
    }

    /**
     * Set the maximum display width of an embedded timeline in whole pixels
     *
     * Must be an integer in the allowed range: 180-1200 inclusive
     *
     * @since 2.0.0
     *
     * @param int $width the maximum display width of an embedded timeline in whole pixels
     *
     * @return self allow chaining
     */
    public function setWidth($width)
    {
        if (static::isValidWidth($width)) {
            $this->width = $width;
        }

        return $this;
    }

    /**
     * Tests a supplied height for validity
     *
     * @since 2.0.0
     *
     * @param int $height height of the embedded timeline in whole pixels
     *
     * @return bool true if height is an integer greater than or equal to the minimum allowed height
     */
    public static function isValidHeight($height)
    {
        return (is_int($height) && $height>=static::MIN_HEIGHT);
    }

    /**
     * Set the maximum display height of an embedded timeline in whole pixels
     *
     * Must be an integer greater than or equal to MIN_HEIGHT
     *
     * @since 2.0.0
     *
     * @param int $height the maximum display height of an embedded timeline in whole pixels
     *
     * @return self allow chaining
     */
    public function setHeight($height)
    {
        if (static::isValidHeight($height)) {
            $this->height = $height;
        }

        return $this;
    }

    /**
     * Tests a supplied limit for validity
     *
     * @since 2.0.0
     *
     * @param int $limit maximum number of Tweets to display in a timeline without pagination
     *
     * @return bool true if supplied limit is an integer and within the allowed range inclusive
     */
    public static function isValidLimit($limit)
    {
        return ( is_int($limit) && $limit >=static::MIN_LIMIT && $limit<=static::MAX_LIMIT );
    }

    /**
     * Set the maximum number of Tweets to display in the embedded timeline
     *
     * @param int $limit maximum number of Tweets to display
     *
     * @return self support chaining
     */
    public function setLimit($limit)
    {
        if (static::isValidLimit($limit)) {
            $this->limit = $limit;
        }

        return $this;
    }

    /**
     * Is the provided chrome option supported by embedded timelines?
     *
     * @since 2.0.0
     *
     * @param string $option chrome option
     *
     * @return bool true if provided option is a known chrome option
     */
    public static function isValidChromeOption($option)
    {
        return ( $option && isset(static::$CHROME_OPTIONS[$option]) );
    }

    /**
     * Set chrome preferences from a list of possible chrome tokens
     *
     * @since 2.0.0
     *
     * @param array $chrome chrome preference tokens {
     *   @type string chrome preference token
     *   @type bool not used
     * }
     *
     * @return self support chaining
     */
    public function setChrome($chrome)
    {
        if (! is_array($chrome) || empty($chrome)) {
            return $this;
        }

        $keys = null;
        if (isset($chrome[0])) {
            $keys = $chrome;
        } else {
            $keys = array_keys($chrome);
        }
        if ($keys && is_array($keys)) {
            array_walk($keys, array($this, 'setChromeOption'));
        }

        return $this;
    }

    /**
     * Possibly set a chrome array value from a valid passed chrome option
     *
     * @since 2.0.0
     *
     * @param string $chrome_option chrome option
     *
     * @return bool option set
     */
    protected function setChromeOption($chrome_option)
    {
        if (is_string($chrome_option)) {
            $chrome_option = strtolower(trim($chrome_option));
            if ($chrome_option && static::isValidChromeOption($chrome_option) && ! isset($this->chrome[$chrome_option])) {
                $this->chrome[$chrome_option] = true;
                return true;
            }
        }

        return false;
    }

    /**
     * Do not include a header component of the embedded timeline
     *
     * Sites are expected to include their own header to introduce the datasource and link to its equivalent location on Twitter.com
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function hideHeader()
    {
        if (! isset($this->chrome[static::CHROME_NOHEADER])) {
            $this->chrome[static::CHROME_NOHEADER] = true;
        }

        return $this;
    }

    /**
     * Include a header component of the embedded timeline (default option)
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function showHeader()
    {
        unset($this->chrome[static::CHROME_NOHEADER]);

        return $this;
    }

    /**
     * Do not include a footer component of the embedded timeline
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function hideFooter()
    {
        if (! isset($this->chrome[static::CHROME_NOFOOTER])) {
            $this->chrome[static::CHROME_NOFOOTER] = true;
        }

        return $this;
    }

    /**
     * Include a footer component of the embedded timeline (default option)
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function showFooter()
    {
        unset($this->chrome[static::CHROME_NOFOOTER]);

        return $this;
    }

    /**
     * Do not include a border separating Tweets displayed in the embedded timeline
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function hideBorders()
    {
        if (! isset($this->chrome[static::CHROME_NOBORDERS])) {
            $this->chrome[static::CHROME_NOBORDERS] = true;
        }

        return $this;
    }

    /**
     * Include a border separating Tweets displayed in the embedded timeline (default option)
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function showBorders()
    {
        unset($this->chrome[static::CHROME_NOBORDERS]);

        return $this;
    }

    /**
     * Do not include a footer component of the embedded timeline
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function hideScrollbar()
    {
        if (! isset($this->chrome[static::CHROME_NOSCROLLBAR])) {
            $this->chrome[static::CHROME_NOSCROLLBAR] = true;
        }

        return $this;
    }

    /**
     * Include a visual scrollbar to assist navigating Tweets in an embedded timeline exceeding the visible widget area (default option)
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function showScrollbar()
    {
        unset($this->chrome[static::CHROME_NOSCROLLBAR]);

        return $this;
    }

    /**
     * Hide the widget theme's background by setting it to transparent
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function hideThemeBackground()
    {
        if (! isset($this->chrome[static::CHROME_TRANSPARENT])) {
            $this->chrome[static::CHROME_TRANSPARENT] = true;
        }

        return $this;
    }

    /**
     * Display an embedded timeline with the default background color for the chosen theme (default)
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function showThemeBackground()
    {
        unset($this->chrome[static::CHROME_TRANSPARENT]);

        return $this;
    }

    /**
     * Set an ARIA live region politeness value for the embedded timeline
     *
     * @since 2.0.0
     *
     * @param string $politeness ARIA live region politeness setting
     *
     * @return self support chaining
     */
    public function setAriaLive($politeness)
    {
        if (is_string($politeness)) {
            $politeness = strtolower(trim($politeness));
            if ($politeness === static::ARIA_POLITE_ASSERTIVE) {
                $this->setAriaLiveAssertive();
            }
        }

        return $this;
    }

    /**
     * New Tweets are important enough to be announced to the user as soon as possible, but it is not necessary to immediately interrupt the user
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function setAriaLiveAssertive()
    {
        $this->aria_politeness = static::ARIA_POLITE_ASSERTIVE;

        return $this;
    }

    /**
     * Populate base options from a passed associative array
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
    public function setBaseOptions($options)
    {
        parent::setBaseOptions($options);
        $this->setThemeOptions($options);

        if (isset($options['width']) && $options['width']) {
            $this->setWidth($options['width']);
        }

        if (isset($options['aria-polite']) && $options['aria-polite']) {
            $this->setAriaLive($options['aria-polite']);
        }

        if (isset($options['limit']) && $options['limit']) {
            $this->setLimit($options['limit']);
        } elseif (isset($options['height']) && $options['height']) {
            $this->setHeight($options['height']);
        }

        if (isset($options['chrome']) && is_array($options['chrome']) && !empty($options['chrome'])) {
            $this->setChrome($options['chrome']);
        }

        return $this;
    }

    /**
     * Convert the class object into an array, removing default field values
     *
     * @since 2.0.0
     *
     * @return array properties as associative array
     */
    public function toArray()
    {
        $data = parent::toArray();
        $data = array_merge($data, $this->themeToArray());

        if (isset($this->width)) {
            $data['width'] = $this->width;
        }

        // limit sets height
        if (isset($this->limit)) {
            $data['tweet-limit'] = $this->limit;
            // scroll bar is not used on expanded timeline display triggered by limit
            unset($this->chrome[static::CHROME_NOSCROLLBAR]);
            $this->aria_politeness = null;
        } elseif (isset($this->height)) {
            $data['height'] = $this->height;
        }

        if (!empty($this->chrome)) {
            $data['chrome'] = array_keys($this->chrome);
        }

        if ($this->aria_politeness && $this->aria_politeness !== static::ARIA_POLITE_POLITE) {
            $data['aria-polite'] = $this->aria_politeness;
        }

        return $data;
    }

    /**
     * Output timeline as an array suitable for use as oEmbed query parameters
     *
     * @since 2.0.0
     *
     * @return array profile timeline parameter array {
     *   @type string query parameter name
     *   @type string query parameter value
     * }
     */
    public function toOEmbedParameterArray()
    {
        $query_parameters = parent::toArray();
        $query_parameters = array_merge($query_parameters, $this->themeToOEmbedParameterArray());

        if (isset($this->width)) {
            $query_parameters['maxwidth'] = $this->width;
        }

        if (isset($this->limit)) {
            $query_parameters['limit'] = $this->limit;
            // scroll bar is not used on expanded timeline display triggered by limit
            unset($this->chrome[static::CHROME_NOSCROLLBAR]);
            $this->aria_politeness = null;
        } elseif (isset($this->height)) {
            $query_parameters['maxheight'] = $this->height;
        }

        if (isset($this->chrome)) {
            $query_parameters['chrome'] = implode(' ', array_keys($this->chrome));
        }

        if ($this->aria_politeness && $this->aria_politeness !== static::ARIA_POLITE_POLITE) {
            $query_parameters['aria_polite'] = $this->aria_politeness;
        }

        return $query_parameters;
    }
}
