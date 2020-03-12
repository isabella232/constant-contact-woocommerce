<?php // phpcs:ignore -- Class name okay, PSR-4.
/**
 * Class to handle recovery of abandoned checkouts via URL.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Api
 * @since   1.2.0
 */

namespace WebDevStudios\CCForWoo\AbandonedCheckouts;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class CheckoutRecovery
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   1.2.0
 */
class CheckoutRecovery extends Service {

	/**
	 * Current checkout UUID.
	 *
	 * @var string
	 * @since  1.2.0
	 */
	protected $checkout_uuid = '';

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @return void
	 */
	public function register_hooks() {
		// Sanitize checkout UUID.
		$this->checkout_uuid = filter_input( INPUT_GET, 'recover-checkout', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );

		if ( empty( $this->checkout_uuid ) ) {
			return;
		}

		add_action( 'wp_loaded', [ $this, 'recover_checkout' ] );
	}

	/**
	 * Recovery saved checkout from UUID.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @return void
	 */
	public function recover_checkout() {

		// Set checkout session UUID.
		WC()->session->set( 'checkout_uuid', $this->checkout_uuid );

		// Clear current checkout contents.
		WC()->cart->empty_cart();

		// Get saved checkout contents.
		$checkout_contents = CheckoutHandler::get_checkout_contents( $this->checkout_uuid );

		if ( null === $checkout_contents ) {
			return;
		}

		// Recover saved products.
		$this->recover_products( $checkout_contents['products'] );

		// Apply coupons.
		foreach ( $checkout_contents['coupons'] as $coupon ) {
			WC()->cart->apply_coupon( $coupon );
		}

		// Update totals.
		WC()->cart->calculate_totals();

		// Redirect to checkout page.
		wp_safe_redirect( wc_get_page_permalink( 'cart' ) );
		exit();
	}

	/**
	 * Recover products from saved checkout data.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  array $products Array of product data.
	 */
	protected function recover_products( $products ) {
		// Programmatically add each product to cart.
		$products_added = [];
		foreach ( $products as $product ) {
			$added = WC()->cart->add_to_cart(
				$product['product_id'],
				$product['quantity'],
				empty( $product['variation_id'] ) ? 0 : $product['variation_id'],
				empty( $product['variation'] ) ? array() : $product['variation']
			);
			if ( false !== $added ) {
				$products_added[ ( empty( $product['variation_id'] ) ? $product['product_id'] : $product['variation_id'] ) ] = $product['quantity'];
			}
		}

		// Add product notices.
		if ( 0 < count( $products_added ) ) {
			wc_add_to_cart_message( $products_added );
		}
		if ( count( $products ) > count( $products_added ) ) {
			wc_add_notice(
				sprintf(
					/* translators: %d item count */
					_n(
						'%d item from your previous order is currently unavailable and could not be added to your cart.',
						'%d items from your previous order are currently unavailable and could not be added to your cart.',
						( count( $products ) - count( $products_added ) ),
						'cc-woo'
					),
					( count( $products ) - count( $products_added ) )
				),
				'error'
			);
		}
	}
}
