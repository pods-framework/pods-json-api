<?php
/**
 * BuddyPress API notifications.
 *
 * notifications api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_notifications_init function.
 *
 * initializes api class for notifications and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_notifications_init() {
    global $bp_api_notifications;

    $bp_api_notifications = new BP_API_Notifications();
    add_filter( 'json_endpoints', array( $bp_api_notifications, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_notifications_init' );


/**
 * BP_API_Notifications class.
 */
class BP_API_notifications {

    public function register_routes( $routes ) {
        $routes['/buddypress/notifications'] = array(
            array( array( $this, 'get_notifications'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_notification'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/notifications/(?P<id>\d+)'] = array(
            array( array( $this, 'get_notification'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_notification'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_notification'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_notifications() {
	    return 'get notifications';
    }
    
    public function get_notification() {
	    return 'get notification';
    }

    public function create_notification() {
	    return 'create notification';
    }    

    public function edit_notification() {
	    return 'edit notification';
    }   
    
    public function delete_notification() {
	    return 'delete notification';
    }    
    
}