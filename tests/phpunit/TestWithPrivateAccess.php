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

/**
 * Create reflection wrappers for private and protected access
 *
 * @since 1.0.0
 */
abstract class TestWithPrivateAccess extends \PHPUnit_Framework_TestCase
{
    /**
     * Get a private or protected property
     *
     * @since 1.0.0
     *
     * @param object $class initialized class
     * @param string $property_name class property name
     *
     * @return mixed the value of the property
     */
    public static function getProperty($class, $property_name)
    {
        if (! $property_name) {
            return;
        }

        $reflection = new \ReflectionClass($class);

        $property = $reflection->getProperty($property_name);
        $property->setAccessible(true);

        return $property->getValue($class);
    }

    /**
     * Set a private or protected property
     *
     * @since 1.0.0
     *
     * @param object $class initialized class
     * @param string $property_name class property name
     * @param mixed $value desired property value
     *
     * @return void
     */
    public static function setProperty($class, $property_name, $value)
    {
        if (! $property_name) {
            return;
        }

        $reflection = new \ReflectionClass($class);

        $property = $reflection->getProperty($property_name);
        $property->setAccessible(true);

        return $property->setValue($class, $value);
    }

    /**
     * Make a private or protected method available for testing
     *
     * @since 1.0.0
     *
     * @param object $class initialized class
     * @param string $method_name class method name
     *
     * @return ReflectionMethod
     */
    public static function getMethod($class, $method_name)
    {
        if (! $method_name) {
            return;
        }

        $reflection = new \ReflectionClass($class);
        $method = $reflection->getMethod($method_name);
        $method->setAccessible(true);

        return $method;
    }
}
