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

namespace Twitter\Tests\WordPress\Shortcodes\Embeds;

/**
 * @group shortcode
 * @coversDefaultClass \Twitter\WordPress\Shortcodes\Embeds\Timeline
 */
final class Timeline extends \WP_UnitTestCase
{
    /**
     * Set up a widget object for each test
     *
     * @since 2.0.0
     *
     * @type \Twitter\WordPress\Shortcodes\Embeds\Timeline
     */
    protected $widget;

    /**
     * Initialize a new Theme object for use in each test
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->widget = $this->getMockForTrait('\Twitter\WordPress\Shortcodes\Embeds\Timeline');
    }

    /**
     * Test converting string attributes parsed by WordPress shortcode API into an associative array used to create a new timeline object
     *
     * @since 2.0.0
     *
     * @covers ::shortcodeAttributesToTimelineKeys
     *
     * @return void
     */
    public function testShortcodeAttributesToTimelineKeys()
    {
        $class = $this->widget;
        $this->assertEquals(array(), $class::shortcodeAttributesToTimelineKeys(42), 'Failed to return empty array for invalid passed options');

        $width = 400;
        $height = 300;
        $limit = 5;
        $clean = $class::shortcodeAttributesToTimelineKeys(array( 'width' => strval($width), 'height' => strval($height), 'limit' => strval($limit)));
        $this->assertArrayHasKey('width', $clean, 'Width value not returned from attribute cleaner');
        $this->assertEquals($width, $clean['width'], 'Width value not properly converted');
        $this->assertArrayHasKey('height', $clean, 'Height value not returned from attribute cleaner');
        $this->assertEquals($height, $clean['height'], 'Height value not properly converted');
        $this->assertArrayHasKey('limit', $clean, 'Limit value not returned from attribute cleaner');
        $this->assertEquals($limit, $clean['limit'], 'Limit value not properly converted');
        unset($width, $height, $limit);
        unset($clean);

        $aria_live = 'assertive';
        $link_color = '21759b';
        $border_color = 'd54e21';
        $clean = $class::shortcodeAttributesToTimelineKeys(array('aria_polite' => $aria_live, 'link_color' => $link_color, 'border_color' => $border_color));
        $this->assertArrayHasKey('aria-polite', $clean, 'Dashed ARIA polite value not returned from attribute cleaner');
        $this->assertEquals($aria_live, $clean['aria-polite'], 'ARIA polite value not properly converted');
        $this->assertArrayHasKey('link-color', $clean, 'Link color value not returned from attribute cleaner');
        $this->assertEquals($link_color, $clean['link-color'], 'Link color value not properly converted');
        $this->assertArrayHasKey('border-color', $clean, 'Border color value not returned from attribute cleaner');
        $this->assertEquals($border_color, $clean['border-color'], 'Border color value not properly converted');
        unset($aria_live, $link_color, $border_color);
        unset($clean);

        $clean = $class::shortcodeAttributesToTimelineKeys(array('chrome'=>'noheader,nofooter,transparent'));
        $this->assertArrayHasKey('chrome', $clean, 'Chrome value not returned from attribute cleaner');
        $this->assertEquals(array('noheader','nofooter','transparent'), $clean['chrome'], 'Chrome values not extracted during shortcode processing');
    }

    /**
     * Test generation of a unique cache key component based on timeline customization parameters
     *
     * @since 2.0.0
     *
     * @covers ::getOEmbedCacheKeyCustomParameters
     *
     * @return void
     */
    public function testGetOEmbedCacheKeyCustomParameters()
    {
        $class = $this->widget;
        $this->assertEquals('', $class::getOEmbedCacheKeyCustomParameters(array()), 'Failed to return an empty string for no query parameters passed');

        $maxwidth = 400;
        $maxheight = 300;
        $limit = 5;
        $link_color = '21759b';
        $border_color = 'd54e21';
        $query_parameters = array(
            'maxwidth'     => $maxwidth,
            'limit'        => $limit,
            'maxheight'    => $maxheight,
            'chrome'       => 'noheader nofooter noborders noscrollbar transparent',
            'aria_polite'  => 'assertive',
            'theme'        => 'dark',
            'link_color'   => $link_color,
            'border_color' => '#' . $border_color,
        );
        $cache_key = $class::getOEmbedCacheKeyCustomParameters($query_parameters);
        $cache_pieces = array(
            'w'.$maxwidth,
            'l'.$limit,
            'hfbst',
            'a',
            'd',
            'l'.$link_color,
            'b'.$border_color
        );
        $this->assertEquals(
            implode('_', $cache_pieces),
            $class::getOEmbedCacheKeyCustomParameters($query_parameters),
            'Cache key with limit and maxheight does not match expected value'
        );

        // remove limit, allowing maxheight
        unset($query_parameters['limit']);
        $cache_pieces[1] = 'h'.$maxheight;
        $this->assertEquals(
            implode('_', $cache_pieces),
            $class::getOEmbedCacheKeyCustomParameters($query_parameters),
            'Cache key does not match expected value'
        );
    }
}
