<?php
/**
 * BuddyPress API xprofile.
 *
 * xprofile api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_xprofile_init function.
 *
 * initializes api class for xprofile and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_xprofile_init() {
    global $bp_api_xprofile;

    $bp_api_xprofile = new BP_API_Xprofile();
    add_filter( 'json_endpoints', array( $bp_api_xprofile, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_xprofile_init' );


/**
 * BP_API_Xprofile class.
 */
class BP_API_Xprofile {

    public function register_routes( $routes ) {
        $routes['/buddypress/xprofile'] = array(
            array( array( $this, 'get_xprofile'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_field'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/xprofile/(?P<id>\d+)'] = array(
            array( array( $this, 'get_field'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_field'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_field'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_xprofile() {
	    return 'get xprofile';
    }
    
    public function get_field() {
	    return 'get field';
    }

    public function create_field() {
	    return 'create field';
    }    

    public function edit_field() {
	    return 'edit field';
    }   
    
    public function delete_field() {
	    return 'delete field';
    }    
    
}