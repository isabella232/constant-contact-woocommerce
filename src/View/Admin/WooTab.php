<?php
/**
 * Constant Contact WooCommerce Settings Tab
 *
 * @since   2019-03-07
 * @author  Zach Owen <zach@webdevstudios>, Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo\View\Admin;

use WebDevStudios\CCForWoo\Meta\ConnectionStatus;
use WebDevStudios\CCForWoo\Settings\SettingsModel;
use WebDevStudios\CCForWoo\Settings\SettingsValidator;
use WebDevStudios\CCForWoo\Utility\NonceVerification;
use WebDevStudios\CCForWoo\View\Checkout\NewsletterPreferenceCheckbox;
use WebDevStudios\OopsWP\Utility\Hookable;
use WC_Settings_Page;

/**
 * Class WooTab
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Admin
 * @since   2019-03-08
 */
class WooTab extends WC_Settings_Page implements Hookable {
	use NonceVerification;

	/**
	 * Store owner first name field.
	 *
	 * @since 2019-03-12
	 */
	const FIRST_NAME_FIELD = 'cc_woo_store_information_first_name';

	/**
	 * Store owner last name field.
	 *
	 * @since 2019-03-12
	 */
	const LAST_NAME_FIELD = 'cc_woo_store_information_last_name';

	/**
	 * Store phone number field.
	 *
	 * @since 2019-03-12
	 */
	const PHONE_NUMBER_FIELD = 'cc_woo_store_information_phone_number';

	/**
	 * Store name field.
	 *
	 * @since 2019-03-12
	 */
	const STORE_NAME_FIELD = 'cc_woo_store_information_store_name';

	/**
	 * Store currency field.
	 *
	 * @since 2019-03-12
	 */
	const CURRENCY_FIELD = 'cc_woo_store_information_currency';

	/**
	 * Store country code field.
	 *
	 * @since 2019-03-12
	 */
	const COUNTRY_CODE_FIELD = 'cc_woo_store_information_country_code';

	/**
	 * Store contact e-mail field.
	 *
	 * @since 2019-03-12
	 */
	const EMAIL_FIELD = 'cc_woo_store_information_contact_email';

	/**
	 * Historical customer data import field.
	 *
	 * @since 2019-03-12
	 */
	const ALLOW_HISTORICAL_CUSTOMER_IMPORT_FIELD = 'cc_woo_customer_data_allow_import';

	/**
	 * Store has user consent field.
	 *
	 * @since 2019-03-12
	 */
	const STORE_AFFIRMS_CONSENT_TO_MARKET_FIELD = 'cc_woo_customer_data_opt_in_consent';

	/**
	 * Settings section ID.
	 *
	 * @var string
	 * @since 2019-03-08
	 */
	protected $id = 'cc_woo';

	/**
	 * Settings Section label.
	 *
	 * @var string
	 * @since 2019-03-08
	 */
	protected $label = '';

	/**
	 * Array of form errors to display with their fields.
	 *
	 * @since 2019-03-08
	 * @var array
	 */
	private $errors = [];

	/**
	 * Instance of the ConnectionStatus object.
	 *
	 * @var ConnectionStatus
	 * @since 2019-03-21
	 */
	private $connection;

