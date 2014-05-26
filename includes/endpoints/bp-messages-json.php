<?php
/**
 * BuddyPress API messages.
 *
 * messages api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_messages_init function.
 *
 * initializes api class for messages and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_messages_init() {
    global $bp_api_messages;

    $bp_api_messages = new BP_API_Messages();
    add_filter( 'json_endpoints', array( $bp_api_messages, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_messages_init' );


/**
 * BP_API_Messages class.
 */
class BP_API_Messages {

    public function register_routes( $routes ) {
        $routes['/buddypress/messages'] = array(
            array( array( $this, 'get_messages'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_message'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/messages/(?P<id>\d+)'] = array(
            array( array( $this, 'get_message'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_message'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_message'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_messages() {
	    return 'get messages';
    }
    
    public function get_message() {
	    return 'get message';
    }

    public function create_message() {
	    return 'create message';
    }    

    public function edit_message() {
	    return 'edit message';
    }   
    
    public function delete_message() {
	    return 'delete message';
    }    
    
}