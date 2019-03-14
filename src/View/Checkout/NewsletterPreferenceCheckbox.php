<?php
/**
 * Class to handle filtering fields in the checkout billing form.
 *
 * @see https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-03-13
 */

namespace WebDevStudios\CCForWoo\View\Checkout;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class BillingFieldFilter
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-03-13
 */
class NewsletterPreferenceCheckbox implements Hookable {
	/**
	 * Register actions and filters with WordPress.
	 *
	 * @since 2019-03-13
	 */
	public function register_hooks() {
		add_action( 'woocommerce_after_checkout_billing_form', [ $this, 'add_field_to_billing_form' ] );
		add_action( 'woocommerce_checkout_update_user_meta', [ $this, 'save_user_preference' ] );
		add_action( 'woocommerce_created_customer', [ $this, 'save_user_preference' ] );
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

		// @TODO Verify nonce.
		$preference = isset( $_POST['customer_newsletter_opt_in'] ) && 1 === filter_var( $_POST['customer_newsletter_opt_in'], FILTER_VALIDATE_INT )
			? 'yes'
			: 'no';

		update_user_meta( $user_id, 'cc_woo_customer_agrees_to_marketing', $preference );
	}

	/**
	 * Add the newsletter checkbox to the set of fields in the billing form.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 */
	public function add_field_to_billing_form() {
		woocommerce_form_field( 'customer_newsletter_opt_in', [
			'type'  => 'checkbox',
			'class' => [ 'input-checkbox' ],
			'label' => __( 'I agree to receive marketing e-mails', 'cc-woo' ),
		], $this->get_default_checked_state() );
	}

	/**
	 * Get the default state of the newsletter opt-in checkbox.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_default_checked_state() : bool {
		return is_user_logged_in() ? $this->get_user_default_checked_state() : $this->get_store_default_checked_state();
	}

	/**
	 * Get the default checkbox state from a user's preferences.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_user_default_checked_state() : bool {
		$user_preference = get_user_meta( get_current_user_id(), 'cc_woo_customer_agrees_to_marketing', true );

		return ! empty( $user_preference ) ? 'yes' === $user_preference : $this->get_store_default_checked_state();
	}

	/**
	 * Get the store's default checkbox state.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_store_default_checked_state() : bool {
		return 'yes' === get_option( 'cc_woo_customer_data_email_opt_in_default' );
	}
}
