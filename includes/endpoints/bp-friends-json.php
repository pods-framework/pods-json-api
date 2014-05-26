<?php
/**
 * BuddyPress API friends.
 *
 * friends api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_friends_init function.
 *
 * initializes api class for friends and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_friends_init() {
    global $bp_api_friends;

    $bp_api_friends = new BP_API_Friends();
    add_filter( 'json_endpoints', array( $bp_api_friends, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_friends_init' );


/**
 * BP_API_friends class.
 */
class BP_API_Friends {

    public function register_routes( $routes ) {
        $routes['/buddypress/friends'] = array(
            array( array( $this, 'get_friends'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_friend'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/friends/(?P<id>\d+)'] = array(
            array( array( $this, 'get_friend'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_friend'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_friend'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_friends() {
	    return 'get friends';
    }
    
    public function get_friend() {
	    return 'get friend';
    }

    public function create_friend() {
	    return 'create friend';
    }    

    public function edit_friend() {
	    return 'edit friend';
    }   
    
    public function delete_friend() {
	    return 'delete friend';
    }    
    
}