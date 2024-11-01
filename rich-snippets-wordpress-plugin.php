<?php
/*
Plugin Name: snip - Structured Data & Schema
Plugin URI: https://rich-snippets.io?pk_campaign=snip-plugin-uri
Description: Allows to create Rich Snippets and general structured data readable by search engines.
Version: 2.31.6
Author: floriansimeth
Author URI: https://florian-simeth.de?pk_campaign=snip-author-uri
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: rich-snippets-schema
Domain Path: /languages
Requires PHP: 8.0.0
Requires at least: 5.8.0
NotActiveWarning: Your copy of the Rich Snippets Plugin has not yet been activated.
ActivateNow: Activate it now.
Active: Your copy is active.
Update URI: https://updates.rich-snippets.io/latest.json

Copyright 2012-2023  WP-Buddy  (email : support@wp-buddy.com)
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'WPB_RS_FILE', __FILE__ );

/**
 *
 * PHP Version check.
 *
 */
if ( ! call_user_func( function () {
	if ( version_compare( PHP_VERSION, '8.0.0', '<' ) ) {
		add_action( 'admin_notices', 'wpb_rs_old_php_notice' );

		function wpb_rs_old_php_notice() {

			printf(
				'<div class="notice error"><p>%s</p></div>',
				sprintf(
					__( 'Hey mate! Sorry for interrupting you. It seem\'s that you\'re using an old PHP version (your current version is %s). You should upgrade to at least %s or higher in order to use SNIP. Operating the plugin in older versions is not guaranteed and may lead to unforeseen errors. Thank you!', 'rich-snippets-schema' ),
					esc_html( PHP_VERSION ),
					'8.0'
				)
			);
		}

		# sorry. The plugin will not work with an old PHP version.
		if ( version_compare( PHP_VERSION, '8.0.0', '<' ) ) {
			return false;
		}
	}

	global $wp_version;

	if ( version_compare( $wp_version, '5.0.0', '<' ) ) {
		add_action( 'admin_notices', 'wpb_rs_old_php_notice' );

		function wpb_rs_old_php_notice() {
			global $wp_version;

			printf(
				'<div class="notice error"><p>%s</p></div>',
				sprintf(
					__( 'Hey mate! Sorry for interrupting you. It seem\'s that you\'re using an old WordPress version (your current version is %s). You should upgrade to at least %s or higher in order to use SNIP. Thank you!', 'rich-snippets-schema' ),
					esc_html( $wp_version ),
					'5.0.0'
				)
			);
		}

		return false;
	}

	if ( function_exists( 'rich_snippets' ) ) {
		add_action( 'admin_notices', 'wpb_rs_already_exists' );

		function wpb_rs_already_exists() {
			printf(
				'<div class="notice error"><p>%s</p></div>',
				__( 'Hey mate! Sorry for interrupting you. It seem\'s that another version of SNIP is already installed and active. Make sure only one version is active.', 'rich-snippets-schema' )
			);
		}

		return false;
	}

	return true;
} ) ) {
	return;
}


/**
 *
 * WP Version check.
 *
 */
if ( version_compare( get_bloginfo( 'version' ), '4.6', '<' ) ) {
	add_action( 'admin_notices', 'wpb_rss_old_php_notice' );

	function wpb_rss_old_php_notice() {

		printf(
			'<div class="notice error"><p>%s</p></div>',
			sprintf(
				__( 'Hey mate! Sorry for interrupting you. It seem\'s that you\'re using an old version WordPress (your current version is %s). You should upgrade to at least 4.6 or higher in order to use the Rich Snippets plugin. Thank you!', 'rich-snippets-schema' ),
				esc_html( get_bloginfo( 'version' ) )
			)
		);
	}

	# sorry. The plugin will not work with an old WP version.
	return;
}


/**
 *
 *
 * Bootstrapping
 *
 */
require_once( __DIR__ . '/bootstrap.php' );
