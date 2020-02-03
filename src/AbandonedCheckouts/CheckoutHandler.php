<?php // phpcs:ignore -- Class name okay, PSR-4.
/**
 * Class to listen to WooCommerce checkouts and possibly store checkouts that are "abandoned".
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\AbandonedCheckouts
 * @since   1.2.0
 */

namespace WebDevStudios\CCForWoo\AbandonedCheckouts;

use WebDevStudios\OopsWP\Structure\Service;
use WC_Customer;
use WC_Order;
use DateTime;
use DateInterval;

/**
 * Class CheckoutHandler
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\AbandonedCheckouts
 * @since   1.2.0
 */
class CheckoutHandler extends Service {

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function register_hooks() {
		add_action( 'woocommerce_before_checkout_form', [ $this, 'enqueue_scripts' ] );

		add_action( 'woocommerce_after_template_part', [ $this, 'save_or_clear_checkout_data' ], 10, 4 );
		add_action( 'woocommerce_checkout_process', [ $this, 'update_checkout_data' ] );
		add_action( 'woocommerce_checkout_updated', [ $this, 'update_checkout_data' ] );
		add_action( 'woocommerce_calculate_totals', [ $this, 'update_checkout_data' ] );
		add_action( 'woocommerce_cart_item_removed', [ $this, 'update_checkout_data' ] );

		add_action( 'cc_woo_check_expired_checkouts', [ $this, 'delete_expired_checkouts' ] );

		add_action( 'wp_ajax_cc_woo_abandoned_checkouts_capture_guest_checkout', [ $this, 'maybe_capture_guest_checkout' ] );
		add_action( 'wp_ajax_nopriv_cc_woo_abandoned_checkouts_capture_guest_checkout', [ $this, 'maybe_capture_guest_checkout' ] );
	}


	/**
	 * Load front-end scripts.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( is_user_logged_in() ) {
			return;
		}

		wp_enqueue_script( 'cc-woo-public' );
	}

	/**
	 * AJAX handler for attempting to capture guest checkouts.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function maybe_capture_guest_checkout() {
		$data = filter_input_array( INPUT_POST, [
			'nonce' => FILTER_SANITIZE_STRING,
			'email' => FILTER_SANITIZE_EMAIL,
		] );

		if ( empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'woocommerce-process_checkout' ) ) {
			wp_send_json_error( esc_html__( 'Invalid nonce.', 'cc-woo' ) );
		}

		if ( ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) {
			wp_send_json_error( esc_html__( 'Invalid email.', 'cc-woo' ) );
		}

		$this->update_checkout_data( $data['email'] );

		wp_send_json_success();
	}

	/**
	 * Either call an update of checkout data which will be saved or remove checkout data based on what template we arrive at.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 *
	 * @since  1.2.0
	 * @param  string $template_name Current template file name.
	 * @param  string $template_path Current template path.
	 * @param  string $located       Full local path to current template file.
	 * @param  array  $args          Template args.
	 */
	public function save_or_clear_checkout_data( $template_name, $template_path, $located, $args ) {

		// If checkout page displayed, save checkout data.
		if ( 'checkout/form-checkout.php' === $template_name ) {
			$this->update_checkout_data();
		}

		// If thankyou page displayed, clear checkout data.
		if ( isset( $args['order'] ) && 'checkout/thankyou.php' === $template_name ) {
			$this->clear_purchased_data( $args['order'] );
		}
	}

	/**
	 * Update current checkout session data in db.
	 *
	 * Param type "mixed" is specified for $billing_email param here because we cannot type hint this, as some Woo hooks that this is a callback to will pass unused objects and other data as first param.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @param  mixed $billing_email Manually set customer billing email if provided.
	 */
	public function update_checkout_data( $billing_email = '' ) {

		// Reset billing email if not string.
		$billing_email = is_string( $billing_email ) ? $billing_email : '';

		// Delete saved checkout if cart emptied; update otherwise.
		if ( false === WC()->cart->is_empty() ) {
			$this->save_checkout_data( $billing_email );
		} else {
			$this->remove_checkout_data();
		}
	}

