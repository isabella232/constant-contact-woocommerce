/**
As a store owner
When I visit the settings page
I should see a checkbox to opt-in users to email marketing at checkout

As a store owner
When I visit the settings page
I should see a drop-down indicating my preference to import historical customer data

As a store owner
When I visit the settings page
I should see a checkbox that verifies I have permission to e-mail customers if the historical drop down is “Yes”
*/

describe('As a store owner on the CC Woo Settings page Historical Import section', function(){
	it('Sees a checkbox to opt-in users to email marketing at checkout', function(){
		cy.login('/wp-admin/admin.php?page=wc-settings&tab=cc_woo&section=customer_data_import')
		cy.get('#customer_marketing_email_opt_in_default').should('be.visible')
	})

	it('Sees a drop-down indicating my preference to import historical customer data', function(){
		cy.login('/wp-admin/admin.php?page=wc-settings&tab=cc_woo&section=customer_data_import')
		cy.get('#customer_marketing_allow_import').should('be.visible')
	})

	it('Sees a a checkbox that verifies I have permission to e-mail customers if the historical drop down is “Yes”', function(){
		cy.login('/wp-admin/admin.php?page=wc-settings&tab=cc_woo&section=customer_data_import')
		cy.get('#customer_marketing_allow_import').then(($select) => {
			if ( 'yes' === $select.val() ) {
				cy.get('#customer_marketing_opt_in_consent').should('be.visible')
			} else {
				$select.val('yes')
				cy.get('button[name="save"]').click().then(() => {
					cy.get('#customer_marketing_opt_in_consent').should('be.visible')
				})
			}
		})
	})
})