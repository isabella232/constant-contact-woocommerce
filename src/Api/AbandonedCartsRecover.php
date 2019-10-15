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
		$cart_hash = $this->get_cart_data( 'cart_hash', 'cart_id', $cart_id );

		if ( null === $cart_hash ) {
			return;
		}

		return add_query_arg(
			'recover-cart',
			$cart_hash,
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
		$cart_hash = sanitize_key( $_GET['recover-cart'] );

		if ( '' === $cart_hash ) {
			return;
		}

		// Clear current cart contents.
		WC()->cart->empty_cart();

		$cart_contents = $this->get_cart_data( 'cart_contents', 'cart_hash', $cart_hash );

		if ( null === $cart_contents ) {
			return;
		}

		// Programmatically add each product to cart.
		foreach ( $cart_contents as $product ) {
			WC()->cart->add_to_cart(
				$product['product_id'],
				$product['quantity'],
				empty( $product['variation_id'] ) ? 0 : $product['variation_id'],
				empty( $product['variation'] ) ? array() : $product['variation']
			);
		}

		// Redirect to cart page.
		wp_safe_redirect( wc_get_page_permalink( 'cart' ) );
	}

	/**
	 * Retrieve cart data from cart ID.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-15
	 * @param  string $select_field Field to return.
	 * @param  string $where_field  Field to search on.
	 * @param  string $where_value  Value to search on.
	 * @return string Cart data.
	 */
	protected function get_cart_data( $select_field, $where_field, $where_value ) {
		global $wpdb;

		// Get/confirm cart ID.
		$table_name = $wpdb->prefix . AbandonedCartsTable::CC_ABANDONED_CARTS_TABLE;
		// Handle binary columns.
		$select_field = 'cart_hash' === $select_field ? "HEX({$select_field}) AS {$select_field}" : $select_field;
		$where_value = 'cart_hash' === $where_field ? "UNHEX('{$where_value}')" : $where_value;
		return maybe_unserialize(
			$wpdb->get_var(
				//@codingStandardsIgnoreStart
				"SELECT {$select_field}
				FROM {$table_name}
				WHERE {$where_field} = {$where_value}"
				//@codingStandardsIgnoreEnd
			)
		);
	}
}
