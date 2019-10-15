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
		global $wpdb;

		// Get/confirm cart ID.
		$table_name = $wpdb->prefix . AbandonedCartsTable::CC_ABANDONED_CARTS_TABLE;
		$cart_id = maybe_unserialize(
			$wpdb->get_var(
				$wpdb->prepare(
					//@codingStandardsIgnoreStart
					"SELECT cart_id
					FROM {$table_name}
					WHERE `cart_id` = %d",
					//@codingStandardsIgnoreEnd
					$cart_id
				)
			)
		);

		if ( null === $cart_id ) {
			return;
		}

		return add_query_arg(
			'recover-cart',
			$cart_id,
			get_site_url()
		);
	}
}
