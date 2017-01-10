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

namespace Twitter\Tests\WordPress\Widgets\Embeds\Timeline;

/**
 * @group shortcode
 * @coversDefaultClass \Twitter\WordPress\Widgets\Embeds\Timeline\Profile
 */
final class Profile extends \WP_UnitTestCase
{
    /**
     * Set up a widget object for each test
     *
     * @since 2.0.0
     *
     * @type \Twitter\WordPress\Widgets\Embeds\Timeline\Profile
     */
    protected $widget;

    /**
     * Initialize a new Twitter\WordPress\Widgets\Embeds\Timeline\Profile object for use in each test
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function setUp()
    {
        $this->widget = new \Twitter\WordPress\Widgets\Embeds\Timeline\Profile();
    }

    /**
     * Test widget constructor
     *
     * @since 2.0.0
     *
     * @covers ::__construct
     * @small
     *
     * @return void
     */
    public function testConstructor()
    {
        $this->assertEquals(\Twitter\WordPress\Shortcodes\Embeds\Timeline\Profile::HTML_CLASS, $this->widget->id_base, 'Base ID not set');
        $this->assertEquals('Twitter Profile', $this->widget->name, 'Widget name not set');
    }

    /**
     * Test frontend output of widget
     *
     * @since 2.0.0
     *
     * @covers ::widget

    public function testWidget()
    {
        ob_start();
        $args = array(
            'before_widget' => '<section>',
            'after_widget' => "</section>\n",
            'before_title' => '<h2>',
            'after_title' => "</h2>\n",
        );
        $shortcode_stub = $this->getMock('\Twitter\WordPress\Shortcodes\Embeds\Timeline\Profile');
        $shortcode_stub->expects($this->any())
          ->method('shortcodeHandler')
          ->will($this->returnValue('<div></div>'));

        $instance = array('title' => 'Tweets', 'screen_name' => 'Twitter');
        $this->widget->_set( 2 );
        $this->widget->widget( $args, $instance );
        $output = ob_get_clean();

        $this->assertNotContains( 'no-options-widget', $output );
        $this->assertContains( '<h2>Tweets</h2>', $output, 'No title wrappers' );
        $this->assertContains( '<section>', $output, 'No customization before widget' );
        $this->assertContains( '</section>', $output, 'No customization after widget' );
    }*/
}
