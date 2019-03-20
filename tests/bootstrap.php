<?php
require_once __DIR__ . '/../vendor/autoload.php';

\WP_Mock::bootstrap();

/**
 * Class WooCommerce
 *
 * A stub for the WooCommerce class.
 *
 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since  2019-03-20
 */
class WooCommerce {
	/**
	 * Instance of a WooCommerce object.
	 *
	 * @var WooCommerce
	 * @since 2019-03-20
	 */
	private static $instance;

	/**
	 * @var string
	 * @since 2019-03-20
	 */
	public $version = '3.5.4';

	/**
	 * Mock of the instance singleton getter for use by tests.
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
