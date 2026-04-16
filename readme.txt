=== Free shipping + Flat rate and hide other methods for WooCommerce ===
Contributors: nakedcatplugins, webdados
Donate link: https://paypal-me/wonderm00n
Tags: woocommerce, shipping, free shipping, flat rate
Requires at least: 5.8
Tested up to: 7.0
Requires PHP: 7.2
Stable tag: 2.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Hide other WooCommerce shipping methods per zone when Free Shipping or Flat Rate is active. Supports per-instance control and shipping class rules.

== Description ==

Automatically hide WooCommerce shipping methods per zone when Free Shipping or Flat Rate becomes available. Built as drop-in replacements for the native WooCommerce methods, with full per-instance control so each zone behaves exactly as you need.

Unlike global plugins that hide methods site-wide, this plugin lets you choose, per shipping method instance, which methods get hidden when that Free Shipping or Flat Rate kicks in. Multiple instances per zone are fully supported.

Both Free Shipping and Flat Rate include a “Flat/Free shipping requires... All products are in the same shipping class” option, letting you restrict activation to carts where every product belongs to a specific shipping class.
You can also restrict each method instance to specific user roles — including guest (non-logged-in) users — so the method only appears to the audiences you choose.
Header photo by [Drew Beamer](https://unsplash.com/photos/0wsnJWonXFs).

= Features: =

* Set a “Free Shipping” or a “Flat Rate” method
* Choose which shipping methods, on the same zone, should be hidden when this one is active
* Activate the method only if all the products in the cart belong to a specific shipping class
* Activate the "Flat Rate" method only for a minimum order amount
* Restrict any method instance to specific user roles, including guest (non-logged-in) users

= PRO add-on (soon to be released) features: =

* Tell us what would you like to see in a PRO add-on :-)
* Technical support

= Other (premium) plugins =

Already know our other WooCommerce (premium) plugins?

