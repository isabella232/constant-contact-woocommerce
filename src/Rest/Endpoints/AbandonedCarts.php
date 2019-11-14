<?php
/**
 * REST API endpoint for collection of Abandoned Carts.
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-16
 */

namespace WebDevStudios\CCForWoo\Rest\Endpoints;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Controller;
use WP_REST_Response;
use WP_Error;
use WC_Product;

use WebDevStudios\CCForWoo\AbandonedCarts\CartsTable;
use WebDevStudios\CCForWoo\AbandonedCarts\Cart;
use WebDevStudios\CCForWoo\Rest\Registrar;

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
			Registrar::$namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Check whether a given request has permission to show abandoned carts.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'cc-woo-rest-not-allowed', esc_html__( 'Sorry, you cannot list resources.', 'cc-woo' ), [ 'status' => rest_authorization_required_code() ] );
		}

		return true;
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

		$params   = $request->get_query_params();
		$page     = $this->get_page_param( $params );
		$per_page = $this->get_per_page_param( $params );
		$date_min = $this->get_date_min_param( $params );
		$date_max = $this->get_date_max_param( $params );
		$offset   = 1 === $page ? 0 : ( $page - 1 ) * $per_page;

		$response = [
			'carts'         => $this->get_cart_data( $per_page, $offset, $date_min, $date_max ),
			'currency_code' => $this->get_currency_code(),
			'page'          => $page,
		];

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Get the "page" request param.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-28
	 *
	 * @param array $params The request params.
	 * @return int
	 */
	private function get_page_param( array $params ) : int {
		return (int) isset( $params['page'] ) ? $params['page'] : 1;
	}

	/**
	 * Get the "per_page" request param.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-28
	 *
	 * @param array $params The request params.
	 * @return int
	 */
	private function get_per_page_param( array $params ) : int {
		return (int) isset( $params['per_page'] ) ? $params['per_page'] : 10;
	}

	/**
	 * Get the "date_min" request param.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-28
	 *
	 * @param array $params The request params.
	 * @return string
	 */
	private function get_date_min_param( array $params ) : string {
		return (string) isset( $params['date_min'] ) ? $params['date_min'] : '';
	}

	/**
	 * Get the "date_max" request param.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-28
	 *
	 * @param array $params The request params.
	 * @return string
	 */
	private function get_date_max_param( array $params ) : string {
		return (string) isset( $params['date_max'] ) ? $params['date_max'] : '';
	}

	/**
	 * Get an array of cart data.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @param int    $per_page The per_page value from the REST request.
	 * @param int    $offset The offset for use in SQL query, based on page number specified in REST request.
	 * @param string $date_min The oldest created_at date to get results from.
	 * @param string $date_max The most recent created_at date to get results from.
	 * @return array
	 */
	private function get_cart_data( int $per_page, int $offset, string $date_min, string $date_max ) : array {
		global $wpdb;

		$table_name  = CartsTable::get_table_name();
		$dates_where = $this->get_dates_where( $date_min, $date_max );

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
				{$dates_where}
				ORDER BY cart_updated_ts
				DESC
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL

		return $this->prepare_cart_data_for_api_response( $data );
	}

	/**
	 * Gets the WHERE clause for passing date_min and date_max values via SQL.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-28
	 *
	 * @param string $date_min The oldest created_at date to get results from.
	 * @param string $date_max The most recent created_at date to get results from.
	 * @return string
	 */
	private function get_dates_where( string $date_min, string $date_max ) : string {
		if ( ! empty( $date_min ) && empty( $date_max ) ) {
			return "WHERE cart_created >= '$date_min'";
		}

		if ( empty( $date_min ) && ! empty( $date_max ) ) {
			return "WHERE cart_created <= '$date_max'";
		}

		if ( ! empty( $date_min ) && ! empty( $date_max ) ) {
			return "WHERE cart_created >= '$date_min' AND cart_created <= '$date_max'";
		}

		if ( empty( $date_min ) && empty( $date_max ) ) {
			return '';
		}

		return '';
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
	private function prepare_cart_data_for_api_response( array $data ) {
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

	/**
	 * Get the Abandoned Cart's schema for public consumption.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'cc_woo_abandoned_cart',
			'type'       => 'object',
			'properties' => [
				'cart_id' => [
					'description' => esc_html__( 'Database ID for the abandoned cart.', 'cc-woo' ),
					'type'        => 'integer',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'user_id' => [
					'description' => esc_html__( 'WordPress user ID of the user the cart belongs to; defaults to 0 if a guest or non-logged-in user.', 'cc-woo' ),
					'type'        => 'integer',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'user_email' => [
					'description' => esc_html__( 'The billing email the user entered at checkout before abandoning it. Note that this may be different than the email address the user has in their WordPress user profile.', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_contents' => [
					'description' => esc_html__( 'Object representation of the cart that was abandoned, and its contents, coupon codes, and billing data.', 'cc-woo' ),
					'type'        => 'object',
					'context'     => [ 'view' ],
					'readonly'    => true,
					'properties' => [
						'products' => [
							'description' => esc_html__( 'Key-value listing of products in the cart. Keys are unique WooCommerce-generated keys identifying the cart in the database; values are objects representing the items in the cart.', 'cc-woo' ),
							'type'        => 'array',
							'context'     => [ 'view' ],
							'readonly'    => true,
							'properties' => [
								[
									'key' => [
										'description' => esc_html__( 'Unique WooCommerce-generated key identifying the cart in the database. This differs from the parent-level cart_hash property.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'product_id' => [
										'description' => esc_html__( 'The WooCommerce product ID.', 'cc-woo' ),
										'type'        => 'integer',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'variation_id' => [
										'description' => esc_html__( 'The WooCommerce product variation ID, if applicable.', 'cc-woo' ),
										'type'        => 'integer',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'variation' => [
										'description' => esc_html__( 'Object representation of any applicable variations, where keys are variation names and values are the actual variation selection.', 'cc-woo' ),
										'type'        => 'object',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'quantity' => [
										'description' => esc_html__( 'Item quantity.', 'cc-woo' ),
										'type'        => 'integer',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'data_hash' => [
										'description' => esc_html__( 'MD5 hash of cart items to determine if contents are modified.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'line_tax_data' => [
										'description' => esc_html__( 'Line subtotal tax and total tax data.', 'cc-woo' ),
										'type'        => 'object',
										'context'     => [ 'view' ],
										'readonly'    => true,
										'properties'  => [
											'subtotal' => [
												'description' => esc_html__( 'Line subtotal tax data.', 'cc-woo' ),
												'type'        => 'string',
												'context'     => [ 'view' ],
												'readonly'    => true,
											],
											'total' => [
												'description' => esc_html__( 'Line total tax data.', 'cc-woo' ),
												'type'        => 'string',
												'context'     => [ 'view' ],
												'readonly'    => true,
											],
										]
									],
									'line_subtotal' => [
										'description' => esc_html__( 'Line subtotal.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'line_subtotal_tax' => [
										'description' => esc_html__( 'Line subtotal tax.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'line_total' => [
										'description' => esc_html__( 'Line total.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'line_tax' => [
										'description' => esc_html__( 'Line total tax.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'data' => [
										'description' => esc_html__( 'Misc. product data in key-value pairs.', 'cc-woo' ),
										'type'        => 'object',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'product_title' => [
										'description' => esc_html__( 'The product title.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'product_sku' => [
										'description' => esc_html__( 'The product SKU.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'product_permalink' => [
										'description' => esc_html__( 'Permalink to the product page.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									],
									'product_image_url' => [
										'description' => esc_html__( 'URL to the full-size featured image for the product if one exists.', 'cc-woo' ),
										'type'        => 'string',
										'context'     => [ 'view' ],
										'readonly'    => true,
									]
								]
							]
						],
						'coupons' => [
							'description' => esc_html__( '', 'cc-woo' ),
							'type'        => 'string',
							'context'     => [ 'view' ],
							'readonly'    => true,
						],
					],
				],
				'cart_updated' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_updated_ts' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_created' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_created_ts' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_hash' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_subtotal' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_total' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_subtotal_tax' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_total_tax' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'cart_recovery_url' => [
					'description' => esc_html__( '', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
			]
		];

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for Abandoned Carts.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return [
			'page'     => [],
			'per_page' => [],
			'date_min' => [],
			'date_max' => [],
		];
	}

}

