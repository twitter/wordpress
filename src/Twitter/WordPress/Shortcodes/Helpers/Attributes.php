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

namespace Twitter\WordPress\Shortcodes\Helpers;

/**
 * Process and convert shortcode attributes into their expected PHP types
 *
 * @since 2.0.0
 */
class Attributes
{

	/**
	 * Accepted string attribute values for a true boolean
	 *
	 * @since 2.0.0
	 *
	 * type array
	 */
	public static $TRUTHY_STRING_VALUES = array(
		'true' => true,
		'yes'  => true,
		'on'   => true,
	);

	/**
	 * Accepted string attribute values for a false boolean
	 *
	 * @since 2.0.0
	 *
	 * type array
	 */
	public static $FALSEY_STRING_VALUES = array(
		'false' => false,
		'no'    => false,
		'off'   => false,
	);

	/**
	 * Convert shortcode attribute value to boolean
	 *
	 * @since 2.0.0
	 *
	 * @param array $attributes parsed shortcode attributes
	 * @param array $bool_keys  array keys of expected truthy or falsey values
	 *
	 * @return array $attributes shortcode attributes with possible type conversions or removed keys
	 */
	public static function booleanOption( array $attributes, $bool_keys )
	{
		if ( ! is_array( $bool_keys ) || empty( $bool_keys ) ) {
			return $attributes;
		}

		foreach ( $bool_keys as $bool_key ) {
			if ( ! isset( $attributes[ $bool_key ] ) || is_bool( $attributes[ $bool_key ] ) ) {
				continue;
			}

			// purposely allow int, string, or truthy/falsey loose match
			// @codingStandardsIgnoreStart WordPress.PHP.StrictComparisons.LooseComparison
			if ( '1' == $attributes[ $bool_key ] || (is_string( $attributes[ $bool_key ] ) && isset( static::$TRUTHY_STRING_VALUES[ strtolower( $attributes[ $bool_key ] ) ] ) ) ) {
				$attributes[ $bool_key ] = true;
			} else if ( '0' == $attributes[ $bool_key ] || (is_string( $attributes[ $bool_key ] ) && isset( static::$FALSEY_STRING_VALUES[ strtolower( $attributes[ $bool_key ] ) ] )) ) {
				$attributes[ $bool_key ] = false;
			} else {
				unset( $attributes[ $bool_key ] );
			}
			// @codingStandardsIgnoreEnd WordPress.PHP.StrictComparisons.LooseComparison
		}

		return $attributes;
	}

	/**
	 * Convert shortcode attribute value to positive integer
	 *
	 * @since 2.0.0
	 *
	 * @param array $attributes parsed shortcode attributes
	 * @param array $int_keys   array keys of expected positive integer values
	 *
	 * @return array $attributes shortcode attributes with possible type conversions or removed keys
	 */
	public static function positiveIntegerOption( array $attributes, $int_keys )
	{
		if ( ! is_array( $int_keys ) || empty( $int_keys ) ) {
			return $attributes;
		}

		foreach ( $int_keys as $int_key ) {
			if ( ! isset( $attributes[ $int_key ] ) ) {
				continue;
			}

			$attributes[ $int_key ] = absint( $attributes[ $int_key ] );
			if ( 0 === $attributes[ $int_key ] ) {
				unset( $attributes[ $int_key ] );
			}
		}

		return $attributes;
	}

	/**
	 * Remove whitespace and convert string attribute to lowercase ASCII characters
	 *
	 * @since 2.0.0
	 *
	 * @param array $attributes  parsed shortcode attributes
	 * @param array $string_keys array keys of expected positive integer values
	 *
	 * @return array $attributes shortcode attributes with possible type conversions, values to lowercase, or removed keys
	 */
	public static function lowercaseStringOption( array $attributes, $string_keys )
	{
		if ( ! is_array( $string_keys ) || empty( $string_keys ) ) {
			return $attributes;
		}

		foreach ( $string_keys as $string_key ) {
			if ( ! isset( $attributes[ $string_key ] ) ) {
				continue;
			}

			$attributes[ $string_key ] = strtolower( trim( $attributes[ $string_key ] ) );
			if ( ! $attributes[ $string_key ] ) {
				unset( $attributes[ $string_key ] );
			}
		}

		return $attributes;
	}
}
