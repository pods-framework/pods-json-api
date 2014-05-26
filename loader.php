<?php
/*
Plugin Name: BuddyPress json API
Plugin URI: https://github.com/modemlooper/buddypress-json-api
Description: json API for BuddyPress. This plugin creates json api endpoints for https://github.com/WP-API
Author: modemlooper
Version: 0.1
Author URI: http://twitter.com/modemlooper
*/

// Define a constant that can be checked to see if the component is installed or not.
define( 'BP_API_IS_INSTALLED', 1 );

// Define a constant that will hold the current version number of the component
// This can be useful if you need to run update scripts or do compatibility checks in the future
define( 'BP_API_VERSION', '0.1' );

// Define a constant that we can use to construct file paths throughout the component
define( 'BP_API_PLUGIN_DIR', dirname( __FILE__ ) );

// is BuddyPress plugin active? If not, throw a notice and deactivate
if ( ! in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_action( 'all_admin_notices', 'bp_api_buddypress_required' );
	return;
}

/**
 * bp_api_init function.
 * 
 * @access public
 * @return void
 */
function bp_api_init() {
	// requires BP 2.0 or greater.
	if ( version_compare( BP_VERSION, '2.0', '>' ) )
		require( dirname( __FILE__ ) . '/includes/bp-api-loader.php' );
}
add_action( 'bp_include', 'bp_api_init' );


/**
 * bp_api_activate function.
 * 
 * @access public
 * @return void
 */
function bp_api_activate() {
}
register_activation_hook( __FILE__, 'bp_api_activate' );


/**
 * bp_api_deactivate function.
 * 
 * @access public
 * @return void
 */
function bp_api_deactivate() {
}
register_deactivation_hook( __FILE__, 'bp_api_deactivate' );


/**
 * bp_api_buddypress_required function.
 * 
 * @access public
 * @return void
 */
function bp_api_buddypress_required() {
	echo '<div id="message" class="error"><p>'. sprintf( __( '%1$s requires the BuddyPress plugin to be installed/activated. %1$s has been deactivated.', 'appbuddy' ), 'BuddyPress json API' ) .'</p></div>';
	deactivate_plugins( plugin_basename( __FILE__ ), true );
}