<?php
/**
 * Trait for Accessing non-public properties.
 *
 * Implementing classes should define `self::$accessible_fields` as an array of
 * properties that are private or protected, that you want access to publicly.
 *
 * @since 0.0.1
 * @package wds-utils
 */

namespace WDS\Util;

/**
 * Accessible Fields Trait
 *
 * @since 0.0.1
 */
trait AccessibleTrait {

	/**
	 * Magic getter - attempts to access inaccessible properties.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $key The property being accessed.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( self::is_accessible( $key ) ) {
			return $this->{$key};
		}
	}

	/**
	 * Magic static getter.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $key The property being accessed.
	 * @return mixed
	 */
	public static function __getStatic( $key ) {
		if ( self::is_accessible( $key ) ) {
			return self::$key;
		}
	}

	/**
	 * Determine if a given property is in the list of accessible properties.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $property The property being accessed.
	 * @return bool
	 */
	protected static function is_accessible( $property ) {
		return in_array( $property, self::$accessible_fields, true );
	}
}
