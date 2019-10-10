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

		$table_name = $wpdb->prefix . 'cc_abandoned_carts';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			cart_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			cart_added datetime NOT NULL default '0000-00-00 00:00:00',
			cart_added_ts
			user_id bigint(20) unsigned NOT NULL DEFAULT '0',
			user_email varchar(200) NOT NULL default '',
			cart_contents longtext NOT NULL,
			PRIMARY KEY (cart_id),
			KEY user_id (user_id)
		) $charset_collate";

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
	 */
	protected function update_cart_data() {
	}
}
