<?php
/**
 * Class responsible for applying preference options and meta to customers and orders.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Meta
 * @since   2019-03-18
 */

namespace WebDevStudios\CCForWoo\Meta;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class NewsletterPreference
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Meta
 * @since   2019-03-18
 */
class NewsletterPreference implements Hookable {
	/**
	 * The name of the meta field for the customer's preference.
	 *
	 * This constant will be used both in usermeta (for users) and postmeta (for orders).
	 *
	 * @var string
	 * @since 2019-03-18
	 */
	const CUSTOMER_PREFERENCE_META_FIELD = 'cc_woo_customer_agrees_to_marketing';

	/**
	 * Register hooks for this preference with WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 */
	public function register_hooks() {
		add_action( 'woocommerce_checkout_update_user_meta', [ $this, 'save_user_preference' ] );
		add_action( 'woocommerce_created_customer', [ $this, 'save_user_preference' ] );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_user_preference_to_order' ] );
	}

	/**
	 * Save the user's newsletter preferences to meta.
	 *
	 * @param int $user_id ID of the user.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return void
	 */
	public function save_user_preference( $user_id ) {
		if ( ! $user_id ) {
			return;
		}

		update_user_meta( $user_id, self::CUSTOMER_PREFERENCE_META_FIELD, $this->get_submitted_customer_preference() );
	}

	/**
	 * Get the submitted customer newsletter preference.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 * @return string
	 */
	private function get_submitted_customer_preference() {
		// @TODO Add nonce verification.
		return isset( $_POST['customer_newsletter_opt_in'] ) && 1 === filter_var( $_POST['customer_newsletter_opt_in'], FILTER_VALIDATE_INT )
			? 'yes'
			: 'no';
	}

	/**
	 * Save the user preference to the order meta.
	 *
	 * @param int $order_id The order ID.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 */
	public function save_user_preference_to_order( $order_id ) {
		add_post_meta( $order_id, self::CUSTOMER_PREFERENCE_META_FIELD, $this->get_submitted_customer_preference(), true );
	}
}