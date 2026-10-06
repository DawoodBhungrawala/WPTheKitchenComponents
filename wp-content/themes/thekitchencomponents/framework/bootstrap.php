<?php

/**
 * If we're using Cloudflare, restore the users connecting IP and specify that SSL is on
 */
if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    $_SERVER['HTTPS'] = 'on';
}

/**
 * Removes WP generator
 */
remove_action('wp_head', 'wp_generator');



/**
 * Removes Yoast SEO comments
 */
add_filter('wpseo_debug_markers', '__return_false');

/**
 * Disables XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Removes the auto paragraph
 */
add_filter('wpcf7_autop_or_not', '__return_false');
