<?php
/**
 * WooCommerce Compatibility Class
 *
 * Tests to see if WooCommerce is available and compatible.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios.com>
 * @package cc-woo
 */

namespace ConstantContact\WooCommerce\Util;

/**
 * Tests if WooCommerce is available and compatible.
 *
 * @since 0.0.1
 */
class WooCompat {

	/**
	 * The minimum WooCommerce version.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	const MINIMUM_WOO_VERSION = '3.5.4';

	/**
	 * Check whether WooCommerce is available.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @return bool
	 */
	public static function is_woo_available() {
		return class_exists( '\WooCommerce' );
	}

	/**
	 * Check whether WooCommerce is compatible
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @return bool
	 */
	public static function is_woo_compatible() {
		$woo = \WooCommerce::instance();
		return 0 >= version_compare( self::MINIMUM_WOO_VERSION, $woo->version );
	}
}
