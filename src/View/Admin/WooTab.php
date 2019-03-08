<?php
/**
 * Constant Contact WooCommerce Settings Tab
 *
 * @since 2019-03-07
 * @author Zach Owen <zach@webdevstudios>, Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo\View\Admin;

use WebDevStudios\OopsWP\Utility\Hookable;

if ( ! class_exists( 'WC_Settings_Page' ) ) {
	$woo_settings_abstract = WP_PLUGIN_DIR . '/woocommerce/includes/admin/settings/class-wc-settings-page.php';

	if ( ! file_exists( $woo_settings_abstract ) ) {
		throw new \Exception( __( 'Woo?' ) );
	}

	require_once $woo_settings_abstract;
}

/**
 * Class WooTab
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Admin
 * @since   2019-03-08
 */
class WooTab extends \WC_Settings_Page implements Hookable {
	/**
	 * Settings section ID.
	 *
	 * @var string
	 * @since 2019-03-08
	 */
	protected $id = 'cc_woo';

	/**
	 * Settings Section label.
	 * @var string
	 * @since 2019-03-08
	 */
	protected $label = '';

	/**
	 * WooTab constructor.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function __construct() {
		$this->label = __( 'Constant Contact', 'cc-woo' );
	}

	/**
	 * Register hooks into WooCommerce
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 99 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

		add_filter( 'woocommerce_settings_groups', [ $this, 'add_rest_group' ] );
		add_filter( "woocommerce_settings-{$this->id}", [ $this, 'add_rest_fields' ] );
		add_filter( 'pre_option_store_information_currency', 'get_woocommerce_currency' );
		add_filter( 'woocommerce_admin_settings_sanitize_option_store_information_phone_number', [ $this, 'sanittize_phone_number' ] );
	}

	/**
	 * Add the settings sections.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array|mixed|void
	 */
	public function get_sections() {
		$sections = [
			''                     => __( 'Store Information', 'cc-woo' ),
			'customer_data_import' => __( 'Historical Customer Data Import', 'cc-woo' ),
		];

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get the settings for the settings tab.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $current_section The currently visible section.
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		$settings = [];

		switch ( $current_section ) {
			case '':
			default:
				$settings = [
					[
						'title' => __( 'Store Information', 'cc-woo' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'store_information_options',
					],
					[
						'title' => __( 'First Name', 'cc-woo' ),
						'desc'  => '',
						'id'    => 'store_information_first_name',
						'type'  => 'text',
					],
					[
						'title' => __( 'Last Name', 'cc-woo' ),
						'desc'  => '',
						'id'    => 'store_information_last_name',
						'type'  => 'text',
					],
					[
						'title' => __( 'Phone Number', 'cc-woo' ),
						'id'    => 'store_information_phone_number',
						'desc'  => '',
						'type'  => 'text',
					],
					[
						'title' => __( 'Store Name', 'cc-woo' ),
						'id'    => 'store_information_store_name',
						'desc'  => '',
						'type'  => 'text',
					],
					[
						'title'             => __( 'Currency', 'cc-woo' ),
						'id'                => 'store_information_currency',
						'desc'              => __( 'This field is read from your General settings.', 'cc-woo' ),
						'type'              => 'text',
						'custom_attributes' => [
							'readonly' => 'readonly',
							'size'     => 4,
						],
					],
					[
						'title' => __( 'Contact E-mail Address', 'cc-woo' ),
						'id'    => 'store_information_contact_email',
						'desc'  => '',
						'type'  => 'email',
					],
					[
						'type' => 'sectionend',
						'id'   => 'store_information_options',
					],
				];
				break;

			case 'customer_data_import':
				break;
		}

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
	}

	/**
	 * Add our settings group to the REST API.
	 *
	 * @since 2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @param array $groups The array of groups being sent to the API.
	 * @return array
	 */
	public function add_rest_group( $groups ) {
		$groups[] = [
			'id'          => 'cc_woo',
			'label'       => __( 'Constant Contact WooCommerce', 'cc-woo' ),
			'description' => __( 'This endpoint provides information for the Constant Contact for WooCommerce plugin.', 'cc-woo' ),
		];

		return $groups;
	}

	/**
	 * Add fields to the REST API for our settings.
	 *
	 * @since 2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @param array $settings The array of settings going to the API.
	 * @return array
	 */
	public function add_rest_fields( $settings ) {
		$fields       = [];
		$section_keys = array_keys( $this->get_sections() );

		foreach ( $section_keys as $section_id ) {
			$fields = array_merge( $fields, $this->get_settings( $section_id ) );
		}

		foreach ( $fields as $field ) {
			$field['option_key'] = $field['option_key'] ?? $field['id'];
			$settings[]          = $field;
		}

		return $settings;
	}
}
