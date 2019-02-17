<?php
/**
 * Constant Contact + WooCommerce
 *
 * @since 2019-02-15
 * @author WebDevStudios <https://www.webdevstudios.com/>
 * @package cc-woo
 *
 * @wordpress-plugin
 * Plugin Name: Constant Contact + WooCommerce
 * Description: Integrate Constant Contact with WooCommerce.
 * Plugin URI: https://github.com/WebDevStudios/constant-contact-woocommerce
 * Version: 0.0.1
 * Author: WebDevStudios
 * Author URI: https://www.webdevstudios.com/
 * Text Domain: cc-woo
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 */

// Autoload things.
$autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! file_exists( $autoloader ) ) {
	// translators: placeholder is the current directory.
	throw new \Exception( sprintf( __( 'Please run `composer install` in the plugin folder "%s" and try activating this plugin again.', 'cc-woo' ), dirname( __FILE__ ) ) );
}

require_once $autoloader;

/**
 * Main plugin helper.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios>
 * @return object
 */
function cc_woo() {
	return \ConstantContact\WooCommerce\Plugin::get_instance(
		[
			'plugin_file' => __FILE__,
		]
	);
}

cc_woo();

// Setup the plugin instance.
add_action( 'plugins_loaded', [ '\\ConstantContact\\WooCommerce\\Plugin', 'maybe_deactivate' ] );
register_deactivation_hook( __FILE__, [ '\\ConstantContact\\WooCommerce\\View\\Admin\\Notice', 'maybe_display_notices' ] );

// Hook things!
# \ConstantContact\WooCommerce\Views\Admin\WooSettingsTab::hooks();
