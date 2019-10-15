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
		$user_id = get_current_user_id();

		// Get user email if provided.
		if ( 0 === $user_id ) {
			// If guest user, check posted data for email.
			$posted = WC()->checkout()->get_posted_data();
			$user_email = '';
			if ( isset( $posted['billing_email'] ) && '' !== $posted['billing_email'] ) {
				$user_email = sanitize_email( $posted['billing_email'] );
			}
		} else {
			// If registered user, get email from account.
			$user_email = sanitize_email( get_userdata( $user_id )->user_email );
		}

		if ( '' === $user_email ) {
			return;
		}

		// Get current time.
		$time_added = current_time( 'mysql', 1 );

		global $wpdb;

		// Insert/update cart data.
		$table_name = $wpdb->prefix . AbandonedCartsTable::CC_ABANDONED_CARTS_TABLE;
		$wpdb->query(
			$wpdb->prepare(
				//@codingStandardsIgnoreStart
				"INSERT INTO {$table_name} (`user_id`, `user_email`, `cart_contents`, `cart_updated`, `cart_updated_ts`, `cart_hash`) VALUES (%d, %s, %s, %s, %d, UNHEX(MD5(CONCAT(user_id, user_email))))
				ON DUPLICATE KEY UPDATE `cart_updated` = VALUES(`cart_updated`), `cart_updated_ts` = VALUES(`cart_updated_ts`), `cart_contents` = VALUES(`cart_contents`)",
				//@codingStandardsIgnoreEnd
				$user_id,
				$user_email,
				maybe_serialize( WC()->cart->get_cart() ),
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
			array(
				'user_id' => $user_id,
				'user_email' => $user_email,
			),
			array(
				'%d',
				'%s',
			)
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
				//@codingStandardsIgnoreStart
				"DELETE FROM {$table_name}
				WHERE `cart_updated_ts` <= %s",
				//@codingStandardsIgnoreEnd
				( new \DateTime() )->sub( new \DateInterval( 'P30D' ) )->format( 'U' )
			)
		);
	}
}
