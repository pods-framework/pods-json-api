<?php
/**
 * BuddyPress API core.
 *
 * core api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_core_init function.
 *
 * initializes api class for core and creates endpoint. Returns basic info about BuddyPress install.
 * 
 * @access public
 * @return void
 */
function bp_api_core_init() {
    global $bp_api_core;

    $bp_api_core = new BP_API_Core();
    add_filter( 'json_endpoints', array( $bp_api_core, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_core_init' );


/**
 * BP_API_Core class.
 */
class BP_API_Core{


    /**
     * register_routes function.
     * 
     * @access public
     * @param mixed $routes
     * @return void
     */
    public function register_routes( $routes ) {
        $routes['/buddypress'] = array(
            array( array( $this, 'get_core'), WP_JSON_Server::READABLE ),
        );

        return $routes;
    }
    
    
    /**
     * get_core function.
     * 
     * @access public
     * @return json
     */
    public function get_core() {
    	global $bp;
    
    	$response = array();
    	
    	$response['version'] = $bp->version;
    	$response['active_components'] = $bp->active_components;
    	$response['directory_page_ids'] = bp_core_get_directory_page_ids();
    	
	    return wp_send_json( $response );
    }
      
    
}