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

use ConstantContact\WooCommerce\Util\WooCompat;

// Autoload things.
$autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! file_exists( $autoloader ) ) {
	// translators: placeholder is the current directory.
	throw new \Exception( sprintf( __( 'Please run `composer install` in the plugin folder "%s" and try activating this plugin again.', 'cc-woo' ), dirname( __FILE__ ) ) );
}

require_once $autoloader;

// Ensure requirements.
if ( ! WooCompat::is_woo_available() ) {
	// translators: placeholder is the minimum supported WooCommerce version.
	throw new \Exception( sprintf( __( 'WooCommerce version "%s" or greater must be installed and activated to use this plugin.', 'cc-woo' ), WooCompat::get_minimum_version() ) );
}

if ( ! WooCompat::is_woo_compatible() ) {
	// translators: placeholder is the minimum supported WooCommerce version.
	throw new \Exception( sprintf( __( 'WooCommerce version "%s" or greater is required to use this plugin.', 'cc-woo' ), WooCompat::get_minimum_version() ) );
}

// Hook things!
\ConstantContact\WooCommerce\Views\Admin\WooSettingsTab::hooks();
