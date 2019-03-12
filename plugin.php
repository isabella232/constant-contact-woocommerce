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

// @TODO We'll probably want to ship the autoloader and dependencies with the plugin and not require folks to have to run Composer.
if ( ! is_readable( $autoloader ) ) {
	// translators: placeholder is the current directory.
	throw new \Exception( sprintf( __( 'Please run `composer install` in the plugin folder "%s" and try activating this plugin again.', 'cc-woo' ), dirname( __FILE__ ) ) );
}

require_once $autoloader;

$plugin = new \WebDevStudios\CCForWoo\Plugin( __FILE__ );
$plugin->run();

// Hook things!
# \WebDevStudios\CCForWoo\Views\Admin\WooSettingsTab::hooks();
