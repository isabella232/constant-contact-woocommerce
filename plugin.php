<?php
/**
 * Constant Contact + WooCommerce
 *
 * @since 2019-02-15
 * @author Constant Contact <https://www.constantcontact.com/>
 * @package cc-woo
 *
 * @wordpress-plugin
 * Plugin Name: Constant Contact + WooCommerce
 * Description: Add products to your emails and sync your contacts.
 * Plugin URI: https://github.com/WebDevStudios/constant-contact-woocommerce
 * Version: 1.3.2
 * Author: Constant Contact
 * Author URI: https://www.constantcontact.com/
 * Text Domain: cc-woo
 * WC tested up to: 4.0.1
 * Requires PHP: 7.2
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 */

// Autoload things.
$cc_woo_autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! is_readable( $cc_woo_autoloader ) ) {
	/* Translators: Placeholder is the current directory. */
	throw new \Exception( sprintf( __( 'Please run `composer install` in the plugin folder "%s" and try activating this plugin again.', 'cc-woo' ), dirname( __FILE__ ) ) );
}

require_once $cc_woo_autoloader;

$cc_woo_plugin = new \WebDevStudios\CCForWoo\Plugin( __FILE__ );
$cc_woo_plugin->run();
