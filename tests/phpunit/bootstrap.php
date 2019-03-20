<?php
require_once __DIR__ . '/../../vendor/autoload.php';

\WP_Mock::bootstrap();

/**
 * Class WooCommerce
 *
 * Mock class of WooCommerce to help with some tests, since it's not available otherwise.
 *
 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since  2019-03-20
 */
class WooCommerce {
	/**
	 * Instance of this WooCommerce class.
	 *
	 * @var WooCommerce
	 * @since 2019-03-20
	 */
	private static $instance;

	/**
	 * Faux singleton class that WooCommerce usually expects for instantiation.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-20
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}