<?php
/**
 * Constant Contact + WooCommerce
 *
 * @since 0.0.1
 * @author WebDevStudios <https://www.webdevstudios.com/>
 * @package cc-woo
 */

namespace ConstantContact\WooCommerce;

use ConstantContact\WooCommerce\Util\WooCompat;

/**
 * "Core" plugin class.
 *
 * @since 0.0.1
 */
final class Plugin {
	use \WebDevStudios\Utility\SingletonTrait;

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
	 * Deactivate this plugin.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $reason The reason for deactivating.
	 * @throws \Exception If the plugin isn't active, throw an \Exception.
	 */
	public function deactivate( $reason ) {
		if ( ! $this->is_active() ) {
			throw new \Exception( $reason );
		}

		deactivate_plugins( $this->get_plugin_file() );
		new \ConstantContact\WooCommerce\View\Admin\Notice(
			[
				'class'   => 'error',
				'message' => $reason,
			]
		);
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
			// Ensure requirements.
			if ( ! WooCompat::is_woo_available() ) {
				// translators: placeholder is the minimum supported WooCommerce version.
				$message = sprintf( __( 'WooCommerce version "%1$s" or greater must be installed and activated to use %2$s.', 'cc-woo' ), WooCompat::MINIMUM_WOO_VERSION, self::PLUGIN_NAME );
				throw new \Exception( $message );
			}

			if ( ! WooCompat::is_woo_compatible() ) {
				// translators: placeholder is the minimum supported WooCommerce version.
				$message = sprintf( __( 'WooCommerce version "%1$s" or greater is required to use %2$s.', 'cc-woo' ), WooCompat::MINIMUM_WOO_VERSION, self::PLUGIN_NAME );
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
	 * @package cc-woo
	 */
	public function setup_plugin( string $plugin_file ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugin_file = $plugin_file;
		$this->is_active   = is_plugin_active( plugin_basename( $this->plugin_file ) );
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
