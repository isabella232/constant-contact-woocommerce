/**
 * When I visit the settings page
 * I should see a button that reads "Connect with Constant Contact"
 */

describe('As a Store Owner on the CC Woo Settings Page', function(){
	it( 'a button that reads "Connect with Constant Contact"', function(){
		cy.login(Cypress.env('cc_woo_url'))
		cy.get('body').then(($body) => {
			if ( $body.text().match(/Connect with Constant Contact/) ) {
				return
			}

			cy.get( '#store_information_first_name' ).clear().type('Fred')
			cy.get( '#store_information_last_name' ).clear().type('Jones')
			cy.get( '#store_information_phone_number' ).clear().type('5555555555')
			cy.get( '#store_information_store_name' ).clear().type('ACME LLC')
			cy.get( '#store_information_country_code' ).clear().type('US')
			cy.get( '#store_information_contact_email' ).clear().type('fred.jones@lab.local')
			cy.get('button[name="save"]').click()
		})
		cy.get('body').should('contain', 'Connect with Constant Contact')
	} )
})

