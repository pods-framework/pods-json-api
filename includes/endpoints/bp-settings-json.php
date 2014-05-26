<?php
/**
 * BuddyPress API settings.
 *
 * settings api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_settings_init function.
 *
 * initializes api class for settings and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_settings_init() {
    global $bp_api_settings;

    $bp_api_settings = new BP_API_Settings();
    add_filter( 'json_endpoints', array( $bp_api_settings, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_settings_init' );


/**
 * BP_API_settings class.
 */
class BP_API_Settings {

    public function register_routes( $routes ) {
        $routes['/buddypress/settings'] = array(
            array( array( $this, 'get_settings'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_setting'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/settings/(?P<id>\d+)'] = array(
            array( array( $this, 'get_setting'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_setting'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_setting'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_settings() {
	    return 'get settings';
    }
    
    public function get_setting() {
	    return 'get setting';
    }

    public function create_setting() {
	    return 'create setting';
    }    

    public function edit_setting() {
	    return 'edit setting';
    }   
    
    public function delete_setting() {
	    return 'delete setting';
    }    
    
}