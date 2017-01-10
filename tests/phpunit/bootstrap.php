<?php
/*
The MIT License (MIT)

Copyright (c) 2015 Twitter Inc. and PHP Framework Interop Group

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

require_once(dirname(__FILE__) . '/autoload.php');

// discover the WordPress testing framework
$_tests_dir = getenv('WP_TESTS_DIR');
if (! $_tests_dir) {
    if (false !== getenv('WP_DEVELOP_DIR')) {
        $_tests_dir = getenv('WP_DEVELOP_DIR') . '/tests/phpunit';
    } elseif (file_exists('../../../../../tests/phpunit/includes/bootstrap.php')) {
        $_tests_dir = '../../../../../tests/phpunit';
    } elseif (file_exists('/tmp/wordpress-tests-lib/includes/bootstrap.php')) {
        $_tests_dir = '/tmp/wordpress-tests-lib';
    }
}

// @link https://core.trac.wordpress.org/browser/trunk/tests/phpunit/includes/functions.php
require_once $_tests_dir . '/includes/functions.php';

// activate the plugin
tests_add_filter('muplugins_loaded', function () {
    require_once((defined('TWITTER_PLUGIN_DIR') ? TWITTER_PLUGIN_DIR : dirname(dirname(__DIR__))) . '/twitter.php');
});

require $_tests_dir . '/includes/bootstrap.php';
