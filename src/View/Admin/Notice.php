<?php
/**
 * Admin Notice Class
 *
 * This class is responsible for storing and showing notices in the WordPress
 * admin area.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios.com>
 * @package cc-woo
 */

namespace ConstantContact\WooCommerce\View\Admin;

/**
 * Notice Class
 *
 * @since 0.0.1
 */
class Notice {
	/**
	 * Transient key used in the database.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	const TRANSIENT_KEY = 'cc-woo-notices';

	/**
	 * Arguments for the notice.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $args = [];

	/**
	 * Class constructor.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param array $args {
	 *     Array of arguments.
	 *
	 *     @type string $class The CSS class to apply to the notice, as 'notice-$class'
	 *     @type string $message The message to display
	 *     @type bool   $dismissible Is the notice dismissible?
	 * }
	 */
	public function __construct( $args ) {
		static $defaults = [
			'class'       => 'notice',
			'message'     => '',
			'dismissible' => false,
		];

		$this->args = array_filter(
			wp_parse_args( $args, $defaults ),
				function( $key ) use ( $defaults ) {
					return isset( $defaults[ $key ] );
				},
				ARRAY_FILTER_USE_KEY
			);

		$this->store_notice();
	}

	/**
	 * Store admin notices in the database.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 */
	private function store_notice() {
		$existing_notices   = get_transient( self::TRANSIENT_KEY ) ?: [];
		$existing_notices[] = $this->args;
		set_transient( 'cc-woo-notices', $existing_notices );
	}

	/**
	 * Display notices, if there are any.
	 *
	 * This method will also delete the notices transient if they are displayed.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @return void
	 */
	public static function maybe_display_notices() {
		$notices = get_transient( self::TRANSIENT_KEY ) ?: [];

		if ( empty( $notices ) ) {
			return;
		}

		foreach ( $notices as $notice ) {
			$dismissible_css = $notice['dismissible'] ? 'is-dismissible' : '';
?>
	<div class="notice notice-<?php echo esc_attr( $notice['class'] ); ?> <?php echo esc_attr( $dismissible_css ); ?>">
	<p><?php echo esc_html( $notice['message'] ); ?></p>
</div>
<?php
		}

		delete_transient( self::TRANSIENT_KEY );
	}
}