* [Advanced Coupon Restrictions for WooCommerce](https://nakedcatplugins.com/product/advanced-coupon-restrictions-for-woocommerce/) - Create coupons for any Product Taxonomy, User details, and Order destination.
* [Simple Custom Fields for WooCommerce Blocks Checkout](https://nakedcatplugins.com/product/simple-custom-fields-for-woocommerce-blocks-checkout/) - Add custom fields to the new WooCommerce Block-based Checkout
* [Simple WooCommerce Order Approval](https://nakedcatplugins.com/product/simple-woocommerce-order-approval/) - The hassle-free solution for WooCommerce order approval before payment
* [Shop as Client for WooCommerce](https://nakedcatplugins.com/product/shop-as-client-for-woocommerce-pro-add-on/) - Quickly create orders on behalf of your customers
* [Taxonomy/Term and Role based Discounts for WooCommerce](https://nakedcatplugins.com/product/taxonomy-term-and-role-based-discounts-for-woocommerce-pro-add-on/) - Easily create bulk discount rules for products based on any taxonomy terms (built-in or custom)
* [DPD / SEUR / Geopost Pickup and Lockers network for WooCommerce](https://nakedcatplugins.com/product/dpd-seur-geopost-pickup-and-lockers-network-for-woocommerce/) - Deliver your WooCommerce orders on the DPD and SEUR Pickup network of Parcelshops and Lockers in 21 European countries

== Frequently Asked Questions ==

= Is this plugin compatible with the new WooCommerce High-Performance Order Storage? =

Yes.

= Is this plugin compatible with the new WooCommerce block-based Cart and Checkout? =

Yes.

= I need help, can I get technical support? =

This is a free plugin. It’s our way of giving back to the wonderful WordPress community.

There’s a support tab on the top of this page, where you can ask the community for help. We’ll try to keep an eye on the forums but we cannot promise to answer support tickets.

If you reach us by email or any other direct contact method, we’ll assume you need premium, paid-for support.

= Where do I report security vulnerabilities found in this plugin? =  
 
You can report any security bugs found in the source code of this plugin through the [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/free-shipping-hide-other-methods-woo). The Patchstack team will assist you with verification, CVE assignment and take care of notifying the developers of this plugin.

= Can I contribute with a translation? =

Sure. Go to [GlotPress](https://translate.wordpress.org/projects/wp-plugins/free-shipping-hide-other-methods-woo) and help us out.

== Changelog ==

= 3.0 - 206-04-16 =
* [NEW] New “Flat Rate (hide other methods)” option: “Flat/Free shipping requires... A minimum order amount”
* [NEW] Restrict each shipping method to specific user roles, including guest users
* [NEW] Free plugin ownership transferred from [Marco Almeida | Webdados](https://profiles.wordpress.org/webdados/) to [Naked Cat Plugins](https://profiles.wordpress.org/nakedcatplugins/) on WordPress.org - No worries, we’re the same people :-)
* [TWEAK] Refactor plugin descriptions
* [DEV] Removed `load_plugin_textdomain` call as WordPress handles it
* [DEV] Tested up to WordPress 7.0-RC2-62241 and WooCommerce 10.7.0

= 2.4 - 2025-12-04 =
* [DEV] Stop using `wc_enqueue_js` which is deprecated from WooCommerce 10.4
* [FIX] Fields not correctly hidden on the new WooCommerce admin interface
* [DEV] Tested up to WordPress 7.0-alpha-61349 and WooCommerce 10.4.0-beta.2

= 2.3 - 2025-05-09 =
* [NEW] We are now called Naked Cat Plugins 😻
* [DEV] Requires PHP 7.2, WordPress 5.8 and WooCommerce 7.1
* [DEV] Tested up to WordPress 6.8 and WooCommerce 9.8.4

= 2.2 - 2023-12-13 =
* [FIX] Methods to hide on “Rest of the world”
* [DEV] Small code refactoring
* [DEV] Requires WordPress 5.8, WooCommerce 7.0 and PHP 7.2
* [DEV] Tested up to WordPress 6.7 and WooCommerce 9.4.1

= 2.1 - 2023-12-13 =
* Declare WooCommerce block-based Cart and Checkout compatibility
* Fix jQuery deprecation notices
* Requires WordPress 5.4
* Tested up to WordPress 6.5-alpha-57159 and WooCommerce 8.4.0

= 2.0 - 2023-10-16 =
* Name change to “Free shipping + Flat rate and hide other methods for WooCommerce”
* New “Flat Rate (hide other methods)” shipping method
* Fix a bug when more than one instance of the same method are available on the same shipping zone
* Tested up to WordPress 6.4-beta2-56809 and WooCommerce 8.2.0

= 1.2 =
* Remove the own shiping method from the list of methods to hide

= 1.1 - 2023-09-01 =
* Performance improvement
* Implement WordPress Coding Standards
* Tested up to WordPress 6.4-alpha-56479 and WooCommerce 6.8.0-beta.2

= 1.0 - 2023-06-05 =
* Fix php loop when adding this twice to the same zone
* High-Performance Order Storage compatible (in beta and only on WooCommerce 7.1 and above)
* Requires WooCommerce 5.0
* Tested up to WordPress 6.3-alpha-55859 and WooCommerce 6.8.0-beta.2

= 0.5.0 - 2022-06-29 =
* New brand: PT Woo Plugins 🥳
* Requires WordPress 5.0, WooCommerce 3.0 and PHP 7.0
* Tested up to WordPress 6.1-alpha-53556 and WooCommerce 6.7.0-beta.2

= 0.4.2 - 2021-03-11 =
* Tested up to WordPress 5.8-alpha-50516 and WooCommerce 5.1.0

= 0.4.1 =
* Fix one string textdomain

= 0.4 =
* Add a new “Free shipping requires...” option “All products are in the same shipping class” (sponsored by [Intergesso](https://www.intergesso.com/))

= 0.3 =
* Bugfix on admin javascript
* Fix two strings textdomain

= 0.2 =
* readme.txt fix

= 0.1 =
* Initial release (sponsored by [Planeta Tangerina](https://www.planetatangerina.com/))
* Tested up to WordPress 5.6-alpha-49035 and WooCommerce 4.6.0-beta.1
