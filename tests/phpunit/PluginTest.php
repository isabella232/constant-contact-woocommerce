<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Test
 * @since   2019-03-20
 */

namespace WebDevStudios\CCForWoo\Test;

use WebDevStudios\CCForWoo\Plugin;
use WP_Mock\Tools\TestCase;

/**
 * Class PluginTest
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Test
 * @since   2019-03-20
 */
class PluginTest extends TestCase {
	/**
	 * @test
	 */
	public function throws_exception_if_woocommerce_version_is_too_low() {
		$woocommerce          = \WooCommerce::instance();
		$woocommerce->version = '3.2.0';

		$e      = null;
		$plugin = new Plugin( __FILE__ );

		try {
			$plugin->check_for_required_dependencies();
		} catch ( \Exception $e ) {
			$this->assertEquals( 'WooCommerce version "3.5.4" or greater is required to use Constant Contact + WooCommerce.', $e->getMessage() );
		}
	}
}
