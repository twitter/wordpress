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

namespace Twitter\Widgets\Buttons\Periscope;

/**
 * Periscope profile button with on-air status display
 *
 * @since 2.0.0
 */
class OnAir extends \Twitter\Widgets\Base
{
    /**
     * HTML class expected by the Periscope widget JS
     *
     * @since 2.0.0
     *
     * @type string
     */
    const HTML_CLASS = 'periscope-on-air';


    /**
     * Default button size
     *
     * @since 2.0.0
     *
     * @type string
     */
    const DEFAULT_SIZE = 'small';

    /**
     * Periscope web profile base URL
     *
     * @since 2.0.0
     *
     * @type string
     */
    const BASE_URL = 'https://periscope.tv/';

    /**
     * Periscope username
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $username;

    /**
     * Allowed values for the size property
     *
     * @since 2.0.0
     *
     * @type array allowed sizes {
     *   @type string size
     *   @type bool exists
     * }
     */
    public static $ALLOWED_SIZES = array( 'small' => true, 'large' => true );

    /**
     * Size of the button
     *
     * @since 2.0.0
     *
     * @type string
     */
    protected $size;

    /**
     * Require username
     *
     * @param string $username target username
     * @param bool   $validate validate inputs such as username before storing
     *
     * @since 2.0.0
     */
    public function __construct($username, $validate = true)
    {
        $username = \Twitter\Helpers\Validators\PeriscopeUsername::trim($username);
        if (false === $validate || \Twitter\Helpers\Validators\PeriscopeUsername::isValid($username)) {
            $this->username = $username;
        }
    }

    /**
     * Retrieve the stored Periscope username
     *
     * @since 2.0.0
     *
     * @return string Periscope username or empty string if username not set
     */
    public function getUsername()
    {
        return $this->username ?: '';
    }

    /**
     * Build a Periscope web profile URL
     *
     * @since 2.0.0
     *
     * @return string Periscope web profile URL or empty string if username not set
     */
    public function getWebProfileURL()
    {
        return $this->username ? static::BASE_URL . $this->username : '';
    }

    /**
     * Set the desired size of the On Air button
     *
     * @since 2.0.0
     *
     * @param string $size button size
     *
     * @return self support chaining
     */
    public function setSize($size)
    {
        if ($size && isset(static::$ALLOWED_SIZES[$size])) {
            $this->size = $size;
        }
        return $this;
    }

    /**
     * Build a Periscope On Air object from an associative array
     *
     * @since 2.0.0
     *
     * @param array $options associative array of options {
     *   @type string option name
     *   @type string option value
     * }
     *
     * @return self|null new OnAir object of null if minimum requirements not met
     */
    public static function fromArray($options)
    {
        if (! isset($options['username']) && $options['username']) {
            return null;
        }

        $class = get_called_class();
        $on_air = new $class( $options['username'] );
        unset($class);

        $on_air->setBaseOptions($options);

        if (isset($options['size']) && static::DEFAULT_SIZE !== $options['size']) {
            $on_air->setSize($options['size']);
        }

        return $on_air;
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

        if ($this->size && static::DEFAULT_SIZE !== $this->size) {
            $data['size'] = $this->size;
        }

        return $data;
    }

    /**
     * Generate a link to a Periscope web profile configured for enhancement by the Twitter for Websites JavaScript
     *
     * @since 2.0.0
     *
     * @param string $html_builder_class callable HTML builder with a static anchorElement class
     *
     * @return string HTML markup or empty string if minimum requirements not met
     */
    public function toHTML($html_builder_class = '\Twitter\Helpers\HTMLBuilder')
    {
        // test for invalid passed class
        if (! ( class_exists($html_builder_class) && method_exists($html_builder_class, 'anchorElement') )) {
            return '';
        }

        $web_profile_url = $this->getWebProfileURL();
        if (! $web_profile_url) {
            return '';
        }

        return $html_builder_class::anchorElement(
            $web_profile_url,
            '@' . $this->getUsername(),
            array(
                'class' => static::HTML_CLASS,
            ),
            $this->toArray()
        );
    }
}
