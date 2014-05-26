<?php
/**
 * BuddyPress API Activity.
 *
 * Activity api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_activity_init function.
 *
 * initializes api class for activity and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_activity_init() {
    global $bp_api_activity;

    $bp_api_activity = new BP_API_Activity();
    add_filter( 'json_endpoints', array( $bp_api_activity, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_activity_init' );


/**
 * BP_API_Activity class.
 */
class BP_API_Activity {

    public function register_routes( $routes ) {
        $routes['/buddypress/activity'] = array(
            array( array( $this, 'get_activities'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_activity'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/activity/(?P<id>\d+)'] = array(
            array( array( $this, 'get_activity'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_activity'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_activity'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_activities() {
	    return 'get activities';
    }
    
    public function get_activity() {
	    return 'get activity';
    }

    public function create_activity() {
	    return 'create activity';
    }    

    public function edit_activity() {
	    return 'edit activity';
    }   
    
    public function delete_activity() {
	    return 'delete activity';
    }    
    
}