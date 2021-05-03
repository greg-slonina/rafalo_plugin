=== PW WooCommerce Gift Cards ===
Contributors: pimwick
Donate link: https://paypal.me/pimwick
Tags: woocommerce, gift cards, gift certificates, pimwick
Requires at least: 4.5
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: 1.177
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sell gift cards to your WooCommerce store, in just a few minutes!

== Description ==

**Your WooCommerce store should offer gift cards!**

Gift Cards are convenient and increase sales organically. the WooCommerce Gift Cards plugin makes it easy to sell gift cards to your store. So easy to get started, you can be selling gift cards for your WooCommerce store in 5 minutes!

**Purchasing** Similar to Amazon.com gift cards, the customer can specify the amount, recipient, and message when purchasing.

**Receiving** WooCommerce email template system for beautiful emails. Click the link directly in the email to add the gift card to the cart automatically!

**Redeeming** Integrates into your theme to make redeeming a gift card easy for the customer. Applies the balance after tax, just like cash. New balance shown on the cart and checkout pages.

**Guest Checkout** Gift cards are not tied to a specific account so guests can receive gift cards without having to create an account.

**Compatible with WooCommerce Pre-Orders** If you use the WooCommerce Pre-Orders plugin from WooCommerce.com.

**Setup is easy!** One-click creation of the Gift Card product. Easily customized to suit your needs.

**Gift Card Admin** See your gift card liability at a glance. View details about individual cards.


> **<a href="https://www.pimwick.com/gift-cards/">PW WooCommerce Gift Cards Pro</a> lets you do more:**
>
> * **Set Custom Amounts** - Allow customers to specify the amount. You can set a minimum and a maximum amount.
> * **Schedule delivery** - Optionally allow customers to schedule when a gift card will be delivered.
> * **Specify a Default Amount** - Choose an amount that will be pre-selected when purchasing a gift card.
> * **Customer-facing Balance Page** - A shortcode to let customers check their gift card balances.
> * **Adding funds to existing gift card** - Customers can add funds to existing gift cards from the Check Balance page.
> * **Expiration Dates** - Automatically set an expiration date based on the purchase date.
> * **Balance Adjustments** - Perform balance adjustments in the admin area.
> * **Sell Physical Gift Cards** - Import existing gift card numbers and balances.
> * **Manually Generate Gift Cards** - Specify the amount and quantity for the cards to create multiple cards in one step.
> * **REST API** - Adheres to the WordPress and WooCommerce REST API standards.

Compatible with WooCommerce 3.0 and higher.

Available in the following languages:
* Arabic
* Danish
* Dutch
* English
* Finnish
* French
* Galician
* German
* Italian
* Portuguese
* Romanian
* Russian
* Spanish
* Swedish

The following currency switcher plugins are supported:
* Aelia Currency Switcher
* WooCommerce Currency Switcher by realmag777
* WPML WooCommerce Multi-currency by OnTheGoSystems
* Multi Currency for WooCommerce by VillaTheme
* WooCommerce Ultimate Multi Currency Suite by Dev49.net (requires a patch, contact us for details)
* Polylang + Hyyan WooCommerce Polylang Integration

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/pw-gift-cards` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to Pimwick Plugins -> PW Gift Cards

== Screenshots ==

1. Similar to Amazon.com gift cards, the customer can specify the amount, recipient, and message when purchasing.
2. WooCommerce email template system for beautiful emails. Click the link directly in the email to add the gift card to the cart automatically!
3. Use the email designer to customize your gift card.
4. Integrates into your theme to make redeeming a gift card easy for the customer. Applies the balance after tax, just like cash. New balance shown on the cart and checkout pages.
5. One-click creation of the Gift Card product. Easily customized to suit your needs.
6. See your gift card liability at a glance. View details about individual cards.

== Changelog ==

= 1.177 =
* Fixed a possible fatal error during the Checkout process when the Order object is not of the correct type.

= 1.176 =
* Confirmed compatibility with WordPress 5.7 and WooCommerce 5.1

= 1.175 =
* Added the ability to disable Swatches for the gift card product when using the Variation Swatches For WooCommerce plugin by Emran Ahmed. To disable Swatches for the gift card product only, define the PWGC_WVS_ALLOW constant in your wp-config.php file and set the value to false.

= 1.174 =
* Show the From field in the body of the email in case the WooCommerce Email Header has been turned off in settings.

= 1.173 =
* Internal change to the get_original_order_item_id method to return 0 instead of -1 when the order ID is not found.

= 1.172 =
* Improved integration with WPML WooCommerce Multi Currency.

= 1.171 =
* Confirmed compatibility with WooCommerce 5.0. Small bug fix for getting the existing gift card product.

= 1.170 =
* Fixed an issue with the color picker in the Email Designer on some systems.

= 1.169 =
* Fixed an issue with integration for Currency Switcher for WooCommerce by WP Wham.

= 1.168 =
* Fixed compatibility with the MPesa For WooCommerce plugin by Osen Concepts Kenya. Fixed an issue with integration for Currency Switcher for WooCommerce by WP Wham.

= Previous versions =
* See changelog.txt

== Upgrade Notice ==

= 1.177 =
* Fixed a possible fatal error during the Checkout process when the Order object is not of the correct type.