	/**
	 * WooTab constructor.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function __construct() {
		$this->label        = __( 'Constant Contact', 'cc-woo' );
		$this->nonce_name   = '_cc_woo_nonce';
		$this->nonce_action = 'cc-woo-connect-action';
		$this->connection   = new ConnectionStatus();
	}

	/**
	 * Register hooks into WooCommerce
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function register_hooks() {
		add_filter( 'woocommerce_settings_tabs_array', [ $this, 'add_settings_page' ], 99 );
		add_action( "woocommerce_settings_{$this->id}", [ $this, 'output' ] );

		// Output settings sections.
		add_action( "woocommerce_sections_{$this->id}", [ $this, 'output_sections' ] );

		// CC API interactions.
		add_action( "woocommerce_sections_{$this->id}", [ $this, 'maybe_redirect_to_cc' ] );
		add_action( "woocommerce_sections_{$this->id}", [ $this, 'maybe_update_connection_status' ] );

		// REST API.
		add_filter( 'woocommerce_settings_groups', [ $this, 'add_rest_group' ] );
		add_filter( "woocommerce_settings-{$this->id}", [ $this, 'add_rest_fields' ] );

		// Form.
		add_filter( 'pre_option_' . self::CURRENCY_FIELD, 'get_woocommerce_currency' );
		add_filter( 'pre_option_' . self::COUNTRY_CODE_FIELD, [ $this, 'get_woo_country' ] );
		add_filter( 'pre_update_option_' . self::STORE_AFFIRMS_CONSENT_TO_MARKET_FIELD, [ $this, 'maybe_prevent_opt_in_consent' ] );
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . self::PHONE_NUMBER_FIELD, [ $this, 'sanitize_phone_number' ] );
		add_filter( "woocommerce_get_settings_{$this->id}", [ $this, 'maybe_add_connection_button' ] );
		add_action( 'woocommerce_admin_field_cc_connection_button', [ $this, 'add_cc_connection_button' ] );
		add_action( 'woocommerce_admin_field_cc_cta_button', [ $this, 'render_cta_button' ] );

		// Save actions.
		add_filter( 'woocommerce_settings_start', [ $this, 'validate_option_values' ], 10, 3 );
		add_action( "woocommerce_settings_save_{$this->id}", [ $this, 'save' ] );
		add_action( "woocommerce_settings_save_{$this->id}", [ $this, 'update_setup_option' ] );
	}

	/**
	 * Add the settings sections.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
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
	 * @return array
	 */
	public function get_settings() {
		if ( ! $this->connection->connection_was_attempted() ) {
			return $this->get_filtered_settings( $this->get_default_settings_options() );
		}

		if ( ! $this->connection->is_connected() ) {
			return $this->get_filtered_settings(
				array_merge( $this->get_connection_attempted_options(), $this->get_default_settings_options() )
			);
		}

		$GLOBALS['hide_save_button'] = true;

		return $this->get_filtered_settings( $this->get_connection_established_options() );
	}

