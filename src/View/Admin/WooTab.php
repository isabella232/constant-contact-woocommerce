<?php

namespace WebDevStudios\CCForWoo\View\Admin;

use WebDevStudios\OopsWP\Utility\Hookable;

if ( ! class_exists( 'WC_Settings_Page' ) ) {
	$woo_settings_abstract =  WP_PLUGIN_DIR . '/woocommerce/includes/admin/settings/class-wc-settings-page.php';

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
	 * @var string
	 * @since 2019-03-08
	 */
	protected $id = 'cc_woo';

	/**
	 * @var string
	 * @since 2019-03-08
	 */
	protected $label = 'Constant Contact';

	/**
	 * WooTab constructor.
	 *
	 * @since  2019-03-08
	 */
	public function __construct() {
		// noop
	}

	/**
	 * @since  2019-03-08
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 99 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

		add_filter( 'woocommerce_settings_groups', function( $locations ) {
			$locations[] = [
				'id'          => 'cc_woo',
				'label'       => 'CC Woo Test',
				'description' => 'Blah',
			];
		} );

		add_filter( 'woocommerce_settings-' . $this->id, function( $settings ) {
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
		} );
	}

	/**
	 * @since  2019-03-08
	 * @return array|mixed|void
	 */
	public function get_sections() {
		$sections = [
			''                     => __( 'Store Information', 'my-textdomain' ),
			'customer_data_import' => __( 'Historical Customer Data Import', 'my-textdomain' )
		];

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * @param string $current_section
	 *
	 * @since  2019-03-08
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		$settings = [];

		switch ( $current_section ) {
			case '':
			default:
				$settings = [
					[
						'title' => 'Store Information',
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'store_information_options',
					],
					[
						'title' => 'First Name',
						'desc'  => '',
						'id'    => 'store_information_first_name',
						'type'  => 'text',
					],
					[
						'title' => 'Last Name',
						'desc'  => '',
						'id'    => 'store_information_last_name',
						'type'  => 'text',
					],
					[
						'title' => 'Phone Number',
						'id'    => 'store_information_phone_number',
						'desc'  => '',
						'type'  => 'text',
					],
					[
						'title' => 'Store Name',
						'id'    => 'store_information_store_name',
						'desc'  => '',
						'type'  => 'text',
					],
					[
						'title'             => 'Currency',
						'id'                => 'store_information_currency',
						'desc'              => 'This field is read from your General settings.',
						'type'              => 'text',
						'custom_attributes' => [
							'readonly' => 'readonly',
							'value'    => 'a',
						],
					],
					[
						'title' => 'Contact E-mail Address',
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
}
