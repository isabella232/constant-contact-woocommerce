<?php
/**
 * Class for object representation of Abandoned Cart.
 *
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\AbandonedCarts
 * @since 2019-10-16
 */

namespace WebDevStudios\CCForWoo\AbandonedCarts;

use stdObject;
use stdClass;

/**
 * Class Cart
 *
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @since 2019-10-16
 */
class Cart {

	/**
	 * Abandoned carts table name, including wpdb prefix.
	 *
	 * @since 2019-10-16
	 *
	 * @var int
	 */
	private $table_name;

	/**
	 * The cart object.
	 *
	 * @since 2019-10-16
	 *
	 * @var stdClass
	 */
	private $cart;

	/**
	 * The cart's ID.
	 *
	 * @since 2019-10-16
	 *
	 * @var int
	 */
	private $cart_id;

	/**
	 * The cart's User ID field.
	 *
	 * @since 2019-10-16
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 * The cart's User Email field.
	 *
	 * @since 2019-10-16
	 *
	 * @var string
	 */
	private $user_email;

	/**
	 * The cart's contents.
	 *
	 * @since 2019-10-16
	 *
	 * @var array
	 */
	private $cart_contents;

	/**
	 * The cart's updated time in 'mysql' time format.
	 *
	 * @since 2019-10-16
	 *
	 * @var string
	 */
	private $cart_updated;

	/**
	 * The cart's updated timestamp.
	 *
	 * @since 2019-10-16
	 *
	 * @var string
	 */
	private $cart_updated_ts;

	/**
	 * The cart's hash string.
	 *
	 * @since 2019-10-16
	 *
	 * @var string
	 */
	private $cart_hash;

	/**
	 * Constructor.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @param int $cart_id The cart ID.
	 */
	public function __construct( int $cart_id ) {
		global $wpdb;

		$this->cart_id    = $cart_id;
		$this->table_name = $wpdb->prefix . CartsTable::TABLE_NAME;

		$this->setup_cart_data();
	}

	/**
	 * Setup cart data for use in other methods.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 */
	private function setup_cart_data() {
		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared table name in SQL.
		$this->cart = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT cart_id,
						user_id,
						user_email,
						cart_contents,
						cart_updated,
						cart_updated_ts,
						HEX(cart_hash) as cart_hash
				FROM {$this->table_name}
				WHERE cart_id = %d",
				$this->cart_id
			)
		);
		// phpcs:enable

		$this->user_id         = $this->cart->user_id;
		$this->user_email      = $this->cart->user_email;
		$this->cart_contents   = maybe_unserialize( $this->cart->cart_contents );
		$this->cart_updated    = $this->cart->cart_updated;
		$this->cart_updated_ts = $this->cart->cart_updated_ts;
		$this->cart_hash       = $this->cart->cart_hash;
	}

	/**
	 * Cart getter.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @return stdClass
	 */
	public function get_cart() : stdClass {
		return $this->cart;
	}

	/**
	 * User ID getter.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @return int
	 */
	public function get_user_id() : int {
		return $this->user_id;
	}

	/**
	 * User email getter.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @return string
	 */
	public function get_user_email() : string {
		return $this->user_email;
	}

	/**
	 * Cart contents getter.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @return array
	 */
	public function get_cart_contents() : array {
		return $this->cart_contents;
	}

	/**
	 * Cart updated getter.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @return string
	 */
	public function get_cart_updated() : string {
		return $this->cart_updated;
	}

	/**
	 * Cart updated timestamp getter.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @return string
	 */
	public function get_cart_updated_ts() : array {
		return $this->cart_updated_ts;
	}

	/**
	 * Cart hash getter.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-16
	 *
	 * @return string
	 */
	public function get_cart_hash() : string {
		return $this->cart_hash;
	}

}