	/**
	 * Run the settings for the current connection status through the WooCommerce settings filter.
	 *
	 * @param array $settings Settings options.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return array
	 */
	private function get_filtered_settings( array $settings ) {
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $GLOBALS['current_section'] );
	}

	/**
	 * Get the default view for our settings page.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return array
	 */
	private function get_default_settings_options() {
		$settings = [];

		switch ( $GLOBALS['current_section'] ) {
			case '':
			default:
				$settings = $this->get_store_information_settings();
				break;

			case 'customer_data_import':
				$settings = $this->get_customer_data_settings();
				break;
		}

		$settings = $this->process_errors( $settings );
		$settings = $this->adjust_styles( $settings );

		return $settings;
	}

	/**
	 * Add our settings group to the REST API.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $groups The array of groups being sent to the API.
	 *
	 * @return array
	 */
	public function add_rest_group( $groups ) {
		$groups[] = [
			'id'          => 'cc_woo',
			'label'       => __( 'Constant Contact WooCommerce', 'cc-woo' ),
			'description' => __( 'This endpoint provides information for the Constant Contact for WooCommerce plugin.',
				'cc-woo' ),
		];

		return $groups;
	}

	/**
	 * Add fields to the REST API for our settings.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $settings The array of settings going to the API.
	 *
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

	/**
	 * Get the section options for an attempted connection that failed.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return array
	 */
	private function get_connection_attempted_options() {
		return [
			[
				'title' => __( 'There was a problem connecting your store to Constant Contact. Please try again.', 'cc-woo' ),
				'type'  => 'title',
				'id'    => 'cc_woo_connection_attempted_heading',
			],
		];
	}

	/**
	 * Get the settings for the main section if already connected to Constant Contact.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return array
	 */
	private function get_connection_established_options() {
		return [
			[
				'title' => __( 'Congratulations! Your store is connected to Constant Contact.', 'cc-woo' ),
				'type'  => 'title',
				'id'    => 'cc_woo_connection_established_heading',
			],
			[
				'type' => 'cc_cta_button',
			],
			[
				'type' => 'sectionend',
				'id'   => 'cc_woo_store_information_settings',
			],
		];
	}

	/**
	 * Render the call-to-action button in the admin.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 */
	public function render_cta_button() {
		?>
		<a class="button" href="https://constantcontact.com"><?php esc_html_e( 'Constant Contact CTA', 'cc-woo' ); ?></a>
		<?php
	}

	/**
	 * Gets the settings for the Store Information section.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	private function get_store_information_settings() {
		$readonly_from_general_settings = __( 'This field is read from your General settings.', 'cc-woo' );

		return [
			[
				'title' => __( 'Store Information', 'cc-woo' ),
				'type'  => 'title',
				'desc'  => 'All fields are required.',
				'id'    => 'cc_woo_store_information_settings',
			],
			[
				'title'             => __( 'First Name', 'cc-woo' ),
				'desc'              => '',
				'id'                => self::FIRST_NAME_FIELD,
				'type'              => 'text',
				'custom_attributes' => [
					'required' => 'required',
				],
			],
			[
				'title'             => __( 'Last Name', 'cc-woo' ),
				'desc'              => '',
				'id'                => self::LAST_NAME_FIELD,
				'type'              => 'text',
				'custom_attributes' => [
					'required' => 'required',
				],
			],
			[
				'title'             => __( 'Phone Number', 'cc-woo' ),
				'id'                => self::PHONE_NUMBER_FIELD,
				'desc'              => '',
				'type'              => 'text',
				'custom_attributes' => [
					'required' => 'required',
				],
			],
			[
				'title'             => __( 'Store Name', 'cc-woo' ),
				'id'                => self::STORE_NAME_FIELD,
				'desc'              => '',
				'type'              => 'text',
				'custom_attributes' => [
					'required' => 'required',
				],
			],
			[
				'title'             => __( 'Contact E-mail Address', 'cc-woo' ),
				'id'                => self::EMAIL_FIELD,
				'desc'              => '',
				'type'              => 'email',
				'custom_attributes' => [
					'required' => 'required',
				],
			],
			[
				'title'             => __( 'Currency', 'cc-woo' ),
				'id'                => self::CURRENCY_FIELD,
				'desc'              => $readonly_from_general_settings,
				'type'              => 'text',
				'custom_attributes' => [
					'readonly' => 'readonly',
					'size'     => 4,
				],
			],
			[
				'title'             => __( 'Country Code', 'cc-woo' ),
				'id'                => self::COUNTRY_CODE_FIELD,
				'desc'              => $readonly_from_general_settings,
				'type'              => 'text',
				'custom_attributes' => [
					'readonly' => 'readonly',
					'size'     => 4,
				],
			],
			[
				'title' => __( 'Pre-select customer marketing sign-up at checkout', 'cc-woo' ),
				'desc'  => __( 'Customers will see an option to opt-in to email marketing at checkout. Checking this box will select that option by default.', 'cc-woo' ),
				'type'  => 'checkbox',
				'id'    => NewsletterPreferenceCheckbox::STORE_NEWSLETTER_DEFAULT_OPTION,
			],
			[
				'type' => 'sectionend',
				'id'   => 'cc_woo_store_information_settings',
			],
		];
	}

	/**
	 * Get the customer marketing settings.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	private function get_customer_data_settings() {
		$settings = [
			[
				'title' => __( 'Historical Customer Data Import', 'cc-woo' ),
				'id'    => 'cc_woo_customer_data_settings',
				'type'  => 'title',
			],
			[
				'title' => __( 'User information consent', 'cc-woo' ),
				'desc'  => __( 'By checking this box, you are stating that you have your customers\' permission to email them.',
					'cc-woo' ),
				'type'  => 'checkbox',
				'id'    => self::STORE_AFFIRMS_CONSENT_TO_MARKET_FIELD,
			],
			[
				'title'   => __( 'Import historical customer data', 'cc-woo' ),
				'desc'    => __( 'Selecting Yes here will enable the ability to import your historical customer information to Constant Contact.',
					'cc-woo' ),
				'type'    => 'select',
				'id'      => self::ALLOW_HISTORICAL_CUSTOMER_IMPORT_FIELD,
				'css'     => 'width:100px;display:block;margin-bottom:0.5rem;',
				'default' => 'no',
				'options' => [
					'no'  => 'No',
					'yes' => 'Yes',
				],
			],
		];

		if ( $this->store_owner_confirmed_customer_consent_to_market() ) {
			$settings[] = [
				'id'    => 'cc_woo_customer_data_opt_in_import',
				'type'  => 'button',
				'title' => 'Import Customer Data',
			];
		}

		$settings[] = [
			'type' => 'sectionend',
			'id'   => 'cc_woo_customer_data_settings',
		];

		return $settings;
	}

	/**
	 * Check whether a store owner has confirmed they have customer consent to market to them.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-14
	 * @return bool
	 */
	private function store_owner_confirmed_customer_consent_to_market() {
		return 'yes' === get_option( self::STORE_AFFIRMS_CONSENT_TO_MARKET_FIELD );
	}

	/**
	 * Prevent the opt-in consent from being set if importing is not enabled.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param mixed $value The value being set for the opt-in option.
	 *
	 * @return string
	 */
	public function maybe_prevent_opt_in_consent( $value ) {
		$allow_import = get_option( self::ALLOW_HISTORICAL_CUSTOMER_IMPORT_FIELD );

		if ( 'no' === $allow_import ) {
			return 'no';
		}

		return $value;
	}

	/**
	 * Displays the Constant Contact connection button when the form is validated and a connection is not already established.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $settings The current settings array.
	 *
	 * @return array
	 */
	public function maybe_add_connection_button( $settings ) {
		if ( ! $this->meets_connect_requirements() || $this->connection->is_connected() ) {
			return $settings;
		}

		$settings[] = [
			'type' => 'cc_connection_button',
		];

		return $settings;
	}

	/**
	 * Add the Constant Contact connection button when displaying the form.
	 *
	 * Will display as a "Disconnect" button if the connection has already been established.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function add_cc_connection_button() {
		$connected = get_option( ConnectionStatus::CC_CONNECTION_ESTABLISHED_KEY );
		$value     = $connected ? 'disconnect' : 'connect';
		$message   = $connected
			? __( 'Disconnect from Constant Contact', 'cc-woo' )
			: __( 'Connect with Constant Contact', 'cc-woo' );

		wp_nonce_field( $this->nonce_action, $this->nonce_name );
		?>
		<button class="button button-primary" type="submit" name="cc_woo_action" value="<?php echo esc_attr( $value ); ?>">
			<?php echo esc_html( $message ); ?>
		</button>
		<?php
	}

	/**
	 * Maybe redirects to Constant Contact to connect accounts.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-19
	 * @return void
	 */
	public function maybe_redirect_to_cc() {
		if ( ! $this->requested_connect_to_cc() ) {
			return;
		}

		add_filter( 'allowed_redirect_hosts', [ $this, 'allow_redirect_to_cc' ] );

		$url = wp_parse_url( get_home_url() );

		wp_safe_redirect( 'https://shoppingcart.constantcontact.com/auth/woocommerce/WhoDis?storeDomain="' . $url['host'] . '"' );
		exit;
	}

	/**
	 * Check whether a connection request to CC has been triggered.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-19
	 * @return bool
	 */
	private function requested_connect_to_cc() {
		if ( ! $this->has_valid_nonce() ) {
			return false;
		}

		// phpcs:disable -- Ignoring $_POST warnings.
		return (
			isset( $_POST['cc_woo_action'] )
			&& 'connect' === filter_var( $_POST['cc_woo_action'], FILTER_SANITIZE_STRING )
		);
		// phpcs:enable
	}

	/**
	 * Add the Constant Contact host to the list of allowed hosts.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-19
	 * @return array
	 */
	public function allow_redirect_to_cc() {
		$hosts[] = 'shoppingcart.constantcontact.com';

		return $hosts;
	}

	/**
	 * Check to see if the settings meet the requirements to connect to CC.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function meets_connect_requirements() {
		$model = new SettingsModel(
			get_option( self::FIRST_NAME_FIELD, '' ),
			get_option( self::LAST_NAME_FIELD, '' ),
			get_option( self::PHONE_NUMBER_FIELD, '' ),
			get_option( self::STORE_NAME_FIELD, '' ),
			get_option( self::CURRENCY_FIELD, '' ),
			get_option( self::COUNTRY_CODE_FIELD ),
			get_option( self::EMAIL_FIELD ),
			get_option( self::ALLOW_HISTORICAL_CUSTOMER_IMPORT_FIELD, 'no' ),
			get_option( self::STORE_AFFIRMS_CONSENT_TO_MARKET_FIELD, 'no' )
		);

		$validator = new SettingsValidator( $model );

		return $validator->is_valid();
	}

	/**
	 * Listen for GET request that establishes connection.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 */
	public function maybe_update_connection_status() {
		$success = filter_input( INPUT_GET, 'success', FILTER_SANITIZE_NUMBER_INT );
		$user_id = filter_input( INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT );

		if ( is_null( $success ) || is_null( $user_id ) ) {
			return;
		}

		$this->connection->set_connection( $success );
	}

	/**
	 * Verify that all option values meet the minimum requirements.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return void
	 */
	public function validate_option_values() {
		if ( ! get_option( 'constant_contact_for_woo_has_setup' ) ) {
			return;
		}

		$settings = $this->get_store_information_settings();

		foreach ( $settings as $field ) {
			$this->validate_value( $field );
		}
	}

	/**
	 * Validate a field's value is set, otherwise log an error.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $field The field to validate.
	 *
	 * @return void
	 */
	private function validate_value( $field ) {
		if ( in_array( $field['type'], [ 'title', 'sectionend' ], true ) ) {
			return;
		}

		if ( ! empty( get_option( $field['id'] ) ) ) {
			return;
		}

		// translators: placeholder is the field's title.
		$this->errors[ $field['id'] ] = sprintf( __( 'The "%s" field is required to connect to Constant Contact.',
			'cc-woo' ), $field['title'] );
	}

	/**
	 * Sanitize the phone number to only include digits, -, and (, )
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param mixed $value The incoming phone number value.
	 *
	 * @return string
	 */
	public function sanitize_phone_number( $value ) {
		return is_scalar( $value ) ? preg_replace( '/[^\d-()+]+/', '', $value ) : '';
	}

	/**
	 * Process errors logged for form fields.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $settings An array of settings fields.
	 *
	 * @return array
	 */
	private function process_errors( $settings ) {
		if ( empty( $this->errors ) ) {
			return $settings;
		}

		foreach ( $settings as $key => &$field ) {
			if ( ! isset( $this->errors[ $field['id'] ] ) ) {
				continue;
			}

			if ( ! isset( $field['desc'] ) ) {
				$field['desc'] = '';
			}

			$field['desc'] .= $this->errors[ $field['id'] ];
		}

		return $settings;
	}

	/**
	 * Update the setup option.
	 *
	 * This is used to prevent errors from appearing before the user has submitted the form,
	 * i.e. after a fresh installation.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function update_setup_option() {
		update_option( 'constant_contact_for_woo_has_setup', true );
	}

	/**
	 * Make all form elements for our settings `display:block`.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $settings Array of settings to adjust.
	 *
	 * @return array
	 */
	private function adjust_styles( $settings ) {
		foreach ( $settings as $key => $field ) {
			if ( ! empty( $field['css'] ) ) {
				continue;
			}

			if ( in_array( $field['type'], [ 'title', 'sectionend' ], true ) ) {
				continue;
			}

			$settings[ $key ]['css'] = 'display: block';
		}

		return $settings;
	}

	/**
	 * Get the Country code from the WooCommerce settings.
	 *
	 * @since 2019-03-15
	 * @author Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	public function get_woo_country() : string {
		return wc_get_base_location()['country'] ?? '';
	}
}
