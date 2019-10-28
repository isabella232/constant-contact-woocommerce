<?php
/**
 * REST API endpoint for collection of Abandoned Carts.
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-16
 */

namespace WebDevStudios\CCForWoo\Rest\V1\Endpoints;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Controller;
use WP_REST_Response;
use WC_Product;

use WebDevStudios\CCForWoo\AbandonedCarts\CartsTable;
use WebDevStudios\CCForWoo\AbandonedCarts\Cart;
use WebDevStudios\CCForWoo\Rest\V1\Registrar;

/**
 * Class AbandonedCarts
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-16
 */
class AbandonedCarts extends WP_REST_Controller {

	/**
	 * This endpoint's rest base.
	 *
	 * @since 2019-10-16
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 */
	public function __construct() {
		$this->rest_base = 'abandoned-carts';
	}

	/**
	 * Register the Abandoned Carts route.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 */
	public function register_routes() {
		register_rest_route(
			Registrar::$namespace, '/' . $this->rest_base,
			[
				[
					'methods'  => WP_REST_Server::READABLE,
					'callback' => [ $this, 'get_items' ],
				],
				'schema' => null,
			]
		);
	}

	/**
	 * Register the Abandoned Carts endpoint.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return array
	 */
	public function get_items( $request ) {
		global $wpdb;

		$params = $request->get_query_params();

		$page     = (int) isset( $params['page'] ) ? $params['page'] : 1;
		$per_page = (int) isset( $params['per_page'] ) ? $params['per_page'] : 10;
		$offset   = 1 === $page ? 0 : ( $page - 1 ) * $per_page;

		$response = [
			'carts'         => $this->get_cart_data( $per_page, $offset ),
			'currency_code' => $this->get_currency_code(),
			'page'          => $page,
		];

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Get an array of cart data.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @param int $per_page The per_page value from the REST request.
	 * @param int $offset The offset for use in SQL query, based on page number specified in REST request.
	 * @return array
	 */
	private function get_cart_data( int $per_page, int $offset ) : array {
		global $wpdb;

		$table_name = CartsTable::get_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variable for table name in SQL.
		$data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
					cart_id,
					user_id,
					user_email,
					cart_contents,
					cart_updated,
					cart_updated_ts,
					cart_created,
					cart_created_ts,
					HEX(cart_hash) as cart_hash
				FROM {$table_name}
				ORDER BY cart_updated_ts
				DESC
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL

		return $this->prepare_cart_data_for_api( $data );
	}

	/**
	 * Adds and modifies fields in individual carts before displaying them in the API response.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-23
	 *
	 * @param array $data The carts whose fields need preparation.
	 * @return array
	 */
	private function prepare_cart_data_for_api( array $data ) {
		foreach ( $data as $cart ) {
			$cart->cart_contents     = maybe_unserialize( $cart->cart_contents );
			$cart->cart_contents     = $this->get_additional_product_fields( $cart->cart_contents );
			$cart->cart_subtotal     = $this->get_cart_sum_for_product_field( $cart->cart_contents, 'line_subtotal' );
			$cart->cart_total        = $this->get_cart_sum_for_product_field( $cart->cart_contents, 'line_total' );
			$cart->cart_subtotal_tax = $this->get_cart_sum_for_product_field( $cart->cart_contents, 'line_subtotal_tax' );
			$cart->cart_total_tax    = $this->get_cart_sum_for_product_field( $cart->cart_contents, 'line_tax' );
			$cart->cart_recovery_url = $this->get_cart_recovery_url( $cart->cart_hash );
		}

		return $data;
	}

	/**
	 * Get the currency code for the store's base currency.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-23
	 *
	 * @return string
	 */
	private function get_currency_code() : string {
		return get_woocommerce_currency();
	}

	/**
	 * Looks at the value of the specified field in each product in the cart, and gets the sum of those values.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-23
	 *
	 * @param array  $cart_contents The cart contents, whose products have the line items we want for calculating the sum.
	 * @param string $field_name Name of the product field to get.
	 * @return string
	 */
	private function get_cart_sum_for_product_field( array $cart_contents, string $field_name ) : string {
		$line_items = wp_list_pluck( $cart_contents['products'], $field_name );

		if ( empty( $line_items ) || ! is_array( $line_items ) ) {
			return html_entity_decode( wp_strip_all_tags( wc_price( 0 ) ) );
		}

		return html_entity_decode( wp_strip_all_tags( wc_price( array_sum( $line_items ) ) ) );
	}

	/**
	 * Get the account recovery URL.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-23
	 *
	 * @param string $cart_hash The cart hash.
	 * @return string
	 */
	private function get_cart_recovery_url( string $cart_hash ) : string {
		return add_query_arg( 'recover-cart', $cart_hash, home_url() );
	}

	/**
	 * Get additional product fields to display in the API response--SKU, title, thumbnail, and more.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-23
	 *
	 * @param array $cart_contents The original cart contents.
	 * @return array The modified cart contents.
	 */
	private function get_additional_product_fields( array $cart_contents ) : array {
		foreach ( $cart_contents['products'] as $n => $product ) {
			$wc_product = wc_get_product( $product['product_id'] );

			$cart_contents['products'][ $n ]['product_title']     = $wc_product->get_title();
			$cart_contents['products'][ $n ]['product_sku']       = $wc_product->get_sku();
			$cart_contents['products'][ $n ]['product_permalink'] = $wc_product->get_permalink();
			$cart_contents['products'][ $n ]['product_image_url'] = $this->get_product_image_url( $wc_product );
		}

		return $cart_contents;
	}

	/**
	 * Get attachment URL for the product's full-size image.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-25
	 *
	 * @param WC_Product $wc_product The product whose image to get.
	 * @return string
	 */
	private function get_product_image_url( WC_Product $wc_product ) : string {
		return wp_get_attachment_url( $wc_product->get_image_id() );
	}

}

