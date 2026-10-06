<?php
/**
 * Plugin Name: eBay Product Importer
 * Plugin URI: https://woocommerce.com/products/ebay-product-importer/
 * Description: Import product in your woocommerce shop directly from any ebay marketplace websites by the extension in just one click and sale the imported product as yours or as an affiliate.
 * Version: 5.4.0
 * Author: Nxtal
 * Author URI: https://woocommerce.com/vendor/nxtal/
 * Copyright: © 2026 Nxtal.
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: ebay-product-importer
 * Requires at least: 5.6
 * Tested up to: 7.1
 * WC tested up to: 11.1.0
 * WC requires at least: 5.6
 * Requires Plugins: woocommerce
 * Woo: 6399555:b007efc4aa19974cd5684523cc6d0076

 */
 
// don't call the file directly

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}


if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

//Check if WooCommerce is active
if (is_plugin_active('woocommerce/woocommerce.php')) {
	
	// Load Composer autoloader
	require_once __DIR__ . '/vendor/autoload.php';

	require_once __DIR__ . '/classes/class-init.php';
}
