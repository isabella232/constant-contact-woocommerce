describe('As a store owner', function() {
	it( 'Should not have the opt-in default checked', function(){
		cy.adminLogin(Cypress.env('cc_woo_url')).then(() => {
			admin_set_opt_in_check_state( false )
		})
	})
})

run_test_suite()

describe('As a store owner', function() {
	it( 'Should have the opt-in default checked', function(){
		cy.adminLogin(Cypress.env('cc_woo_url')).then(() => {
			admin_set_opt_in_check_state( true )
		})
	})
})

run_test_suite()

/**
 * Fills out the checkout form with fixture data.o
 *
 * @since 2019-03-20
 * @author Zach Owen <zach.owen@webdevstudios.com>
 */
function fill_out_checkout_form() {
	cy.fixture('checkout-details').then((data) => {
		for ( var key in data.text_fields ) {
			cy.get( '#' + key ).clear().type( data.text_fields[ key ] )
		}

		for ( key in data.select_fields ) {
			cy.get( '#' + key ).select( data.select_fields[ key ], { "force": true })
		}
	})
}

/**
 * Run the test suite.
 *
 * This helper method contains our tests to ensure the default opt-in checkbox
 * has no bearing on (logged in) user choices, once a user has established
 * a preference by checking out once.
 *
 * @since 2019-03-20
 * @author Zach Owen <zach@webdevstudios.com>
 */
function run_test_suite() {
	describe('As a logged-in customer visiting the checkout page', function(){
		it('Should checkout with the opt-out checkbox uncheckd', function() {
			cy.customerLogin(Cypress.env('test_product_url')).then(() => {
				do_customer_checkout( false )
			})
		})

		it('Should checkout and see that the opt-in checkbox is still unchecked', function() {
			cy.customerLogin(Cypress.env('test_product_url')).then(() => {
				verify_opt_in_check_state( false )
			})
		})

		it('Should checkout with the opt-out checkbox checked', function() {
			cy.customerLogin(Cypress.env('test_product_url')).then(() => {
				do_customer_checkout( true )
			})
		})

		it('Should checkout with a product and see that the checkbox for opt-in is still checked from previous selection', function() {
			cy.customerLogin(Cypress.env('test_product_url')).then(() => {
				verify_opt_in_check_state( true )
			})
		})
	})
}

/**
 * Check out the customer.
 *
 * @since 2019-03-20
 * @author Zach Owen <zach@webdevstudios.com>
 * @param {Boolean} opt_in_checked Whether the opt-in checkbox should be checked.
 */
function do_customer_checkout( opt_in_checked ) {
	cy.get('button[name="add-to-cart"]').click()
	cy.get('a[href*="/cart/"]:first').click()
	cy.get('a[href*="/checkout/"]:first').click()
	fill_out_checkout_form()
	cy.get('#customer_newsletter_opt_in')[ opt_in_checked ? 'check' : 'uncheck' ]()
	cy.get('#payment_method_cod').check({"force": true})
	cy.get('#place_order').click({"force": true}).then(() => {
		cy.wait(500)
		cy.get('body').should('contain', 'Order received')
	})
}

/**
 * Verify the opt-in checked state.
 *
 * @since 2019-03-20
 * @author Zach Owen <zach@webdevstudios.com>
 * @param {Boolean} opt_in_checked Whether the opt-in checkbox should be checked.
 */
function verify_opt_in_check_state( opt_in_checked ) {
	cy.get('button[name="add-to-cart"]').click()
	cy.get('a[href*="/cart/"]:first').click()
	cy.get('a[href*="/checkout/"]:first').click()
	cy.get('#customer_newsletter_opt_in').should( opt_in_checked ? 'have.attr' : 'not.have.attr', 'checked')
}

/**
 * Set the opt-in checked default in the admin area.
 *
 * @since 2019-03-20
 * @author Zach Owen <zach@webdevstudios.com>
 * @param {Boolean} opt_in_default_checked Whether the opt-in checkbox should be checked.
 */
function admin_set_opt_in_check_state( opt_in_default_checked ) {
	cy.fixture('contact-settings').then((data) => {
		let checkbox = '#' + data['Pre-select customer marketing sign-up at checkout'];
		cy.get( checkbox )[ opt_in_default_checked ? 'check' : 'uncheck' ]()
		cy.get( 'button[name="save"]' ).click().then(() => {
			cy.get('body').should('contain', 'Your settings have been saved')
			cy.get( checkbox ).should( opt_in_default_checked ? 'have.attr' : 'not.have.attr', 'checked')
		})
	})
}
