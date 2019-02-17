<?php
/**
 * Singleton Trait for classes.
 *
 * This trait allows you to keep a single instance of a class available. Calling
 * `get_instance` will either create a new instance of the class using Late
 * Static Binding, or return the already created instance.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios.com>
 * @package cc-woo
 */

namespace WDS\Util;

trait SingletonTrait {
	/**
	 * This class's single instance.
	 *
	 * @since 0.0.1
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get the single instance of this class.
	 *
	 * N.B. The use of `static` provides Late Static Binding to the calling class.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param array $args Array of arguments to pass to constructor.
	 * @return object
	 */
	public static function get_instance( $args = [] ) {
		$called_class = get_called_class();

		if ( ! ( static::$instance instanceof $called_class ) ) {
			static::$instance = new static( $args );
		}

		return static::$instance;
	}
}
