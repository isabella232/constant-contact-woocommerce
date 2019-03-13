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

use WebDevStudios\CCForWoo\View\Admin\WooTab;
use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class BillingFieldFilter
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-03-13
 */
class BillingFieldsFilter implements Hookable {
	/**
	 * Register actions and filters with WordPress.
	 *
	 * @since 2019-03-13
	 */
	public function register_hooks() {
		add_action( 'woocommerce_after_checkout_billing_form', [ $this, 'add_newsletter_checkbox' ] );
	}

	/**
	 * Add the newsletter checkbox to the set of fields in the billing form.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 */
	public function add_newsletter_checkbox() {
		woocommerce_form_field( 'customer_newsletter_opt_in', [
			'type'  => 'checkbox',
			'class' => [ 'input-checkbox' ],
			'label' => __( 'I agree to receive marketing e-mails', 'cc-woo' ),
		], $this->get_default_checkbox_state() );
	}

	/**
	 * Get the default state of the newsletter opt-in checkbox.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_default_checkbox_state() : bool {
		$store_default = ( 'yes' === get_option( 'cc_woo_customer_data_email_opt_in_default' ) );

		if ( ! is_user_logged_in() ) {
			return $store_default;
		}

		$user_preference = get_user_meta( get_current_user_id(), 'cc_woo_newsletter_opted_in' );

		if ( $user_preference ) {
			return 'yes' === $user_preference;
		}

		return $store_default;
	}
}
