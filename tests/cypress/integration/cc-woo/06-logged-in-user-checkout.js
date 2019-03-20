/**
 * Setup function: This sets the store's default opt-in to unchecked.
 */
describe('As a store owner', function() {
	it( 'Should not have the opt-in default checked', function(){
		cy.adminLogin(Cypress.env('cc_woo_url')).then(() => {
			cy.setAdminOptInCheckState( false )
		})
	})
})

run_test_suite()

/**
 * Setup function: This sets the store's default opt-in to checked.
 */
describe('As a store owner', function() {
	it( 'Should have the opt-in default checked', function(){
		cy.adminLogin(Cypress.env('cc_woo_url')).then(() => {
			cy.setAdminOptInCheckState( true )
		})
	})
})

run_test_suite()

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
				cy.doCustomerCheckout( false )
			})
		})
	})

	describe('As a logged-in customer visiting the checkout page', function(){
		it('Should checkout and see that the opt-in checkbox is still unchecked', function() {
			cy.customerLogin(Cypress.env('test_product_url')).then(() => {
				cy.verifyOptInCheckState( false )
			})
		})
	})

	describe('As a logged-in customer visiting the checkout page', function(){
		it('Should checkout with the opt-out checkbox checked', function() {
			cy.customerLogin(Cypress.env('test_product_url')).then(() => {
				cy.doCustomerCheckout( true )
			})
		})
	})

	describe('As a logged-in customer visiting the checkout page', function(){
		it('Should checkout with a product and see that the checkbox for opt-in is still checked from previous selection', function() {
			cy.customerLogin(Cypress.env('test_product_url')).then(() => {
				cy.verifyOptInCheckState( true )
			})
		})
	})
}
