<?php
/**
 * Class for managing the Constant Contact <-> WooCommerce API key.
 *
 * @since 2019-03-21
 * @package cc-woo-api
 */

namespace WebDevStudios\CCForWoo\Api;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * KeyManager class
 *
 * @uses Hookable
 * @since 2019-03-21
 * @author Zach Owen <zach@webdevstudios>
 */
class KeyManager implements Hookable {
	/**
	 * Register hooks with WordPress
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function register_hooks() {
		add_filter( 'query', [ $this, 'maybe_revoke_api_key' ] );
	}

	/**
	 * Check the database query to see if we're removing a Woo API key.
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $query Database query.
	 * @return string
	 */
	public function maybe_revoke_api_key( string $query ) : string {
		if ( ! $this->is_cc_api_revocation_query( $query ) ) {
			return $query;
		}

		if ( ! $this->user_has_cc_key() ) {
			return $query;
		}

		/**
		 * Fires when a WooCommerce API key is revoked.
		 *
		 * @since 2019-03-21
		 */
		do_action( 'cc_woo_key_revoked' );

		return $query;
	}

	/**
	 * Check whether the query meets our criteria.
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $query The database query.
	 * @return bool
	 */
	private function is_cc_api_revocation_query( string $query ) : bool {
		if ( false === stripos( $query, 'DELETE' ) ) {
			return false;
		}

		if ( false === stripos( $query, 'woocommerce_api_keys' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the current user has a Constant Contact API key.
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function user_has_cc_key() : bool {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$query = <<<SQL
SELECT
	key_id
FROM
{$GLOBALS['wpdb']->prefix}woocommerce_api_keys
WHERE
	user_id = %d
AND
	(
		description LIKE '%Constant Contact%'
	OR
		description LIKE '%ConstantContact%'
	)
SQL;

		return ! empty( $GLOBALS['wpdb']->get_col( $GLOBALS['wpdb']->prepare( $query, $user_id ) ) );
	}
}
