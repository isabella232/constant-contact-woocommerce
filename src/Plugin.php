<?php
/**
 * Constant Contact + WooCommerce
 *
 * @since 0.0.1
 * @author WebDevStudios <https://www.webdevstudios.com/>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo;

use WebDevStudios\OopsWP\Utility\Hookable;
use WebDevStudios\OopsWP\Utility\Runnable;
use WebDevStudios\CCForWoo\View\Admin\Notice;
use WebDevStudios\CCForWoo\View\Admin\NoticeMessage;
use WebDevStudios\CCForWoo\View\Admin\WooTab;
use WebDevStudios\CCForWoo\Utility\PluginCompatibilityCheck;

/**
 * "Core" plugin class.
 *
 * @since 0.0.1
 */
final class Plugin implements Runnable, Hookable {
	const PLUGIN_NAME = 'Constant Contact + WooCommerce';

	/**
	 * Whether the plugin is currently active.
	 *
	 * @since 0.0.1
	 * @var bool
	 */
	private $is_active = false;

	/**
	 * The plugin file path, should be __FILE__ of the main entry point script.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $plugin_file;

	/**
	 * The plugin settings instance.
	 *
	 * @since 0.0.1
	 * @var \WebDevStudios\CCForWoo\Settings\SettingsTab
	 */
	private $settings;

	/**
	 * Deactivate this plugin.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $reason The reason for deactivating.
	 * @throws \Exception If the plugin isn't active, throw an \Exception.
	 */
	private function deactivate( $reason ) {
		unset( $_GET['activate'] );

		if ( ! $this->is_active() ) {
			throw new \Exception( $reason );
		}

		deactivate_plugins( $this->plugin_file );

		new Notice(
			new NoticeMessage(
				$reason,
				'error',
				true
			)
		);

		Notice::set_notices();
	}

	/**
	 * Maybe deactivate the plugin if certain conditions aren't met.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @throws \Exception When WooCommerce is not found or compatible.
	 */
	public function maybe_deactivate() {
		try {
			$compatibility_checker = new PluginCompatibilityCheck( '\\WooCommerce' );

			// Ensure requirements.
			if ( ! $compatibility_checker->is_available() ) {
				// translators: placeholder is the minimum supported WooCommerce version.
				$message = sprintf( __( 'WooCommerce version "%1$s" or greater must be installed and activated to use %2$s.', 'cc-woo' ), PluginCompatibilityCheck::MINIMUM_WOO_VERSION, self::PLUGIN_NAME );
				throw new \Exception( $message );
			}

			if ( ! $compatibility_checker->is_compatible( \WooCommerce::instance() ) ) {
				// translators: placeholder is the minimum supported WooCommerce version.
				$message = sprintf( __( 'WooCommerce version "%1$s" or greater is required to use %2$s.', 'cc-woo' ), PluginCompatibilityCheck::MINIMUM_WOO_VERSION, self::PLUGIN_NAME );
				throw new \Exception( $message );
			}
		} catch ( \Exception $e ) {
			$this->deactivate( $e->getMessage() );
		}
	}

	/**
	 * Setup the instance of this class.
	 *
	 * Prepare some things for later.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $plugin_file The plugin file path of the entry script.
	 * @param \WebDevStudios\CCForWoo\Settings\SettingsTab $settings An instance of the configuration for settings.
	 * @package cc-woo
	 */
	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Run things once the plugin instance is ready.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function run() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->is_active = is_plugin_active( plugin_basename( $this->plugin_file ) );
		$this->register_hooks();
	}

	/**
	 * Register the plugin's hooks with WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-12
	 * @return void
	 */
	public function register_hooks() {
		// Setup the plugin instance.
		register_deactivation_hook( __FILE__, [ Notice::class, 'maybe_display_notices' ] );

		add_action( 'plugins_loaded', [ $this, 'maybe_deactivate' ] );
		add_action( 'woocommerce_get_settings_pages', [ $this, 'load_custom_settings_tab' ] );
	}

	/**
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-12
	 * @return void
	 */
	public function load_custom_settings_tab() {
		( new WooTab() )->register_hooks();
	}

	/**
	 * Returns whether the plugin is active.
	 *
	 * @since 0.0.1
	 * @author Zach Owen Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	public function is_active() : bool {
		return $this->is_active;
	}

	/**
	 * Get the plugin file path.
	 *
	 * @since 0.0.1
	 * @author Zach Owen Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	public function get_plugin_file() : string {
		return $this->plugin_file;
	}
}
