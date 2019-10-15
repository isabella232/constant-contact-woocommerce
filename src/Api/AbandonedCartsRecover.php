<?php
/**
 * Class to handle recovery of abandoned carts via URL.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Api
 * @since   2019-10-11
 */

namespace WebDevStudios\CCForWoo\Api;

use WebDevStudios\CCForWoo\Database\AbandonedCartsTable;
use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class AbandonedCartsRecover
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-11
 */
class AbandonedCartsRecover extends Service {

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-15
	 */
	public function register_hooks() {
		if ( isset( $_GET['recover-cart'] ) ) {
			add_action( 'wp_loaded', [ $this, 'recover_cart' ] );
		}
	}

	/**
	 * Generate cart recovery URL.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-11
	 * @param  int $cart_id ID of abandoned cart.
	 * @return mixed           Cart recovery URL on successful retrieval, void on failure.
	 */
	public function get_cart_url( $cart_id ) {
		$cart_id = $this->get_cart_data( 'cart_id', $cart_id );

		if ( null === $cart_id ) {
			return;
		}

		return add_query_arg(
			'recover-cart',
			$cart_id,
			get_site_url()
		);
	}

	/**
	 * Recovery saved cart from ID.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-15
	 * @return void
	 */
	public function recover_cart() {
		$cart_id = intval(
			sanitize_key( $_GET['recover-cart'] )
		);

		if ( 0 === $cart_id ) {
			return;
		}


	}

	/**
	 * Retrieve cart data from cart ID.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-15
	 * @param  string $field   Field to return.
	 * @param  int    $cart_id Target cart ID.
	 * @return string Cart data.
	 */
	protected function get_cart_data( $field, $cart_id ) {
		global $wpdb;

		// Get/confirm cart ID.
		$table_name = $wpdb->prefix . AbandonedCartsTable::CC_ABANDONED_CARTS_TABLE;
		return maybe_unserialize(
			$wpdb->get_var(
				$wpdb->prepare(
					//@codingStandardsIgnoreStart
					"SELECT {$field}
					FROM {$table_name}
					WHERE `cart_id` = %d",
					//@codingStandardsIgnoreEnd
					$cart_id
				)
			)
		);
	}
}
