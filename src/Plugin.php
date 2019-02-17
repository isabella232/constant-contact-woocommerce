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
	use \WDS\Util\SingletonTrait;
	use \WDS\Util\AccessibleTrait;

	const PLUGIN_NAME = 'Constant Contact + WooCommerce';

	/**
	 * Whether the plugin is currently active.
	 *
	 * @since 0.0.1
	 * @var bool
	 */
	private $is_active = false;

	/**
	 * Array of arguments supplied to this class.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $args = [];

	/**
	 * Array of accessible fields.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	protected static $accessible_fields = [
		'is_active',
		'args',
	];

	/**
	 * Constructor! Kick things off.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param array $args Array of plugin arguments.
	 */
	public function __construct( $args = [] ) {
		$this->parse_args( $args );
		$this->setup_instance();
	}

	/**
	 * Deactivate this plugin.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $reason The reason for deactivating.
	 * @return mixed
	 * @throws \Exception If the plugin isn't active, throw an \Exception.
	 */
	public static function deactivate( $reason ) {
		if ( self::get_instance()->is_active ) {
			deactivate_plugins( self::get_instance()->args['plugin_file'] );
			return new \ConstantContact\WooCommerce\View\Admin\Notice(
				[
					'class'   => 'error',
					'message' => $reason,
				]
			);
		}

		throw new \Exception( $reason );
	}

	/**
	 * Maybe deactivate the plugin if certain conditions aren't met.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @throws \Exception When WooCommerce is not found or compatible.
	 */
	public static function maybe_deactivate() {
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
			self::deactivate( $e->getMessage() );
		}
	}

	/**
	 * Parse the arguments.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param array $args Array of arguments to parse.
	 */
	private function parse_args( $args ) {
		static $defaults = [
			'plugin_file' => '',
		];

		$args = wp_parse_args( $args, $defaults );

		foreach ( array_keys( $args ) as $key ) {
			if ( isset( $defaults[ $key ] ) ) {
				continue;
			}

			unset( $args[ $key ] );
		}

		$this->args = $args;
	}

	/**
	 * Setup the instance of this class.
	 *
	 * Prepare some things for later.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @package cc-woo
	 */
	private function setup_instance() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->is_active = is_plugin_active( plugin_basename( $this->args['plugin_file'] ) );
	}
}
