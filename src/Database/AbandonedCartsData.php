<?php
/**
 * Class to handle abandoned carts data.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-11
 */

namespace WebDevStudios\CCForWoo\Database;

use WebDevStudios\OopsWP\Structure\Service;
use WC_Customer;
use DateTime;
use DateInterval;

/**
 * Class AbandonedCartsData
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-11
 */
class AbandonedCartsData extends Service {

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-11
	 */
	public function register_hooks() {
		add_action( 'woocommerce_after_template_part', [ $this, 'check_template' ], 10, 4 );
		add_action( 'woocommerce_checkout_process', [ $this, 'update_cart_data' ] );
		add_action( 'check_expired_carts', [ $this, 'check_expired_carts' ] );
		add_action( 'woocommerce_calculate_totals', [ $this, 'update_cart_data' ] );
		add_action( 'woocommerce_cart_item_removed', [ $this, 'update_cart_data' ] );
	}

	/**
	 * Check current WC template.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-11
	 * @param  string $template_name Current template file name.
	 * @param  string $template_path Current template path.
	 * @param  string $located       Full local path to current template file.
	 * @param  array  $args          Template args.
	 */
	public function check_template( $template_name, $template_path, $located, $args ) {
		// If checkout page displayed, save cart data.
		if ( 'checkout/form-checkout.php' === $template_name ) {
			$this->update_cart_data();
		}
		// If thankyou page displayed, clear cart data.
		if ( 'checkout/thankyou.php' === $template_name ) {
			$this->clear_purchased_data( $args['order'] );
		}
	}

