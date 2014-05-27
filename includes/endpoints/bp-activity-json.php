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


    /**
     * register_routes function.
     * 
     * @access public
     * @param mixed $routes
     * @return void
     */
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
    
    
    /**
     * get_activities function.
     * 
     * @access public
     * @return void
     */
    public function get_activities() {
		global $bp;
		
		$activities = array();
				
		if ( bp_has_activities( $arg ) ) {
		
			while ( bp_activities() ) {
				
				bp_the_activity();
										
				$activity = array( 
					'avatar' 			=> bp_core_fetch_avatar( array( 'html' => false, 'item_id' => bp_get_activity_id() ) ),
					'action' 			=> bp_get_activity_action(),
					'content' 			=> bp_get_activity_content_body(),
					'activity_id'		=> bp_get_activity_id(),
					'activity_username'	=> bp_core_get_username( bp_get_activity_user_id() ),
					'user_id'			=> bp_get_activity_user_id(),
					'comment_count' 	=> bp_activity_get_comment_count(),
					'can_comment' 		=> bp_activity_can_comment(),
					'can_favorite' 		=> bp_activity_can_favorite(),
					'is_favorite' 		=> bp_get_activity_is_favorite(),
					'can_delete'		=> bp_activity_user_can_delete()				
				);
											
				$activities[] =  $activity;
				
				$response = array(
					'activity' => $activities,
					'more_activity' => bp_activity_has_more_items()
				);

			}
			
			return wp_send_json( $response );
		} else {
			return wp_send_json_error();
		}
				

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