	/**
	 * Retrieve specific user's checkout data.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @param  string $select     Field to return.
	 * @param  mixed  $where      String or array of WHERE clause predicates, using placeholders for values.
	 * @param  array  $where_args Array of WHERE clause arguments.
	 * @param  string $order_by   Order by column.
	 * @param  string $order      Order (ASC/DESC).
	 * @param  string $limit      LIMIT clause.
	 * @param  array  $limit_args Array of LIMIT clause arguments.
	 * @return mixed              Checkout data if exists, else null.
	 */
	public static function get_checkout_data( string $select, $where, array $where_args, string $order_by = 'checkout_updated_ts', string $order = 'DESC', string $limit = '', array $limit_args = [] ) {
		global $wpdb;

		$table_name = CheckoutsTable::get_table_name();
		$where      = is_array( $where ) ? implode( ' AND ', $where ) : $where;

		// Construct query to return checkout data.
		// phpcs:disable -- Disabling a number of sniffs that erroneously flag following block of code.
		// $where often includes placeholders for replacement via $wpdb->prepare(). $where_values provides those values.
		return maybe_unserialize(
			$wpdb->get_var(
				$wpdb->prepare(
					"SELECT {$select}
					FROM {$table_name}
					WHERE {$where}
					ORDER BY {$order_by} {$order}
					{$limit}",
					array_merge( $where_args, $limit_args )
				)
			)
		);
		// phpcs:enable
	}

	/**
	 * Helper function to retrieve checkout contents based on checkout UUID.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 *
	 * @since  1.2.0
	 * @param  string $checkout_uuid Checkout UUID.
	 * @return array                 Checkout contents.
	 */
	public static function get_checkout_contents( $checkout_uuid ) {
		return self::get_checkout_data(
			'checkout_contents',
			'checkout_uuid = %s',
			[
				$checkout_uuid,
			]
		);
	}

	/**
	 * Save current checkout data to db.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @param  string $billing_email Manually set customer billing email if provided.
	 * @return void
	 */
	protected function save_checkout_data( string $billing_email = '' ) {
		global $wpdb;

		// Check for existing checkout session.
		if ( ! WC()->session->get( 'checkout_uuid' ) ) {

			// Only create session if currently on checkout page.
			if ( ! is_checkout() ) {
				return;
			}

			WC()->session->set( 'checkout_uuid', wp_generate_uuid4() );
		}

		$current_time = current_time( 'mysql', 1 );
		$table_name   = CheckoutsTable::get_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variable for table name in SQL.
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$table_name} (
					`user_id`,
					`user_email`,
					`checkout_contents`,
					`checkout_updated`,
					`checkout_updated_ts`,
					`checkout_created`,
					`checkout_created_ts`,
					`checkout_uuid`
				) VALUES (
					%d,
					%s,
					%s,
					%s,
					%d,
					%s,
					%d,
					%s
				) ON DUPLICATE KEY UPDATE `user_id` = VALUES(`user_id`), `user_email` = VALUES(`user_email`), `checkout_updated` = VALUES(`checkout_updated`), `checkout_updated_ts` = VALUES(`checkout_updated_ts`), `checkout_contents` = VALUES(`checkout_contents`)",
				get_current_user_id(),
				! empty( $billing_email ) ? $billing_email : WC()->checkout->get_value( 'billing_email' ),
				maybe_serialize( [
					'products'        => array_values( WC()->cart->get_cart() ),
					'coupons'         => WC()->cart->get_applied_coupons(),
				] ),
				$current_time,
				strtotime( $current_time ),
				$current_time,
				strtotime( $current_time ),
				WC()->session->get( 'checkout_uuid' )
			)
		);
		// phpcs:enable
	}

	/**
	 * Remove current checkout session data from db upon successful order submission.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @param  WC_Order $order Newly submitted order object.
	 * @return void
	 */
	public function clear_purchased_data( $order ) {
		if ( empty( $order ) ) {
			return;
		}

		$this->remove_checkout_data();
	}

	/**
	 * Helper function to remove checkout session data from db.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	protected function remove_checkout_data() {
		global $wpdb;

		// Delete current checkout data.
		$wpdb->delete(
			CheckoutsTable::get_table_name(),
			[
				'checkout_uuid' => WC()->session->get( 'checkout_uuid' ),
			],
			[
				'%s',
			]
		);
	}

	/**
	 * Delete expired checkouts.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function delete_expired_checkouts() {
		global $wpdb;

		// Delete all checkouts at least 30 days old.
		$table_name = CheckoutsTable::get_table_name();

		$wpdb->query(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variable for table name in SQL.
				"DELETE FROM {$table_name}
				WHERE `checkout_updated_ts` <= %s",
				// phpcs:enable
				( new DateTime() )->sub( new DateInterval( 'P30D' ) )->format( 'U' )
			)
		);
	}
}