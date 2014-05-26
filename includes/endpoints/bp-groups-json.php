<?php
/**
 * BuddyPress API groups.
 *
 * groups api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_groups_init function.
 *
 * initializes api class for groups and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_groups_init() {
    global $bp_api_groups;

    $bp_api_groups = new BP_API_Groups();
    add_filter( 'json_endpoints', array( $bp_api_groups, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_groups_init' );


/**
 * BP_API_groups class.
 */
class BP_API_Groups {

    public function register_routes( $routes ) {
        $routes['/buddypress/groups'] = array(
            array( array( $this, 'get_groups'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_group'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/groups/(?P<id>\d+)'] = array(
            array( array( $this, 'get_group'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_group'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_group'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_groups() {
	    return 'get groups';
    }
    
    public function get_group() {
	    return 'get group';
    }

    public function create_group() {
	    return 'create group';
    }    

    public function edit_group() {
	    return 'edit group';
    }   
    
    public function delete_group() {
	    return 'delete group';
    }    
    
}