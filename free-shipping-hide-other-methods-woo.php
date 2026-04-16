<?php
/**
 * Plugin Name:          Free shipping + Flat rate and hide other methods for WooCommerce
 * Plugin URI:
 * Description:          Alternative WooCommerce “Free Shipping” and “Flat Rate” methods that allows the shop owner to select and make unavailable any other shipping methods on the same zone when this one is available
 * Version:              2.4
 * Author:               Naked Cat Plugins (by Webdados)
 * Author URI:           https://nakedcatplugins.com
 * Text Domain:          free-shipping-hide-other-methods-woo
 * Requires at least:    5.8
 * Tested up to:         6.8
 * Requires PHP:         7.2
 * WC requires at least: 7.1
 * WC tested up to:      10.4
 * Requires Plugins:     woocommerce
 * License:              GPLv3
 **/

/* WooCommerce CRUD ready */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Localization
 */
function free_shipping_hide_others_load_textdomain() {
	load_plugin_textdomain( 'free-shipping-hide-other-methods-woo' );
}
add_action( 'plugins_loaded', 'free_shipping_hide_others_load_textdomain', 0 );

/**
 * Init
 */
function fsho_init() {
	if ( class_exists( 'WooCommerce' ) && version_compare( WC_VERSION, '7.1', '>=' ) ) {
		require_once __DIR__ . '/includes/class-ptwooplugins-fsho.php';
		$GLOBALS['PTWooPlugins_FSHO'] = PTWooPlugins_FSHO();
	}
}
add_action( 'woocommerce_shipping_init', 'fsho_init' );

/**
 * Main class
 */
function PTWooPlugins_FSHO() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return PTWooPlugins_FSHO::instance();
}

/* HPOS Compatible */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);

/* If you're reading this you must know what you're doing ;-) Greetings from sunny Portugal! */