	/**
	 * Update current cart session data in db.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-10
	 * @return void
	 */
	public function update_cart_data() {
		$user_id       = get_current_user_id();
		$customer_data = [
			'billing'  => [],
			'shipping' => [],
		];

		// Get saved customer data if exists. If guest user, blank customer data will be generated.
		$customer                  = new WC_Customer( $user_id );
		$customer_data['billing']  = $customer->get_billing();
		$customer_data['shipping'] = $customer->get_shipping();

		// Update customer data from user session data.
		$customer_data['billing'] = array_merge( $customer_data['billing'], WC()->customer->get_billing() );
		$customer_data['shipping'] = array_merge( $customer_data['shipping'], WC()->customer->get_shipping() );

		// Check if submission attempted.
		if ( isset( $_POST['woocommerce_checkout_place_order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- Okay use of $_POST data.
			// Update customer data from posted data.
			array_walk( $customer_data['billing'], [ $this, 'process_customer_data' ], 'billing' );
			array_walk( $customer_data['shipping'], [ $this, 'process_customer_data' ], 'shipping' );
		} else {
			// Retrieve cart data for current user, if exists.
			$cart_data = $this::get_cart_data(
				'cart_contents',
				[
					'user_id = %d',
					'user_email = %s',
				],
				[
					$user_id,
					WC()->checkout->get_value( 'billing_email' ),
				]
			);
			if ( null !== $cart_data && ! empty( $cart_data['customer'] ) ) {
				// Update customer data from saved cart data.
				$customer_data['billing'] = array_merge( $customer_data['billing'], $cart_data['customer']['billing'] );
				$customer_data['shipping'] = array_merge( $customer_data['shipping'], $cart_data['customer']['shipping'] );
			}
		}

		if ( empty( $customer_data['billing']['email'] ) ) {
			return;
		}

		// Delete saved cart if cart emptied; update otherwise.
		if ( false === WC()->cart->is_empty() ) {
			// Save cart data to db.
			$this->save_cart_data( $user_id, $customer_data );
		} else {
			// Delete cart data from db.
			$this->remove_cart_data( $user_id, $customer_data['billing']['email'] );
		}
	}

	/**
	 * Merge database and posted customer data.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-15
	 * @param  string $value  Value of posted array item.
	 * @param  string $key    Key of posted array item.
	 * @param  string $type   Type of array (billing or shipping).
	 */
	protected function process_customer_data( &$value, $key, $type ) {
		$posted = WC()->checkout()->get_posted_data();
		$value = isset( $posted[ "{$type}_{$key}" ] ) ? $posted[ "{$type}_{$key}" ] : $value;
	}

	/**
	 * Retrieve specific user's cart data.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-16
	 * @param  string $select        Field to return.
	 * @param  mixed  $where         String or array of WHERE clause predicates, using placeholders for values.
	 * @param  array  $where_values  Array of WHERE clause values.
	 * @return string Cart data.
	 */
	public static function get_cart_data( $select, $where, $where_values ) {
		global $wpdb;

		$table_name = $wpdb->prefix . AbandonedCartsTable::CC_ABANDONED_CARTS_TABLE;
		$where      = is_array( $where ) ? implode( ' AND ', $where ) : $where;

		// Construct query to return cart data.
		return maybe_unserialize(
			$wpdb->get_var(
				$wpdb->prepare(
					// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variables in SQL.
					"SELECT {$select}
					FROM {$table_name}
					WHERE {$where}",
					// phpcs:enable
					$where_values
				)
			)
		);
	}

	/**
	 * Helper function to retrieve cart contents based on cart hash key.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-17
	 * @param  int $cart_id ID of abandoned cart.
	 * @return string       Hash key string of abandoned cart.
	 */
	public static function get_cart_hash( int $cart_id ) {
		return $this::get_cart_data(
			'HEX(cart_hash)',
			'cart_id = %d',
			[
				intval( $cart_id ),
			]
		);
	}

	/**
	 * Save current cart data to db.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-15
	 * @param  int   $user_id       Current user ID.
	 * @param  array $customer_data Customer billing and shipping data.
	 */
	protected function save_cart_data( $user_id, $customer_data ) {
		// Get current time.
		$time_added = current_time( 'mysql', 1 );

		global $wpdb;

		// Insert/update cart data.
		$table_name = $wpdb->prefix . AbandonedCartsTable::CC_ABANDONED_CARTS_TABLE;
		$wpdb->query(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variable for table name in SQL.
				"INSERT INTO {$table_name} (`user_id`, `user_email`, `cart_contents`, `cart_updated`, `cart_updated_ts`, `cart_hash`) VALUES (%d, %s, %s, %s, %d, UNHEX(MD5(CONCAT(user_id, user_email))))
				ON DUPLICATE KEY UPDATE `cart_updated` = VALUES(`cart_updated`), `cart_updated_ts` = VALUES(`cart_updated_ts`), `cart_contents` = VALUES(`cart_contents`)",
				// phpcs:enable
				$user_id,
				$customer_data['billing']['email'],
				maybe_serialize( [
					'products'        => WC()->cart->get_cart(),
					'coupons'         => WC()->cart->get_applied_coupons(),
					'customer'        => $customer_data,
					'shipping_method' => WC()->checkout()->get_posted_data()['shipping_method'],
				] ),
				$time_added,
				strtotime( $time_added )
			)
		);
	}

	/**
	 * Remove current cart session data from db upon successful order submission.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-11
	 * @param  WC_Order $order Newly submitted order object.
	 * @return void
	 */
	public function clear_purchased_data( $order ) {
		if ( false === $order ) {
			return;
		}
		$this->remove_cart_data( $order->get_user_id(), $order->get_billing_email() );
	}

	/**
	 * Helper function to remove cart session data from db.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-11
	 * @param  int    $user_id    ID of cart owner.
	 * @param  string $user_email Email of cart owner.
	 */
	protected function remove_cart_data( $user_id, $user_email ) {
		global $wpdb;

		// Delete current cart data.
		$wpdb->delete(
			$wpdb->prefix . AbandonedCartsTable::CC_ABANDONED_CARTS_TABLE,
			[
				'user_id' => $user_id,
				'user_email' => $user_email,
			],
			[
				'%d',
				'%s',
			]
		);
	}

	/**
	 * Delete expired carts.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-11
	 */
	public function check_expired_carts() {
		global $wpdb;

		// Delete all carts at least 30 days old.
		$table_name = $wpdb->prefix . AbandonedCartsTable::CC_ABANDONED_CARTS_TABLE;
		$test = $wpdb->query(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variable for table name in SQL.
				"DELETE FROM {$table_name}
				WHERE `cart_updated_ts` <= %s",
				// phpcs:enable
				( new DateTime() )->sub( new DateInterval( 'P30D' ) )->format( 'U' )
			)
		);
	}
}
