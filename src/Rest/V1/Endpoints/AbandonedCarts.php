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
	 * Register the Abandoned Carts endpoint.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 */
	public function register_routes() {
		register_rest_route(
			Registrar::$namespace, '/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
					'args'                => $this->get_endpoint_args_for_item_schema( false ),
				],
				'schema' => null,
			]
		);
	}

	/**
	 * Register the Abandoned Carts endpoint.
	 *
	 * Note: Type and return hints are intentionally avoided here to match abstract method signature and prevent PHP warnings.
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

		return [
			'data' => $this->get_cart_data( $per_page, $offset ),
			'page' => $page,
		];
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
					HEX(cart_hash) as cart_hash
				FROM {$table_name}
				ORDER BY cart_updated_ts
				DESC
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

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
			$cart->cart_contents                  = maybe_unserialize( $cart->cart_contents );
			$cart->cart_contents['currency_code'] = $this->get_currency_code();
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

	// private function get_cart_totals_for_contents( array $cart_contents ) : array {
	// 	return [

	// 	];
	// }

	/**
	 * Permissions for reading the the Abandoned Carts endpoint.
	 *
	 * Note: Type and return hints are intentionally avoided here to match abstract method signature and prevent PHP warnings.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @todo Require auth via token!
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return bool
	 */
	public function get_items_permissions_check( $request ) {
		return true;
	}

}

