=== Constant Contact + WooCommerce ===
Contributors: constantcontact, webdevstudios, znowebdev, jmichaelward, ggwicz, ravedev
Tags: capture, contacts, constant contact, constant contact form, constant contact newsletter, constant contact official, contact forms, email, form, forms, marketing, mobile, newsletter, opt-in, plugin, signup, subscribe, subscription, widget
Requires at least: 5.2.2
Tested up to: 5.5.1
Stable tag: 1.3.3
Requires PHP: 7.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add products to your emails and sync your contacts.

== Description ==

Constant Contact is built to simplify the complex and confusing task of marketing your idea, even if you’re a beginner. And our award-winning team of marketing advisors is there for every customer, every step of the way, offering live, real-time marketing advice on the phone or online anytime you get stuck. Whether it’s creating great-looking email marketing campaigns, building an awesome website with ease, creating a beautiful logo for your brand, running Google Ads to get more website traffic, or finding new customers on social media, we’ve got all the tools, features, and expert guidance you need to help you succeed - all in one place.

**Unleash the power of your business — and drive more revenue — by integrating Constant Contact with WooCommerce today.**

https://youtu.be/YXWxySMcCsE

With Constant Contact you can:

* Easily connect WooCommerce to your Constant Contact account and sync contacts automatically.
* Drag and drop a product(s) from your WooCommerce catalog and insert into your email in seconds.
* Target the right customer/prospect with the right message with pre-defined segments based on your customers’ purchasing behavior:

 * All WooCommerce contacts who opt in to receive email emarketing
 * Recent Customers: Purchased within 30 days
 * First-time Customers: Purchased once
 * Repeat Customers: Made more than one purchase
 * Lapsed Customers: Have not made a purchase in more than 120 days
 * Prospects: Have not made a purchase yet
* Bring customers back to your online store with automated, targeted emails.
* Find new customers with our social marketing and advertising tools to expand your reach.
* Automatically send a customized branded abandoned cart email to customers who left items in their cart and track resulting revenue.

== Screenshots ==

1. Drag the WooCommerce action block directly into your email.
2. Edit the details of your product.
3. Pre-filtered segmented lists.
4. Syncing status for contacts.

== Frequently Asked Questions ==

#### Overall WooCommerce and Constant Contact Guide
[https://knowledgebase.constantcontact.com/guides/KnowledgeBase/34383-Guide-Constant-Contact-and-WooCommerce?q=woocommerce*&lang=en_US](https://knowledgebase.constantcontact.com/guides/KnowledgeBase/34383-Guide-Constant-Contact-and-WooCommerce?q=woocommerce*&lang=en_US)

#### How to Sync Contacts between WooCommerce and Constant Contact
[https://knowledgebase.constantcontact.com/articles/KnowledgeBase/33143-Sync-Contacts-Between-Your-WooCommerce-Store-and-Constant-Contact?q=woocommerce*&lang=en_US](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/33143-Sync-Contacts-Between-Your-WooCommerce-Store-and-Constant-Contact?q=woocommerce*&lang=en_US)

#### How to use the Product Block to insert WooCommerce Products to an Email
[https://knowledgebase.constantcontact.com/articles/KnowledgeBase/33144-Add-Products-from-a-WooCommerce-Store-to-an-Email?q=woocommerce*&lang=en_US](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/33144-Add-Products-from-a-WooCommerce-Store-to-an-Email?q=woocommerce*&lang=en_US)

#### How to Create an Automated Abandoned Cart Reminder Email for WooCommerce
Customers
[https://knowledgebase.constantcontact.com/articles/KnowledgeBase/36890-Create-an-Automated-Abandoned-Cart-Reminder-Email-for-WooCommerce-Customers?q=woocommerce*&lang=en_US](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/36890-Create-an-Automated-Abandoned-Cart-Reminder-Email-for-WooCommerce-Customers?q=woocommerce*&lang=en_US)

#### VIDEO Tutorial: Create an Automated WooCommerce Abandoned Cart Email
[https://knowledgebase.constantcontact.com/tutorials/KnowledgeBase/37409-Tutorial-Create-an-Automated-WooCommerce-Abandoned-Cart-Email?q=woocommerce*&lang=en_US](https://knowledgebase.constantcontact.com/tutorials/KnowledgeBase/37409-Tutorial-Create-an-Automated-WooCommerce-Abandoned-Cart-Email?q=woocommerce*&lang=en_US)

#### View WooCommerce Sales and Recovered Revenue Reporting
[https://knowledgebase.constantcontact.com/articles/KnowledgeBase/36892-View-Recovered-Revenue-from-the-WooCommerce-Abandoned-Cart-Reminder-Email?q=woocommerce*&lang=en_US](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/36892-View-Recovered-Revenue-from-the-WooCommerce-Abandoned-Cart-Reminder-Email?q=woocommerce*&lang=en_US)

== Changelog ==

= 1.3.3 =

* New - Adding minimum required WooCommerce version.
* Updated - Resolving various undefined index notices when WP_DEBUG is enabled.

= 1.3.2 =

* Updated - Add links to documentation in README Frequently Asked Questions.
* Updated - Use WooCommerce's WC_Validation::is_phone() for Phone Number setting validation.
* Updated - Use WooCommerce's wc_sanitize_phone_number() for Phone Number setting sanitization.

= 1.3.1 =

* Tweak - Change `CampaignId::save_user_campaign_id_to_order` and `NewsletterPreferenceCheckbox::save_user_preference_to_order` methods to fire on `woocommerce_checkout_create_order` hook.
* Tweak - Replace `add_post_meta` usage with `$order->update_meta_data` in `CampaignId::save_user_campaign_id_to_order` and `NewsletterPreferenceCheckbox::save_user_preference_to_order` methods.

= 1.3.0 =

* Updated - Revised abandoned cart functionality to instead be abandoned checkouts
* Fix - Fixed callback warning about WooTab and enqueue_scripts

= 1.2.0 =

* New - Introduced the "abandoned carts" feature, where abandoned carts are captured and stored in the database for further action.
* New — Introduced new authenticated REST API endpoints that list abandoned carts.

= 1.1.0 =

* New - Added Campaign ID data inclusion for purchased orders originating from your mailing campaigns.
* Fix - Fixed compatibility issue with phone numbers and PHP 7.3
* Tweak - Updated wording in our WooCommerce tab.
