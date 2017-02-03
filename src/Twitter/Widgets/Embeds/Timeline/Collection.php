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

namespace Twitter\Widgets\Embeds\Timeline;

/**
 * Display Tweets inside a collection
 *
 * @since 2.0.0
 */
class Collection extends \Twitter\Widgets\Embeds\Timeline
{
    /**
     * Construct a full Twitter URI by appending to base string
     *
     * @since 2.0.0
     *
     * @type string
     */
    const BASE_URL = 'https://twitter.com/_/timelines/';

    /**
     * Grid display template
     *
     * @since 2.0.0
     *
     * @type string
     */
    const WIDGET_TYPE_GRID = 'grid';

    /**
     * Fields supported in a vertical template not supported in a grid template
     *
     * @since 2.0.0
     *
     * @type array data-* key, oEmbed value
     */
    public static $FIELDS_NOT_SUPPORTED_IN_GRID = array(
      'height'       => 'maxheight',    // auto-expands to limit
      'aria-polite'  => 'aria_polite',  // grid is not a live region
      'theme'        => 'theme',        // always light text on dark theme
      'link-color'   => 'link_color',   // text color
      'border-color' => 'border_color',
    );

    /**
     * Collection unique identifier
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $id;

    /**
     * The display template used to display Tweets in the collection
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $widget_type;

    /**
     * Create a new collection object
     *
     * @since 2.0.0
     *
     * @param string $id unique identifier of the collection
     */
    public function __construct($id)
    {
        if (is_string($id) && $id) {
            $this->setID($id);
        }
    }

    /**
     * Test ID validity
     *
     * @since 2.0.0
     *
     * @link https://dev.twitter.com/overview/api/twitter-ids-json-and-snowflake
     *
     * @param string $id snowflake ID
     *
     * @return bool true if valid snowflake ID
     */
    public static function isValidSnowflakeID($id)
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
     * Get the unique collection identifier
     *
     * @since 2.0.0
     *
     * @return string the unique collection identifier or empty string if not set
     */
    public function getID()
    {
        return $this->id ?: '';
    }

    /**
     * Set the unique identifier of the collection
     *
     * @since 2.0.0
     *
     * @param string $id unique identifier of the collection
     *
     * @return self support chaining
     */
    public function setID($id)
    {
        if (is_string($id)) {
            $id = trim($id);
            if (static::isValidSnowflakeID($id)) {
                $this->id = $id;
            }
        }

        return $this;
    }

    /**
     * Twitter collection URL
     *
     * @since 2.0.0
     *
     * @return string Twitter collection URL or empty string if no ID set
     */
    public function getURL()
    {
        if ($this->id) {
            return static::BASE_URL . $this->id;
        }

        return '';
    }

    /**
     * Use the grid display template
     *
     * @since 2.0.0
     *
     * @return self support chaining
     */
    public function setGridTemplate()
    {
        $this->widget_type = static::WIDGET_TYPE_GRID;

        return $this;
    }

    /**
     * Create a collection object from an associative array
     *
     * @since 2.0.0
     *
     * @param array $options {
     *   @type string          parameter name
     *   @type string|int|bool parameter value
     * }
     *
     * @return self|null new Collection object with configured properties
     */
    public static function fromArray($options)
    {
        // id required
        if (! (is_array($options) && isset($options['id']))) {
            return null;
        }

        if (! (is_string($options['id']) && $options['id'])) {
            return null;
        }

        $class = get_called_class();
        $timeline = new $class( $options['id'] );
        unset($class);
        unset($options['id']);

        if (method_exists($timeline, 'setBaseOptions')) {
            $timeline->setBaseOptions($options);
        }

        if (isset($options['widget_type']) && static::WIDGET_TYPE_GRID === $options['widget_type']) {
            if (method_exists($timeline, 'setGridTemplate')) {
                $timeline->setGridTemplate();
            }
        }

        return $timeline;
    }

    /**
     * Return collection parameters suitable for conversion to data-*
     *
     * @since 2.0.0
     *
     * @return array collection parameter array {
     *   @type string dashed parameter name
     *   @type string parameter value
     * }
     */
    public function toArray()
    {
        $data = parent::toArray();

        if ($this->id) {
            $data['id'] = $this->id;
        } else {
            return array();
        }

        if ($this->widget_type) {
            if (static::WIDGET_TYPE_GRID === $this->widget_type) {
                $data['widget-type'] = $this->widget_type;

                $unsupported_fields = array_keys(static::$FIELDS_NOT_SUPPORTED_IN_GRID);

                foreach ($unsupported_fields as $key) {
                    unset($data[$key]);
                }

                // only footer applies. footer is transparent
                if (array_key_exists(static::CHROME_NOFOOTER, $this->chrome)) {
                    $data['chrome'] = array(static::CHROME_NOFOOTER);
                } else {
                    unset($data['chrome']);
                }

                // grid parameter differs from standard timeline parameter
                if (array_key_exists('tweet-limit', $data)) {
                    $data['limit'] = $data['tweet-limit'];
                    unset($data['tweet-limit']);
                }
            }
        }

        return $data;
    }

    /**
     * Output timeline as an array suitable for use as oEmbed query parameters
     *
     * @since 2.0.0
     *
     * @return array collection parameter array {
     *   @type string query parameter name
     *   @type string query parameter value
     * }
     */
    public function toOEmbedParameterArray()
    {
        $query_parameters = parent::toOEmbedParameterArray();

        $url = $this->getURL();
        if (! $url) {
            return array();
        }
        $query_parameters['url'] = $url;

        if ($this->widget_type) {
            if (static::WIDGET_TYPE_GRID === $this->widget_type) {
                $query_parameters['widget_type'] = static::WIDGET_TYPE_GRID;

                $unsupported_parameters = array_values(static::$FIELDS_NOT_SUPPORTED_IN_GRID);
                foreach ($unsupported_parameters as $key) {
                    unset($query_parameters[$key]);
                }
                unset($unsupported_parameters);

                // only footer applies. footer is transparent
                if (array_key_exists(static::CHROME_NOFOOTER, $this->chrome)) {
                    $query_parameters['chrome'] = array(static::CHROME_NOFOOTER);
                } else {
                    unset($query_parameters['chrome']);
                }
            }
        }

        return $query_parameters;
    }
}
