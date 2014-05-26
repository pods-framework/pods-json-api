<?php
/**
 * BuddyPress API members.
 *
 * members api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_members_init function.
 *
 * initializes api class for members and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_members_init() {
    global $bp_api_members;

    $bp_api_members = new BP_API_Members();
    add_filter( 'json_endpoints', array( $bp_api_members, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_members_init' );


/**
 * BP_API_members class.
 */
class BP_API_Members {

    public function register_routes( $routes ) {
        $routes['/buddypress/members'] = array(
            array( array( $this, 'get_members'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_member'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/members/(?P<id>\d+)'] = array(
            array( array( $this, 'get_member'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_member'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_member'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_members() {
	    return 'get members';
    }
    
    public function get_member() {
	    return 'get member';
    }

    public function create_member() {
	    return 'create member';
    }    

    public function edit_member() {
	    return 'edit member';
    }   
    
    public function delete_member() {
	    return 'delete member';
    }    
    
}