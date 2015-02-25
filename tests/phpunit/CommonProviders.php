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

namespace Twitter\Tests;

final class CommonProviders
{
    /**
     * Truthy values that should be interpreted as boolean true
     *
     * @since 1.0.0
     *
     * @return array truthy values {
     *   @type array truthy test value, message
     * }
     */
    public static function truthyProvider()
    {
        return array(
            array( true, 'Did not accept boolean' ),
            array( 'true', 'Did not accept true in string form' ),
            array( 1, 'Did not accept true as int' ),
            array( '1', 'Did not accept true int in string' ),
        );
    }

    /**
     * Falsey values that should be interpreted as boolean false by our options reader
     *
     * @since 1.0.0
     *
     * @return array falsey values {
     *   @type array falsey test value, message
     * }
     */
    public static function falseyProvider()
    {
        return array(
            array( false, 'Did not accept boolean' ),
            array( 'false', 'Did not accept false in string form' ),
            array( 0, 'Did not accept false as int' ),
            array( '0', 'Did not accept false int in string' ),
        );
    }
}
