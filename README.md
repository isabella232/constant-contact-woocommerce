# Constant Contact ❤️ WooCommerce

## Testing

### Cypress.io

This plugin uses [Cypress.io](https://cypress.io/) for end-to-end testing.

#### Configuration

To set local environment variables, create a file named `./cypress.env.json`.
These values append to, or overwrite, the values set in the `env` key
in `cypress.json`. Sample:

```json
{
  "admin_user": "myadminuser",
  "some_var": "foo"
}
```

The above will override `env.admin_user`, and create a new environment variable
called `env.some_var`, with a value of `foo`.

##### Variables

You will need to configure the following variables for your environment.

- `admin_user` The WordPress admin user.
- `admin_pass` Password for the admin user.
- `customer_user` A WooCommerce customer account user.
- `customer_pass` The password for the customer account.
- `test_product_url` A frontend URL to a product to test with during checkout.

#### Running Cypress

In your terminal:

```sh
npm i
$(npm bin)/cypress open
```
