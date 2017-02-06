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

namespace Twitter\Widgets\Embeds\Timeline;

/**
 * Display Tweets published within the last ~7 days for a given widget ID
 *
 * @since 2.0.0
 */
class Search extends \Twitter\Widgets\Embeds\Timeline
{
    /**
     * HTML class expected by the Twitter widget JS
     *
     * @since 2.0.0
     *
     * @type string
     */
    const HTML_CLASS = 'twitter-timeline';

    /**
     * Construct a full Twitter URI by appending to base string
     *
     * @since 2.0.0
     *
     * @type string
     */
    const BASE_URL = 'https://twitter.com/search';

    /**
     * Twitter widgets setting URL including a widget ID as a path component
     *
     * @since 2.0.0
     *
     * @type string
     */
    const SETTINGS_URL_REGEX = '#^https://twitter\.com/settings/widgets/([0-9]+)/edit$#i';

    /**
     * Widget ID configured on Twitter.com
     *
     * @since 2.0.0
     *
     * @see https://twitter.com/settings/widgets/new/search Search widget configuration
     *
     * @type string
     */
    protected $widget_id;

    /**
     * Search terms set in the widget ID
     *
     * Used to construct a meaningful link to a search results page and relevant link text.
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $search_terms;

    /**
     * Create a new search timeline widget with a required widget ID and optional search terms
     *
     * @since 2.0.0
     *
     * @param string $widget_id widget identifier
     * @param string $search_terms (optional) search terms for display in link
     */
    public function __construct($widget_id, $search_terms = '')
    {
        $this->setWidgetID($widget_id);
        if ($search_terms) {
            $this->setSearchTerms($search_terms);
        }
    }

    /**
     * Extract a widget ID from a Twitter.com widgets setting URL
     *
     * @since 2.0.0
     *
     * @param string $url Twitter.com widgets setting URL
     *
     * @return string widget ID or empty string if none found
     */
    public static function getWidgetIDFromSettingsURL($url)
    {
        if (! (is_string($url) && $url)) {
            return '';
        }
        $url = trim($url);
        $matches = array();
        preg_match(static::SETTINGS_URL_REGEX, $url, $matches);
        if (is_array($matches) && isset($matches[1]) && $matches[1]) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Test widget ID validity
     *
     * @since 2.0.0
     *
     * @param string $id widget ID
     *
     * @return bool true if valid widget ID
     */
    public static function isValidWidgetID($id)
    {
        if (is_string($id)) {
            if (function_exists('ctype_digit')) {
                return ctype_digit($id);
            } else {
                return (bool) (preg_match("/^[0-9]+$/", $id));
            }
        }

        return false;
    }

    /**
     * Get a widget ID corresponding to a saved search
     *
     * @since 2.0.0
     *
     * @return string widget ID if set else empty string
     */
    public function getWidgetID()
    {
        return $this->widget_id ?: '';
    }

    /**
     * Set a widget ID corresponding to a saved search
     *
     * @since 2.0.0
     *
     * @param string $widget_id widget identifier
     *
     * @return self support chaining
     */
    public function setWidgetID($widget_id)
    {
        if (is_string($widget_id)) {
            $widget_id = trim($widget_id);
            if (static::isValidWidgetID($widget_id)) {
                $this->widget_id = $widget_id;
            }
        }

        return $this;
    }

    /**
     * Get search terms for more meaningful fallback display
     *
     * Should match the search terms stored for the widget ID. Does not affect rendered widget
     *
     * @since 2.0.0
     *
     * @return string search terms or empty string if no search terms set
     */
    public function getSearchTerms()
    {
        return $this->search_terms ?: '';
    }

    /**
     * Set search terms used by markup builder for more meaningful fallback display
     *
     * Should match search terms stored for the widget ID. Does not affect rendered widget.
     *
     * @since 2.0.0
     *
     * @param string $terms search terms
     *
     * @return self support chaining
     */
    public function setSearchTerms($terms)
    {
        if (is_string($terms)) {
            $terms = trim($terms);
            if ($terms) {
                $this->search_terms = $terms;
            }
        }

        return $this;
    }

    /**
     * Build a URL to a Twitter search page, with terms if set
     *
     * @since 2.0.0
     *
     * @return string Twitter search URL
     */
    public function getURL()
    {
        $url = static::BASE_URL;
        $search_terms = $this->getSearchTerms();
        if ($search_terms) {
            $url .= '?' . http_build_query(
                array( 'q' => $search_terms ),
                '',
                '&',
                PHP_QUERY_RFC3986
            );
        }

        return $url;
    }

    /**
     * Create a search timeline object from an associative array
     *
     * @since 2.0.0
     *
     * @param array $options {
     *   @type string          parameter name
     *   @type string|int|bool parameter value
     * }
     *
     * @return self|null new Search object with configured properties or null if minimum requirements not met
     */
    public static function fromArray($options)
    {
        // widget ID required
        if (! (is_array($options) && isset($options['widget_id']))) {
            return null;
        }

        if (! (is_string($options['widget_id']) && $options['widget_id'])) {
            return null;
        }
        // parse a widget ID from a Twitter.com widget settings URL
        if (strlen($options['widget_id']) > 7 && 0 === substr_compare('https://', $options['widget_id'], 0, 8, true)) {
            $options['widget_id'] = static::getWidgetIDFromSettingsURL($options['widget_id']);
            if (!$options['widget_id']) {
                return null;
            }
        }

        $search_terms = '';
        if (isset($options['terms']) && is_string($options['terms']) && $options['terms']) {
            $search_terms = $options['terms'];
        }

        $class = get_called_class();
        $timeline = new $class( $options['widget_id'], $search_terms );

        if (method_exists($timeline, 'setBaseOptions')) {
            $timeline->setBaseOptions($options);
        }

        return $timeline;
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

        if ($this->widget_id) {
            $data['widget-id'] = $this->widget_id;
        }

        // always include theme value. may override value stored with widget
        if ($this->theme) {
            $data['theme'] = $this->theme;
        }

        return $data;
    }

    /**
     * Disabled. oEmbed is not supported for a search timeline widget
     *
     * @since 2.0.0
     *
     * @return array empty array
     */
    public function toOEmbedParameterArray()
    {
        // not supported
        return array();
    }

    /**
     * Generate HTML as fallback markup for the Twitter for Websites JavaScript
     *
     * @since 2.0.0
     *
     * @param string $anchor_text inner text of the generated anchor element. Supports a single '%s' screen name passed through sprintf. Default: Follow %s
     * @param string $html_builder_class callable HTML builder with a static anchorElement class
     *
     * @return string HTML markup or empty string if minimum requirements not met
     */
    public function toHTML($anchor_text = 'Tweets about %s', $html_builder_class = '\Twitter\Helpers\HTMLBuilder')
    {
        if (! ( is_string($anchor_text) && $anchor_text )) {
            return '';
        }

        // test for invalid passed class
        if (! ( class_exists($html_builder_class) && method_exists($html_builder_class, 'anchorElement') )) {
            return '';
        }

        $search_url = $this->getURL();
        $search_terms = $this->getSearchTerms();
        if ($search_terms) {
            $anchor_text = sprintf($anchor_text, $search_terms);
        } else {
            $anchor_text = 'Twitter Search';
        }

        return $html_builder_class::anchorElement(
            $search_url,
            $anchor_text,
            array(
                'class' => static::HTML_CLASS,
            ),
            $this->toArray()
        );
    }
}
