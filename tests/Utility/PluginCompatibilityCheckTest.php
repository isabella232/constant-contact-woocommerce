<?php
/**
 * Tests for the PluginCompatibilityCheck class.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Test\Utility
 * @since   2019-03-20
 */

namespace WebDevStudios\CCForWoo\Test\Utility;

use WebDevStudios\CCForWoo\Utility\PluginCompatibilityCheck;
use WP_Mock\Tools\TestCase;

/**
 * Class PluginCompatibilityCheckTest
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Test\Utility
 * @since   2019-03-20
 */
class PluginCompatibilityCheckTest extends TestCase {
	/**
	 * Assert that classes are available when they in fact exist.
	 *
	 * @test
	 */
	public function is_available_returns_true_when_class_exists() {
		$checker = new PluginCompatibilityCheck( \WooCommerce::class );

		$this->assertTrue( $checker->is_available() );
	}

	/**
	 * Assert that classes are not available when they do not exist.
	 *
	 * @test
	 */
	public function is_available_returns_false_when_class_not_found() {
		$checker = new PluginCompatibilityCheck( 'AMadeUpClass' );

		$this->assertFalse( $checker->is_available() );
	}

	/**
	 * Assert that the compatibility checker returns false if WooCommerce is below the compatible version.
	 *
	 * @test
	 */
	public function is_compatible_returns_false_if_woocommerce_version_is_below_minimum_requirement() {
		$woocommerce          = \WooCommerce::instance();
		$woocommerce->version = '3.0.0';

		$checker = new PluginCompatibilityCheck( 'WooCommerce' );

		$this->assertFalse( $checker->is_compatible( $woocommerce ) );
	}

	/**
	 * Assert that the plugin is compatible if the version is the same as WooCommerce's.
	 *
	 * Note: This test will fail and will need to be updated if the minimum version of Woo is bumped.
	 *
	 * @test
	 */
	public function is_compatible_returns_true_if_equal_to_minimum_version() {
		$woocommerce          = \WooCommerce::instance();
		$woocommerce->version = '3.5.4';
		$checker              = new PluginCompatibilityCheck( 'WooCommerce' );

		$this->assertTrue( $checker->is_compatible( $woocommerce ) );
	}
}
