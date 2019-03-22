<?php
/**
 * Constant Contact + WooCommerce
 *
 * @since 0.0.1
 * @author WebDevStudios <https://www.webdevstudios.com/>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo;

use WebDevStudios\CCForWoo\Utility\PluginCompatibilityCheck;
use WebDevStudios\OopsWP\Structure\ServiceRegistrar;
use WebDevStudios\CCForWoo\View\ViewRegistrar;
use WebDevStudios\CCForWoo\View\Admin\Notice;
use WebDevStudios\CCForWoo\View\Admin\NoticeMessage;
use WebDevStudios\CCForWoo\Meta\ConnectionStatus;
use WebDevStudios\CCForWoo\Api\KeyManager;

/**
 * "Core" plugin class.
 *
 * @since 0.0.1
 */
final class Plugin extends ServiceRegistrar {
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
	 * @var array
	 * @since 2019-03-13
	 */
	protected $services = [
		ViewRegistrar::class,
		KeyManager::class,
	];

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
	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

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

		$this->do_deactivation_process();

		new Notice(
			new NoticeMessage( $reason, 'error', true )
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
	public function check_for_required_dependencies() {
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

		parent::run();
	}

	/**
	 * Register the plugin's hooks with WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-12
	 */
	public function register_hooks() {
		add_action( 'plugins_loaded', [ $this, 'check_for_required_dependencies' ] );

		register_activation_hook( $this->plugin_file, [ $this, 'do_activation_process' ] );
		register_deactivation_hook( $this->plugin_file, [ $this, 'do_deactivation_process' ] );
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

	/**
	 * Activate WooCommerce along with Constant Contact + WooCommerce if it's present and not already active.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 */
	private function maybe_activate_woocommerce() {
		$woocommerce = 'woocommerce/woocommerce.php';

		if ( ! is_plugin_active( $woocommerce ) && in_array( $woocommerce, array_keys( get_plugins() ), true ) ) {
			activate_plugin( $woocommerce );
		}
	}

	/**
	 * Callback for register_activation_hook.
	 *
	 * Performs the plugin's activation routines.
	 *
	 * @see register_activation_hook()
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 */
	public function do_activation_process() {
		$this->maybe_activate_woocommerce();

		flush_rewrite_rules();
	}

	/**
	 * Callback for register_deactivation_hook.
	 *
	 * Performs the plugin's deactivation routines, including notifying Constant Contact of disconnection.
	 *
	 * @see register_deactivation_hook()
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 * @return void
	 */
	public function do_deactivation_process() {
		if ( ! get_option( ConnectionStatus::CC_CONNECTION_ESTABLISHED_KEY ) ) {
			return;
		}

		delete_option( ConnectionStatus::CC_CONNECTION_ESTABLISHED_KEY );
	}
}
