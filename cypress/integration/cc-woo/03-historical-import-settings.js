describe('As a store owner on the CC Woo Settings page Historical Import section', function(){
	let url = '/wp-admin/admin.php?page=wc-settings&tab=cc_woo&section=customer_data_import'
	/**
	 * As a store owner
	 * When I visit the settings page
	 * I should see a checkbox to opt-in users to email marketing at checkout
	 */
	it('Sees a checkbox to opt-in users to email marketing at checkout', function(){
		cy.login(url)
		cy.get('#customer_marketing_email_opt_in_default').should('be.visible')
	})

	/**
	 * As a store owner
	 * When I visit the settings page
	 * I should see a drop-down indicating my preference to import historical customer data
	 */
	it('Sees a drop-down indicating my preference to import historical customer data', function(){
		cy.login(url)
		cy.get('#customer_marketing_allow_import').should('be.visible')
	})

	/**
	 * As a store owner
	 * When I visit the settings page
	 * I should see a checkbox that verifies I have permission to e-mail customers if the historical drop down is “Yes”
	 */
	it('Sees a a checkbox that verifies I have permission to e-mail customers if the historical drop down is “Yes”', function(){
		cy.login(url)
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

	/**
	 * As WordPress
	 * If a store owner selects "no" for importing historical data
	 * I should not display a checkbox to give them the option to say they have permission to email customers
	 * And I should set the permissions value to false.
	 */
	it('Does not see a consent checkbox if the import option is "No"', function(){
	})
})
