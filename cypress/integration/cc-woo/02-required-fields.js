/**
 * As a store owner
 * When I click on the Constant Contact tab in the WooCommerce Settings
 * I should be presented with a required text field for both first name and last name
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Phone Number
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Store Name
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Currency
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Country Code
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Contact Email Address
 */

function test_required( name, element ) {
	it('Sees a required ' + name + ' field', function() {
		cy.login(Cypress.env('cc_woo_url'))
		cy.get('body').should('contain', name)
		cy.get( element ).should('have.attr', 'required')
	})
}

describe('As a Store Owner on the CC Woo Settings Page', function(){
	test_required( 'First Name', '#store_information_first_name' )
	test_required( 'Last Name', '#store_information_last_name' )
	test_required( 'Phone Number', '#store_information_phone_number' )
	test_required( 'Store Name', '#store_information_store_name' )
	test_required( 'Country Code', '#store_information_country_code' )
	test_required( 'Contact E-mail Address', '#store_information_contact_email' )
})

