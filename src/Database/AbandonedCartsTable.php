<?php
/**
 * Class to handle creation and deletion of abandoned carts table.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-09
 */

namespace WebDevStudios\CCForWoo\Database;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class AbandonedCartsTable
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-09
 */
class AbandonedCartsTable extends Service {

	/**
	 * Current version of abandoned carts table.
	 */
	const CC_ABANDONED_CARTS_DB_VERSION = '1.0';

	/**
	 * Option name for abandoned carts db version.
	 */
	const CC_ABANDONED_CARTS_DB_VERSION_OPTION = 'cc_abandoned_carts_db_version';

	/**
	 * Abandoned carts table name.
	 */
	const CC_ABANDONED_CARTS_TABLE = 'cc_abandoned_carts';

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-09
	 */
	public function register_hooks() {
		add_action( 'plugins_loaded', [ $this, 'update_db_check' ] );
		add_action( 'woocommerce_after_template_part', [ $this, 'check_template' ], 10, 4 );
	}

	/**
	 * Create abandoned carts table.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-10
	 */
	public function create_table() {

		global $wpdb;

		$table_name = $wpdb->prefix . self::CC_ABANDONED_CARTS_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			cart_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			user_email varchar(200) NOT NULL default '',
			cart_contents longtext NOT NULL,
			cart_updated datetime NOT NULL default '0000-00-00 00:00:00',
			cart_updated_ts int(11) unsigned NOT NULL default 0,
			PRIMARY KEY (cart_id),
			UNIQUE KEY user (user_id, user_email)
		) {$charset_collate}";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( self::CC_ABANDONED_CARTS_DB_VERSION_OPTION, self::CC_ABANDONED_CARTS_DB_VERSION );
	}

	/**
	 * Check if table exists and is up-to-date.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-10
	 */
	public function update_db_check() {
		if ( self::CC_ABANDONED_CARTS_DB_VERSION !== get_site_option( self::CC_ABANDONED_CARTS_DB_VERSION_OPTION ) ) {
			$this->create_table();
		}
	}

	/**
	 * Check current WC template.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-10
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
	}

	/**
	 * Update current cart session data in db.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-10
	 * @return void
	 */
	protected function update_cart_data() {

		$cart = WC()->cart->get_cart();
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
		$time_added = current_time( 'mysql' );
		$time_added_ts = strtotime( $time_added );

		global $wpdb;

		// Insert/update cart data.
		$table_name = $wpdb->prefix . self::CC_ABANDONED_CARTS_TABLE;
		$wpdb->query(
			$wpdb->prepare(
				//@codingStandardsIgnoreStart
				"INSERT INTO {$table_name} (`user_id`, `user_email`, `cart_contents`, `cart_updated`, `cart_updated_ts`) VALUES (%d, %s, %s, %s, %d)
				ON DUPLICATE KEY UPDATE `cart_updated` = VALUES(`cart_updated`), `cart_updated_ts` = VALUES(`cart_updated_ts`), `cart_contents` = VALUES(`cart_contents`)",
				//@codingStandardsIgnoreEnd
				$user_id,
				$user_email,
				maybe_serialize( $cart ),
				$time_added,
				$time_added_ts
			)
		);
	}
}
