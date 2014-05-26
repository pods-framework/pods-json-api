<?php
/**
 * BuddyPress API Forums.
 *
 * Forums api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_forums_init function.
 *
 * initializes api class for forums and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_forums_init() {
    global $bp_api_forums;

    $bp_api_forums = new BP_API_Forums();
    add_filter( 'json_endpoints', array( $bp_api_forums, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_forums_init' );


/**
 * BP_API_Forums class.
 */
class BP_API_Forums {

    public function register_routes( $routes ) {
        $routes['/buddypress/forums'] = array(
            array( array( $this, 'get_forums'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_forum'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/forums/(?P<id>\d+)'] = array(
            array( array( $this, 'get_forum'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_forum'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_forum'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_forums() {
	    return 'get forums';
    }
    
    public function get_forum() {
	    return 'get forum';
    }

    public function create_forum() {
	    return 'create forum';
    }    

    public function edit_forum() {
	    return 'edit forum';
    }   
    
    public function delete_forum() {
	    return 'delete forum';
    }    
    
}